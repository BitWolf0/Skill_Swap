<?php
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$method = $_SERVER['REQUEST_METHOD'];
$sessionUserId = (int)$_SESSION['user_id'];
$role = $_SESSION['user_role'];
$action = $_GET['action'] ?? '';
$db = Database::getInstance();

if ($action === 'assign' && $method === 'POST') {
    if (!in_array($role, ['formateur', 'administrateur'])) {
        jsonResponse(['error' => 'Non autorisé'], 403);
    }

    $data = jsonBody();
    $targetUserId = (int)($data['user_id'] ?? 0);
    $badgeId = (int)($data['badge_id'] ?? 0);

    if (!$targetUserId || !$badgeId) jsonResponse(['error' => 'Utilisateur et badge requis'], 400);

    $check = $db->prepare('SELECT id FROM badges_stagiaire WHERE utilisateur_id = ? AND badge_id = ?');
    $check->execute([$targetUserId, $badgeId]);
    if ($check->fetch()) jsonResponse(['error' => 'Badge déjà attribué à cet utilisateur'], 409);

    $db->prepare('INSERT INTO badges_stagiaire (utilisateur_id, badge_id, attribue_par) VALUES (?, ?, ?)')
       ->execute([$targetUserId, $badgeId, (string)$sessionUserId]);

    $badge = $db->prepare('SELECT points_requis, nom FROM badges WHERE id = ?');
    $badge->execute([$badgeId]);
    $b = $badge->fetch();
    if ($b && $b['points_requis'] > 0) {
        ajouterPoints($targetUserId, (int)$b['points_requis'], 'Badge attribué');
    }

    $badgeName = $b['nom'] ?? 'Badge';
    ajouterNotification($targetUserId, 'badge', 'Badge débloqué !', "Vous avez reçu le badge « {$badgeName} »", 'badge', $badgeId);

    jsonResponse(['success' => true, 'message' => 'Badge attribué'], 201);
}

switch ($method) {
    case 'GET':
        $targetUser = (int)($_GET['user'] ?? 0);

        if ($targetUser) {
            $stmt = $db->prepare('
                SELECT b.*, bs.obtenu_le, bs.motif
                FROM badges_stagiaire bs
                JOIN badges b ON bs.badge_id = b.id
                WHERE bs.utilisateur_id = ?
                ORDER BY bs.obtenu_le DESC
            ');
            $stmt->execute([$targetUser]);
            jsonResponse($stmt->fetchAll());
        }

        $stmt = $db->query('SELECT * FROM badges WHERE est_actif = 1 ORDER BY categorie, nom');
        jsonResponse($stmt->fetchAll());

    case 'POST':
        if ($role !== 'administrateur') jsonResponse(['error' => 'Non autorisé'], 403);
        $data = jsonBody();
        if (empty($data['nom'])) jsonResponse(['error' => 'Nom du badge requis'], 400);

        $stmt = $db->prepare('INSERT INTO badges (nom, description, points_requis, icone) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['nom'],
            $data['description'] ?? '',
            (int)($data['points_requis'] ?? 0),
            $data['icone'] ?? '',
        ]);
        jsonResponse(['success' => true, 'id' => (int)$db->lastInsertId()], 201);

    case 'DELETE':
        if (!in_array($role, ['formateur', 'administrateur'])) {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }
        $badgeId = (int)($_GET['id'] ?? 0);
        if (!$badgeId) jsonResponse(['error' => 'ID requis'], 400);

        $db->prepare('DELETE FROM badges_stagiaire WHERE badge_id = ?')->execute([$badgeId]);
        $db->prepare('UPDATE badges SET est_actif = 0 WHERE id = ?')->execute([$badgeId]);
        jsonResponse(['success' => true, 'message' => 'Badge désactivé']);

    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
