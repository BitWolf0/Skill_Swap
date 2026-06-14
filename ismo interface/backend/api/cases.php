<?php
declare(strict_types=1);
/**
 * ISMO-SkillSwap — Service Cases API (support & moderation)
 * GET  /api/cases.php                — list cases
 * GET  /api/cases.php?type=report    — moderation queue
 * POST /api/cases.php                — create case
 * PUT  /api/cases.php?id=1           — update case (admin)
 * POST /api/cases.php?action=reply   — add reply
 */
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$db = Database::getInstance();
$userId = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ─── Add reply ───
if ($action === 'reply' && $method === 'POST') {
    $data = jsonBody();
    $caseId = (int)($data['case_id'] ?? 0);
    $message = $data['message'] ?? '';

    if (!$caseId || empty($message)) jsonResponse(['error' => 'Message requis'], 400);

    $isStaff = in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_FORMATEUR]) ? 1 : 0;

    $stmt = $db->prepare('INSERT INTO case_replies (case_id, user_id, message, is_staff) VALUES (?, ?, ?, ?)');
    $stmt->execute([$caseId, $userId, $message, $isStaff]);

    // Update case status
    $db->prepare("UPDATE service_cases SET status = 'in_progress' WHERE case_id = ? AND status = 'open'")
       ->execute([$caseId]);

    jsonResponse(['success' => true, 'message' => 'Réponse ajoutée'], 201);
}

switch ($method) {
    case 'GET':
        $caseId = (int)($_GET['id'] ?? 0);
        $caseType = $_GET['type'] ?? '';

        if ($caseId) {
            $stmt = $db->prepare('SELECT sc.*, CONCAT(u.prenom, " ", u.nom) as created_by_name FROM service_cases sc JOIN utilisateurs u ON sc.created_by_user_id = u.id WHERE sc.case_id = ?');
            $stmt->execute([$caseId]);
            $case = $stmt->fetch();
            if (!$case) jsonResponse(['error' => 'Cas non trouvé'], 404);

            // Get replies
            $replies = $db->prepare('SELECT cr.*, CONCAT(u.prenom, " ", u.nom) as user_name FROM case_replies cr JOIN utilisateurs u ON cr.user_id = u.id WHERE cr.case_id = ? ORDER BY cr.created_at ASC');
            $replies->execute([$caseId]);
            $case['replies'] = $replies->fetchAll();

            jsonResponse($case);
        }

        $sql = 'SELECT sc.*, CONCAT(u.prenom, " ", u.nom) as created_by_name FROM service_cases sc JOIN utilisateurs u ON sc.created_by_user_id = u.id WHERE 1=1';
        $params = [];

        if ($caseType) {
            $sql .= ' AND sc.case_type = ?';
            $params[] = $caseType;
        }

        // Non-admin users see only their own cases
        if (!in_array($_SESSION['user_role'], [ROLE_ADMIN, ROLE_FORMATEUR])) {
            $sql .= ' AND sc.created_by_user_id = ?';
            $params[] = $userId;
        }

        $sql .= ' ORDER BY sc.created_at DESC LIMIT 50';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse($stmt->fetchAll());

    case 'POST':
        $data = jsonBody();
        if (empty($data['subject']) || empty($data['description'])) {
            jsonResponse(['error' => 'Sujet et description requis'], 400);
        }

        $stmt = $db->prepare('INSERT INTO service_cases (case_type, created_by_user_id, subject, description, category, target_user_id, target_content_type, target_content_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['case_type'] ?? 'support',
            $userId,
            $data['subject'],
            $data['description'],
            $data['category'] ?? 'other',
            !empty($data['target_user_id']) ? (int)$data['target_user_id'] : null,
            $data['target_content_type'] ?? null,
            !empty($data['target_content_id']) ? (int)$data['target_content_id'] : null,
        ]);

        jsonResponse(['success' => true, 'message' => 'Cas créé', 'id' => (int)$db->lastInsertId()], 201);

    case 'PUT':
        requireRole([ROLE_ADMIN, ROLE_FORMATEUR]);
        $caseId = (int)($_GET['id'] ?? 0);
        if (!$caseId) jsonResponse(['error' => 'ID requis'], 400);

        $data = jsonBody();
        $allowed = ['status', 'priority', 'severity_level', 'assigned_to_user_id', 'action_taken', 'resolution_notes'];
        $fields = [];
        $params = [];
        foreach ($allowed as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (in_array($data['status'] ?? '', ['resolved', 'closed', 'dismissed'])) {
            $fields[] = 'resolved_at = NOW()';
            $fields[] = 'reviewed_by_user_id = ?';
            $params[] = $userId;
        }
        if (empty($fields)) jsonResponse(['error' => 'Aucun champ'], 400);
        $params[] = $caseId;

        $db->prepare('UPDATE service_cases SET ' . implode(', ', $fields) . ' WHERE case_id = ?')->execute($params);
        jsonResponse(['success' => true, 'message' => 'Cas mis à jour']);

    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
