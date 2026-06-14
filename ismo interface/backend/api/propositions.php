<?php
/**
 * ISMO-SkillSwap v4 — Propositions API
 * 
 * POST /api/propositions.php              — propose help on a request
 * PUT  /api/propositions.php?id=1         — accept / refuse / withdraw a proposal
 * PUT  /api/propositions.php?action=resoudre&id=X  — mark request resolved + rate mentor
 */
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$method = $_SERVER['REQUEST_METHOD'];
$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['user_role'];
$db     = Database::getInstance();
$action = $_GET['action'] ?? '';

// ─── UPDATE SOLUTION (mentor already assigned, updates proposal message) ──
if ($action === 'update_solution' && $method === 'PUT') {
    $data = jsonBody();
    $reqId = (int)($data['demande_id'] ?? 0);
    $message = trim($data['message'] ?? '');
    if (!$reqId) jsonResponse(['error' => 'ID de la demande requis'], 400);
    if (!$message) jsonResponse(['error' => 'La solution ne peut pas être vide'], 400);

    // Verify the user is the assigned mentor for this request
    $check = $db->prepare('SELECT id, mentor_id, statut FROM demandes_aide WHERE id = ?');
    $check->execute([$reqId]);
    $demande = $check->fetch();
    if (!$demande) jsonResponse(['error' => 'Demande non trouvée'], 404);
    if ($demande['mentor_id'] != $userId) {
        jsonResponse(['error' => 'Vous n\'êtes pas le mentor assigné à cette demande'], 403);
    }
    if ($demande['statut'] !== 'En cours') {
        jsonResponse(['error' => 'La demande doit être en cours pour soumettre une solution'], 400);
    }

    // Update the mentor's accepted proposal message
    $stmt = $db->prepare("UPDATE propositions_aide SET message = ? WHERE demande_id = ? AND proposant_id = ? AND statut = 'Acceptée' LIMIT 1");
    $stmt->execute([$message, $reqId, $userId]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['error' => 'Aucune proposition acceptée trouvée pour cette demande'], 404);
    }

    jsonResponse(['success' => true, 'message' => 'Solution mise à jour avec succès']);
}

// ─── RESOLVE + RATE (action separate car combine deux opérations) ──
if ($action === 'resoudre' && $method === 'PUT') {
    $reqId = (int)($_GET['id'] ?? 0);
    if (!$reqId) jsonResponse(['error' => 'ID requis'], 400);

    $data = jsonBody();
    $note = isset($data['note']) ? (int)$data['note'] : null;
    $commentaire = trim($data['commentaire'] ?? '');

    // Check ownership + current status
    $check = $db->prepare('SELECT auteur_id, mentor_id, statut FROM demandes_aide WHERE id = ?');
    $check->execute([$reqId]);
    $demande = $check->fetch();
    if (!$demande) jsonResponse(['error' => 'Demande non trouvée'], 404);
    if ($demande['auteur_id'] != $userId && $demande['mentor_id'] != $userId && $role !== 'administrateur') {
        jsonResponse(['error' => 'Seul l\'auteur, le mentor assigné ou un administrateur peut résoudre'], 403);
    }
    if ($demande['statut'] !== 'En cours') {
        jsonResponse(['error' => 'Seules les demandes en cours peuvent être résolues'], 400);
    }
    if (!$demande['mentor_id']) {
        jsonResponse(['error' => 'Aucun mentor assigné à cette demande'], 400);
    }
    if (!$note || $note < 1 || $note > 5) {
        jsonResponse(['error' => 'Note pour le mentor (1-5) requise'], 400);
    }

    $db->beginTransaction();
    try {
        // Mark request as resolved
        $db->prepare("UPDATE demandes_aide SET statut = 'Résolu', date_resolution = NOW(), note_mentor = ?, commentaire_mentor = ? WHERE id = ?")
           ->execute([$note, $commentaire ?: null, $reqId]);

        // Mark all pending proposals for this request as refused
        $db->prepare("UPDATE propositions_aide SET statut = 'Refusée', date_traitement = NOW() WHERE demande_id = ? AND statut = 'En attente'")
           ->execute([$reqId]);

        $db->commit();

        // Gamification: award points (mentor gets note × 5, 1★=5pts, 5★=25pts)
        $mentorPoints = $note ? min(25, max(5, $note * 5)) : 10;
        ajouterPoints((int)$demande['auteur_id'], 5, 'Demande résolue');
        ajouterPoints((int)$demande['mentor_id'], $mentorPoints, 'Aide fournie — note ' . ($note ?? 'N/A') . '/5');

        // Notify both parties
        ajouterNotification((int)$demande['auteur_id'], 'resolu', 'Demande résolue', 'Votre demande a été marquée comme résolue', 'demande', $reqId);
        ajouterNotification((int)$demande['mentor_id'], 'resolu', 'Aide terminée', 'L\'aide que vous avez fournie a été marquée comme terminée', 'demande', $reqId);
    } catch (Exception $e) {
        $db->rollBack();
        jsonResponse(['error' => 'Erreur lors de la résolution'], 500);
    }

    jsonResponse(['success' => true, 'message' => 'Demande résolue. Merci pour votre contribution !']);
}

switch ($method) {

    // ─── PROPOSE HELP ────────────────────────────────────────────
    case 'POST':
        $data = jsonBody();
        $reqId = (int)($data['demande_id'] ?? 0);
        $message = trim($data['message'] ?? '');

        if (!$reqId) jsonResponse(['error' => 'Demande requise'], 400);

        // Validate request exists and is open
        $check = $db->prepare('SELECT id, auteur_id, statut FROM demandes_aide WHERE id = ?');
        $check->execute([$reqId]);
        $demande = $check->fetch();
        if (!$demande) jsonResponse(['error' => 'Demande non trouvée'], 404);
        if ($demande['statut'] !== 'Ouvert') {
            jsonResponse(['error' => 'Cette demande n\'accepte plus de propositions'], 400);
        }
        if ($demande['auteur_id'] == $userId) {
            jsonResponse(['error' => 'Vous ne pouvez pas proposer votre aide sur votre propre demande'], 400);
        }

        // Check duplicate proposal
        $dup = $db->prepare('SELECT id FROM propositions_aide WHERE demande_id = ? AND proposant_id = ?');
        $dup->execute([$reqId, $userId]);
        if ($dup->fetch()) {
            jsonResponse(['error' => 'Vous avez déjà proposé votre aide sur cette demande'], 409);
        }

        $stmt = $db->prepare('INSERT INTO propositions_aide (demande_id, proposant_id, message) VALUES (?, ?, ?)');
        $stmt->execute([$reqId, $userId, $message ?: null]);
        $propId = (int)$db->lastInsertId();

        // Notify author
        ajouterNotification((int)$demande['auteur_id'], 'proposition', 'Nouvelle proposition', 'Quelqu\'un a proposé son aide sur votre demande', 'proposition', $propId);

        jsonResponse([
            'success' => true,
            'message' => 'Proposition envoyée avec succès',
        ], 201);


    // ─── ACCEPT / REFUSE / WITHDRAW ─────────────────────────────
    case 'PUT':
        $propId = (int)($_GET['id'] ?? 0);
        if (!$propId) jsonResponse(['error' => 'ID requis'], 400);

        $data = jsonBody();
        $newStatut = $data['statut'] ?? '';

        if (!in_array($newStatut, ['Acceptée', 'Refusée', 'Retirée'])) {
            jsonResponse(['error' => 'Statut invalide. Valeurs: Acceptée, Refusée, Retirée'], 400);
        }

        // Fetch proposal with joined request info
        $stmt = $db->prepare('
            SELECT p.*, d.auteur_id, d.statut AS demande_statut, d.titre, d.id AS demande_id
            FROM propositions_aide p
            JOIN demandes_aide d ON p.demande_id = d.id
            WHERE p.id = ?
        ');
        $stmt->execute([$propId]);
        $prop = $stmt->fetch();
        if (!$prop) jsonResponse(['error' => 'Proposition non trouvée'], 404);

        $isOwner    = $prop['auteur_id'] == $userId;
        $isProposer = $prop['proposant_id'] == $userId;
        $isAdmin    = $role === 'administrateur';

        // Authorization
        if ($newStatut === 'Retirée' && !$isProposer && !$isAdmin) {
            jsonResponse(['error' => 'Seul le proposant peut retirer sa proposition'], 403);
        }
        if (in_array($newStatut, ['Acceptée', 'Refusée']) && !$isOwner && !$isAdmin) {
            jsonResponse(['error' => 'Seul l\'auteur de la demande peut accepter ou refuser'], 403);
        }
        if ($prop['demande_statut'] !== 'Ouvert' && $newStatut === 'Acceptée') {
            jsonResponse(['error' => 'La demande n\'accepte plus de nouvelles acceptations'], 400);
        }

        $db->beginTransaction();
        try {
            // Update proposal status
            $db->prepare('UPDATE propositions_aide SET statut = ?, date_traitement = NOW() WHERE id = ?')
               ->execute([$newStatut, $propId]);

            if ($newStatut === 'Acceptée') {
                // Assign mentor to the request
                $db->prepare("UPDATE demandes_aide SET mentor_id = ?, statut = 'En cours' WHERE id = ?")
                   ->execute([$prop['proposant_id'], $prop['demande_id']]);

                // Reject all other pending proposals
                $db->prepare("UPDATE propositions_aide SET statut = 'Refusée', date_traitement = NOW() WHERE demande_id = ? AND id != ? AND statut = 'En attente'")
                   ->execute([$prop['demande_id'], $propId]);
            }

            $db->commit();

            // Gamification: small points for getting accepted as mentor
            if ($newStatut === 'Acceptée') {
                ajouterPoints((int)$prop['proposant_id'], 5, 'Proposition acceptée');
                ajouterNotification((int)$prop['proposant_id'], 'acceptee', 'Proposition acceptée', 'Votre aide a été acceptée ! Consultez la demande.', 'proposition', $propId);
                ajouterNotification((int)$prop['auteur_id'], 'acceptee', 'Proposition acceptée', 'Vous avez accepté un mentor pour votre demande', 'proposition', $propId);
            }
        } catch (Exception $e) {
            $db->rollBack();
            jsonResponse(['error' => 'Erreur lors du traitement'], 500);
        }

        $msg = match ($newStatut) {
            'Acceptée' => 'Proposition acceptée ! Le mentor a été assigné.',
            'Refusée'  => 'Proposition refusée.',
            'Retirée'  => 'Proposition retirée.',
            default    => 'Statut mis à jour.',
        };

        jsonResponse(['success' => true, 'message' => $msg]);


    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
