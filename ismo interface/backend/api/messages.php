<?php
declare(strict_types=1);
/**
 * ISMO-SkillSwap — Messaging API
 * GET  /api/messages.php                — list user's conversations
 * GET  /api/messages.php?conversation=1  — get messages in a conversation
 * GET  /api/messages.php?unread=1        — get unread count
 * POST /api/messages.php                 — send message (to existing conversation or find/create one)
 * POST /api/messages.php?action=start    — start a new conversation
 */
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$db = Database::getInstance();
$userId = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$convId = (int)($_GET['conversation'] ?? 0);

switch ($method) {
    case 'GET':
        if (!empty($_GET['unread'])) {
            $stmt = $db->prepare('
                SELECT COUNT(*) FROM messages m
                JOIN conversation_participants cp ON m.conversation_id = cp.conversation_id
                WHERE cp.user_id = ? AND m.sender_id != ? AND m.is_read = 0
            ');
            $stmt->execute([$userId, $userId]);
            jsonResponse(['count' => (int)$stmt->fetchColumn()]);
        }

        if ($convId) {
            $isParticipant = $db->prepare('SELECT 1 FROM conversation_participants WHERE conversation_id = ? AND user_id = ?');
            $isParticipant->execute([$convId, $userId]);
            if (!$isParticipant->fetch()) jsonResponse(['error' => 'Conversation introuvable'], 404);

            $stmt = $db->prepare('SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC');
            $stmt->execute([$convId]);
            $messages = $stmt->fetchAll();

            $db->prepare('UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ? AND is_read = 0')
               ->execute([$convId, $userId]);

            $db->prepare('UPDATE conversation_participants SET last_read_at = NOW() WHERE conversation_id = ? AND user_id = ?')
               ->execute([$convId, $userId]);

            $partStmt = $db->prepare('
                SELECT u.id AS user_id, u.prenom AS first_name, u.nom AS last_name, u.role, u.photo AS profile_picture_url
                FROM conversation_participants cp
                JOIN utilisateurs u ON cp.user_id = u.id
                WHERE cp.conversation_id = ?
            ');
            $partStmt->execute([$convId]);
            $participants = $partStmt->fetchAll();

            $convStmt = $db->prepare('SELECT * FROM conversations WHERE conversation_id = ?');
            $convStmt->execute([$convId]);
            $conversation = $convStmt->fetch();

            jsonResponse([
                'conversation' => $conversation,
                'participants' => $participants,
                'messages' => $messages
            ]);
        }

        $stmt = $db->prepare('
            SELECT
                c.conversation_id AS id,
                c.subject,
                c.related_post_id,
                c.updated_at AS timestamp,
                (
                    SELECT m.message_text FROM messages m
                    WHERE m.conversation_id = c.conversation_id
                    ORDER BY m.created_at DESC LIMIT 1
                ) AS lastMessage,
                (
                    SELECT COUNT(*) FROM messages m
                    WHERE m.conversation_id = c.conversation_id AND m.sender_id != ? AND m.is_read = 0
                ) AS unread,
                (
                    SELECT u.prenom FROM conversation_participants cp2
                    JOIN utilisateurs u ON cp2.user_id = u.id
                    WHERE cp2.conversation_id = c.conversation_id AND cp2.user_id != ?
                    LIMIT 1
                ) AS other_first_name,
                (
                    SELECT u.nom FROM conversation_participants cp2
                    JOIN utilisateurs u ON cp2.user_id = u.id
                    WHERE cp2.conversation_id = c.conversation_id AND cp2.user_id != ?
                    LIMIT 1
                ) AS other_last_name,
                (
                    SELECT u.role FROM conversation_participants cp2
                    JOIN utilisateurs u ON cp2.user_id = u.id
                    WHERE cp2.conversation_id = c.conversation_id AND cp2.user_id != ?
                    LIMIT 1
                ) AS other_role
            FROM conversations c
            JOIN conversation_participants cp ON c.conversation_id = cp.conversation_id
            WHERE cp.user_id = ?
            ORDER BY c.updated_at DESC
        ');
        $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
        $convs = $stmt->fetchAll();

        $result = array_map(function ($c) {
            $otherName = trim(($c['other_first_name'] ?? '') . ' ' . ($c['other_last_name'] ?? ''));
            $names = explode(' ', $otherName);
            $initials = '';
            foreach ($names as $n) {
                if (!empty($n)) $initials .= strtoupper($n[0]);
            }
            $colors = ['blue', 'green', 'orange', 'pink', 'purple'];
            $color = $colors[crc32($otherName) % count($colors)];

            return [
                'id' => (int)$c['id'],
                'subject' => $c['subject'],
                'related_post_id' => $c['related_post_id'] ? (int)$c['related_post_id'] : null,
                'participant' => [
                    'name' => $otherName ?: 'Utilisateur',
                    'role' => roleLabel($c['other_role'] ?? ''),
                    'initials' => $initials ?: '?',
                    'color' => $color,
                ],
                'requestTitle' => $c['subject'] ?: 'Sans objet',
                'lastMessage' => $c['lastMessage'] ?? '',
                'timestamp' => $c['timestamp'],
                'unread' => (int)$c['unread'],
            ];
        }, $convs);

        jsonResponse($result);

    case 'POST':
        $data = jsonBody();
        if (!$data) jsonResponse(['error' => 'Données invalides'], 400);

        $messageText = trim($data['message_text'] ?? '');
        if (!$messageText) jsonResponse(['error' => 'Message vide'], 400);

        if (!empty($_GET['action']) && $_GET['action'] === 'start') {
            $recipientId = (int)($data['recipient_id'] ?? 0);
            $postId = (int)($data['post_id'] ?? 0);
            $responseId = (int)($data['response_id'] ?? 0);
            $subject = trim($data['subject'] ?? '');

            if (!$recipientId && !$postId) jsonResponse(['error' => 'Destinataire ou publication requis'], 400);

            // If postId is provided, get post details
            if ($postId) {
                $postStmt = $db->prepare('SELECT sp.post_id, sp.title, sp.owner_id FROM skill_posts sp WHERE sp.post_id = ?');
                $postStmt->execute([$postId]);
                $post = $postStmt->fetch();
                if (!$post) jsonResponse(['error' => 'Publication introuvable'], 404);
                
                $recipientId = (int)$post['owner_id'];
                if (!$subject) $subject = $post['title'];
                $relatedPostId = $postId;
            }

            $check = $db->prepare("SELECT id FROM utilisateurs WHERE id = ? AND est_actif = 1");
            $check->execute([$recipientId]);
            if (!$check->fetch()) jsonResponse(['error' => 'Destinataire invalide'], 404);

            if ($recipientId === $userId) jsonResponse(['error' => 'Vous ne pouvez pas vous envoyer un message'], 400);

            // Check for existing conversation between these two users for this post
            if ($postId) {
                $existStmt = $db->prepare('
                    SELECT c.conversation_id FROM conversations c
                    JOIN conversation_participants cp1 ON c.conversation_id = cp1.conversation_id AND cp1.user_id = ?
                    JOIN conversation_participants cp2 ON c.conversation_id = cp2.conversation_id AND cp2.user_id = ?
                    WHERE c.related_post_id = ?
                    LIMIT 1
                ');
                $existStmt->execute([$userId, $recipientId, $postId]);
                $existing = $existStmt->fetch();
                if ($existing) {
                    jsonResponse(['success' => true, 'conversation_id' => (int)$existing['conversation_id']]);
                }
            }

            $db->beginTransaction();
            try {
                $stmt = $db->prepare('INSERT INTO conversations (subject, related_post_id) VALUES (?, ?)');
                $stmt->execute([$subject, $relatedPostId ?? null]);
                $newConvId = (int)$db->lastInsertId();

                $db->prepare('INSERT INTO conversation_participants (conversation_id, user_id, last_read_at) VALUES (?, ?, NOW())')
                   ->execute([$newConvId, $userId]);
                $db->prepare('INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?)')
                   ->execute([$newConvId, $recipientId]);

                // If there's a message, add it. If not, add a default message
                if ($messageText) {
                    $db->prepare('INSERT INTO messages (conversation_id, sender_id, message_text) VALUES (?, ?, ?)')
                       ->execute([$newConvId, $userId, $messageText]);
                } else {
                    $defaultMsg = 'Conversation démarrée pour "' . h($subject) . '"';
                    $db->prepare('INSERT INTO messages (conversation_id, sender_id, message_text) VALUES (?, ?, ?)')
                       ->execute([$newConvId, $userId, $defaultMsg]);
                }

                $db->commit();
                jsonResponse(['success' => true, 'conversation_id' => $newConvId], 201);
            } catch (Exception $e) {
                $db->rollBack();
                jsonResponse(['error' => 'Erreur lors de la création'], 500);
            }
        }

        $targetConvId = (int)($data['conversation_id'] ?? 0);
        if ($targetConvId) {
            $check = $db->prepare('SELECT 1 FROM conversation_participants WHERE conversation_id = ? AND user_id = ?');
            $check->execute([$targetConvId, $userId]);
            if (!$check->fetch()) jsonResponse(['error' => 'Conversation introuvable'], 404);

            $db->prepare('INSERT INTO messages (conversation_id, sender_id, message_text) VALUES (?, ?, ?)')
               ->execute([$targetConvId, $userId, $messageText]);
            $msgId = (int)$db->lastInsertId();

            // Notify the other participant
            $others = $db->prepare('SELECT user_id FROM conversation_participants WHERE conversation_id = ? AND user_id != ?');
            $others->execute([$targetConvId, $userId]);
            while ($o = $others->fetch()) {
                ajouterNotification((int)$o['user_id'], 'message', 'Nouveau message', $messageText, 'conversation', $targetConvId);
            }

            $stmt = $db->prepare('SELECT * FROM messages WHERE message_id = ?');
            $stmt->execute([$msgId]);
            jsonResponse($stmt->fetch(), 201);
        }

        $recipientId = (int)($data['recipient_id'] ?? 0);
        if ($recipientId) {
            if ($recipientId === $userId) jsonResponse(['error' => 'Vous ne pouvez pas vous envoyer un message'], 400);

            $check = $db->prepare("SELECT id FROM utilisateurs WHERE id = ? AND est_actif = 1");
            $check->execute([$recipientId]);
            if (!$check->fetch()) jsonResponse(['error' => 'Destinataire invalide'], 404);

            $stmt = $db->prepare('
                SELECT c.conversation_id FROM conversations c
                JOIN conversation_participants cp1 ON c.conversation_id = cp1.conversation_id AND cp1.user_id = ?
                JOIN conversation_participants cp2 ON c.conversation_id = cp2.conversation_id AND cp2.user_id = ?
            ');
            $stmt->execute([$userId, $recipientId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $existingConvId = (int)$existing['conversation_id'];
                $db->prepare('INSERT INTO messages (conversation_id, sender_id, message_text) VALUES (?, ?, ?)')
                   ->execute([$existingConvId, $userId, $messageText]);
                $stmt = $db->prepare('SELECT * FROM messages WHERE message_id = ?');
                $stmt->execute([$db->lastInsertId()]);
                jsonResponse($stmt->fetch(), 201);
            }

            $subject = trim($data['subject'] ?? '');
            $db->beginTransaction();
            try {
                $stmt = $db->prepare('INSERT INTO conversations (subject) VALUES (?)');
                $stmt->execute([$subject]);
                $newConvId = (int)$db->lastInsertId();

                $db->prepare('INSERT INTO conversation_participants (conversation_id, user_id, last_read_at) VALUES (?, ?, NOW())')
                   ->execute([$newConvId, $userId]);
                $db->prepare('INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?)')
                   ->execute([$newConvId, $recipientId]);

                $db->prepare('INSERT INTO messages (conversation_id, sender_id, message_text) VALUES (?, ?, ?)')
                   ->execute([$newConvId, $userId, $messageText]);

                $db->commit();
                jsonResponse(['success' => true, 'conversation_id' => $newConvId], 201);
            } catch (Exception $e) {
                $db->rollBack();
                jsonResponse(['error' => 'Erreur lors de la création'], 500);
            }
        }

        jsonResponse(['error' => 'conversation_id ou recipient_id requis'], 400);

    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
