<?php
/**
 * ISMO-SkillSwap — Notifications API
 * GET  /api/notifications.php        — user's notifications
 * PUT  /api/notifications.php?id=1   — mark as read
 * PUT  /api/notifications.php?all=1  — mark all as read
 * GET  /api/notifications.php?count=1 — unread count
 */
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$db = Database::getInstance();
$userId = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (!empty($_GET['count'])) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$userId]);
            jsonResponse(['count' => (int)$stmt->fetchColumn()]);
        }

        $stmt = $db->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50');
        $stmt->execute([$userId]);
        jsonResponse($stmt->fetchAll());

    case 'PUT':
        if (!empty($_GET['all'])) {
            $db->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0")
               ->execute([$userId]);
            jsonResponse(['success' => true, 'message' => 'Toutes les notifications marquées comme lues']);
        }

        $notifId = (int)($_GET['id'] ?? 0);
        if (!$notifId) jsonResponse(['error' => 'ID requis'], 400);

        $db->prepare('UPDATE notifications SET is_read = 1, read_at = NOW() WHERE notification_id = ? AND user_id = ?')
           ->execute([$notifId, $userId]);
        jsonResponse(['success' => true]);

    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
