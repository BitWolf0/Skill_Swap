<?php
/**
 * ISMO-SkillSwap v4 — Compétences API
 * 
 * Catalogue:
 *   GET  /api/competences.php            — list active skills
 *   POST /api/competences.php            — create skill (admin only)
 *   PUT  /api/competences.php?id=1       — update skill (admin only)
 *   DEL  /api/competences.php?id=1       — deactivate skill (admin only)
 * 
 * Declarations:
 *   GET  /api/competences.php?user=1     — user's declared skills
 *   POST /api/competences.php?action=declarer  — declare a skill
 *   PUT  /api/competences.php?action=valider   — validate/refuse (formateur)
 */
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['user_role'];
$db     = Database::getInstance();

// ─── DECLARER une compétence ──────────────────────────────────
if ($action === 'declarer' && $method === 'POST') {
    $data   = jsonBody();
    $skillId = (int)($data['competence_id'] ?? 0);
    $niveau  = $data['niveau_estime'] ?? 'Intermédiaire';

    if (!$skillId) jsonResponse(['error' => 'Compétence requise'], 400);
    if (!in_array($niveau, ['Débutant', 'Intermédiaire', 'Avancé'])) {
        $niveau = 'Intermédiaire';
    }

    // Check duplicate
    $dup = $db->prepare('SELECT id FROM competences_stagiaire WHERE utilisateur_id = ? AND competence_id = ?');
    $dup->execute([$userId, $skillId]);
    if ($dup->fetch()) {
        jsonResponse(['error' => 'Cette compétence est déjà déclarée'], 409);
    }

    $stmt = $db->prepare('INSERT INTO competences_stagiaire (utilisateur_id, competence_id, niveau_estime) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $skillId, $niveau]);

    // Bump catalogue usage counter
    $db->prepare('UPDATE competences_catalogue SET nb_utilisations = nb_utilisations + 1 WHERE id = ?')
       ->execute([$skillId]);

    // Notify formateurs of new pending declaration
    $skill = $db->prepare('SELECT nom FROM competences_catalogue WHERE id = ?');
    $skill->execute([$skillId]);
    $skillName = $skill->fetchColumn();
    $formateurs = $db->query("SELECT id FROM utilisateurs WHERE role = 'formateur' OR role = 'administrateur'")->fetchAll();
    $declarationId = (int)$db->lastInsertId();
    if (!empty($formateurs)) {
        $vals = [];
        $params = [];
        foreach ($formateurs as $f) {
            $vals[] = '(?, ?, ?, ?, ?, ?, NOW())';
            $params[] = (int)$f['id'];
            $params[] = 'declaration';
            $params[] = 'Nouvelle déclaration';
            $params[] = "{$skillName} déclarée en attente de validation";
            $params[] = 'competence';
            $params[] = $declarationId;
        }
        $db->prepare('INSERT INTO notifications (user_id, notification_type, title, message, reference_type, reference_id, created_at) VALUES ' . implode(',', $vals))->execute($params);
    }

    jsonResponse(['success' => true, 'message' => 'Compétence déclarée. En attente de validation.', 'id' => (int)$db->lastInsertId()], 201);
}

// ─── VALIDER / REFUSER une déclaration (formateur) ────────────
if ($action === 'valider' && $method === 'PUT') {
    if (!in_array($role, ['formateur', 'administrateur'])) {
        jsonResponse(['error' => 'Seuls les formateurs peuvent valider'], 403);
    }

    $data   = jsonBody();
    $declId = (int)($data['declaration_id'] ?? 0);
    $statut = $data['statut'] ?? ''; // 'Validé' or 'Refusé'
    $motif  = trim($data['motif'] ?? '');

    if (!$declId) jsonResponse(['error' => 'Déclaration requise'], 400);
    if (!in_array($statut, ['Validé', 'Refusé'])) {
        jsonResponse(['error' => 'Statut invalide (Validé ou Refusé)'], 400);
    }

    $stmt = $db->prepare('SELECT id, utilisateur_id FROM competences_stagiaire WHERE id = ?');
    $stmt->execute([$declId]);
    $decl = $stmt->fetch();
    if (!$decl) jsonResponse(['error' => 'Déclaration non trouvée'], 404);

    $db->prepare('UPDATE competences_stagiaire SET statut_validation = ?, validateur_id = ?, date_validation = NOW(), motif_refus = ? WHERE id = ?')
       ->execute([$statut, $userId, $motif ?: null, $declId]);

    // Gamification: award points when validated
    if ($statut === 'Validé') {
        ajouterPoints((int)$decl['utilisateur_id'], 10, 'Compétence validée');
        ajouterNotification((int)$decl['utilisateur_id'], 'validation', 'Compétence validée', 'Votre compétence a été validée par un formateur', 'competence', $declId);
    } else {
        ajouterNotification((int)$decl['utilisateur_id'], 'refus', 'Compétence refusée', $motif ? "Compétence refusée : {$motif}" : 'Votre compétence a été refusée', 'competence', $declId);
    }

    jsonResponse(['success' => true, 'message' => $statut === 'Validé' ? 'Compétence validée' : 'Compétence refusée']);
}

switch ($method) {
    // ─── LIST ─────────────────────────────────────────────────
    case 'GET':
        $skillId = (int)($_GET['id'] ?? 0);
        $targetUser = (int)($_GET['user'] ?? 0);
        $pending = $_GET['en_attente'] ?? '';

        // Single skill
        if ($skillId) {
            $stmt = $db->prepare('SELECT * FROM competences_catalogue WHERE id = ?');
            $stmt->execute([$skillId]);
            $skill = $stmt->fetch();
            if (!$skill) jsonResponse(['error' => 'Compétence non trouvée'], 404);
            jsonResponse($skill);
        }

        // User's declared skills
        if ($targetUser) {
            $stmt = $db->prepare('
                SELECT cs.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
                       v.nom AS validateur_nom, v.prenom AS validateur_prenom
                FROM competences_stagiaire cs
                JOIN competences_catalogue c ON cs.competence_id = c.id
                LEFT JOIN utilisateurs v ON cs.validateur_id = v.id
                WHERE cs.utilisateur_id = ?
                ORDER BY cs.date_declaration DESC
            ');
            $stmt->execute([$targetUser]);
            jsonResponse($stmt->fetchAll());
        }

        // Pending declarations (for formateurs)
        if ($pending === '1') {
            if (!in_array($role, ['formateur', 'administrateur'])) {
                jsonResponse(['error' => 'Non autorisé'], 403);
            }
            $stmt = $db->query('
                SELECT cs.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
                       u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom,
                       u.filiere AS utilisateur_filiere
                FROM competences_stagiaire cs
                JOIN competences_catalogue c ON cs.competence_id = c.id
                JOIN utilisateurs u ON cs.utilisateur_id = u.id
                WHERE cs.statut_validation = "En attente"
                ORDER BY cs.date_declaration ASC
            ');
            jsonResponse($stmt->fetchAll());
        }

        // List catalogue
        $category = $_GET['categorie'] ?? '';
        $search   = $_GET['search'] ?? '';

        $sql = 'SELECT * FROM competences_catalogue WHERE est_active = 1';
        $params = [];

        if ($category) {
            $sql .= ' AND categorie = ?';
            $params[] = $category;
        }
        if ($search) {
            $sql .= ' AND (nom LIKE ? OR description LIKE ?)';
            $term = "%$search%";
            $params[] = $term;
            $params[] = $term;
        }
        $sql .= ' ORDER BY nb_utilisations DESC, nom ASC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse($stmt->fetchAll());

    // ─── CREATE skill (admin) ─────────────────────────────────
    case 'POST':
        if ($role !== 'administrateur') {
            jsonResponse(['error' => 'Seuls les administrateurs peuvent ajouter des compétences'], 403);
        }
        $data = jsonBody();
        if (empty($data['nom'])) jsonResponse(['error' => 'Nom de compétence requis'], 400);

        $stmt = $db->prepare('INSERT INTO competences_catalogue (nom, categorie, description) VALUES (?, ?, ?)');
        $stmt->execute([$data['nom'], $data['categorie'] ?? '', $data['description'] ?? '']);
        jsonResponse(['success' => true, 'id' => (int)$db->lastInsertId()], 201);

    // ─── UPDATE skill (admin) ─────────────────────────────────
    case 'PUT':
        if ($role !== 'administrateur') {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }
        $skillId = (int)($_GET['id'] ?? 0);
        if (!$skillId) jsonResponse(['error' => 'ID requis'], 400);
        $data = jsonBody();

        $allowed = ['nom', 'categorie', 'description', 'est_active'];
        $fields = [];
        $params = [];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (empty($fields)) jsonResponse(['error' => 'Aucun champ'], 400);
        $params[] = $skillId;
        $db->prepare('UPDATE competences_catalogue SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($params);
        jsonResponse(['success' => true]);

    // ─── DELETE (deactivate) skill (admin) ────────────────────
    case 'DELETE':
        if ($role !== 'administrateur') {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }
        $skillId = (int)($_GET['id'] ?? 0);
        if (!$skillId) jsonResponse(['error' => 'ID requis'], 400);
        $db->prepare("UPDATE competences_catalogue SET est_active = 0 WHERE id = ?")->execute([$skillId]);
        jsonResponse(['success' => true]);

    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
