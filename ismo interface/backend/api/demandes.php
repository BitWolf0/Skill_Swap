<?php
/**
 * ISMO-SkillSwap v4 — Marketplace API (Demandes d'Aide)
 * 
 * GET    /api/demandes.php              — list all open requests (with filters)
 * GET    /api/demandes.php?id=1         — single request detail + proposals
 * GET    /api/demandes.php?user=1       — requests by a specific user
 * POST   /api/demandes.php              — create new help request
 * PUT    /api/demandes.php?id=1         — update request (owner only)
 * DELETE /api/demandes.php?id=1         — soft-delete request
 */
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$method = $_SERVER['REQUEST_METHOD'];
$userId = (int)$_SESSION['user_id'];
$role   = $_SESSION['user_role'];
$db     = Database::getInstance();

switch ($method) {

    // ─── LIST / GET ─────────────────────────────────────────────
    case 'GET':
        $reqId  = (int)($_GET['id'] ?? 0);
        $owner  = (int)($_GET['user'] ?? 0);
        $statut = $_GET['statut'] ?? '';
        $skill  = (int)($_GET['competence_id'] ?? 0);
        $search = $_GET['search'] ?? '';

        // Single request detail
        if ($reqId) {
            $stmt = $db->prepare('
                SELECT d.*,
                       c.nom AS competence_nom, c.categorie AS competence_categorie,
                       a.nom AS auteur_nom, a.prenom AS auteur_prenom,
                       a.filiere AS auteur_filiere,
                       m.nom AS mentor_nom, m.prenom AS mentor_prenom
                FROM demandes_aide d
                JOIN competences_catalogue c ON d.competence_id = c.id
                JOIN utilisateurs a ON d.auteur_id = a.id
                LEFT JOIN utilisateurs m ON d.mentor_id = m.id
                WHERE d.id = ?
            ');
            $stmt->execute([$reqId]);
            $request = $stmt->fetch();
            if (!$request) jsonResponse(['error' => 'Demande non trouvée'], 404);

            // Attach proposals
            $props = $db->prepare('
                SELECT p.*, u.nom, u.prenom
                FROM propositions_aide p
                JOIN utilisateurs u ON p.proposant_id = u.id
                WHERE p.demande_id = ?
                ORDER BY p.creee_le DESC
            ');
            $props->execute([$reqId]);
            $request['propositions'] = $props->fetchAll();

            jsonResponse($request);
        }

        // User's own requests
        if ($owner) {
            $stmt = $db->prepare('
                SELECT d.*, c.nom AS competence_nom
                FROM demandes_aide d
                JOIN competences_catalogue c ON d.competence_id = c.id
                WHERE d.auteur_id = ?
                ORDER BY d.creee_le DESC
            ');
            $stmt->execute([$owner]);
            jsonResponse($stmt->fetchAll());
        }

        // Public listing with filters
        $sql = '
            SELECT d.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
                   u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                   (SELECT COUNT(*) FROM propositions_aide WHERE demande_id = d.id) AS nb_propositions
            FROM demandes_aide d
            JOIN competences_catalogue c ON d.competence_id = c.id
            JOIN utilisateurs u ON d.auteur_id = u.id
            WHERE 1=1
        ';
        $params = [];

        if ($statut) {
            $sql .= ' AND d.statut = ?';
            $params[] = $statut;
        } else {
            $sql .= " AND d.statut != 'Résolu'";
        }

        if ($skill) {
            $sql .= ' AND d.competence_id = ?';
            $params[] = $skill;
        }

        if ($search) {
            $sql .= ' AND (d.titre LIKE ? OR d.description LIKE ?)';
            $term = "%$search%";
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= ' ORDER BY d.creee_le DESC LIMIT 50';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse($stmt->fetchAll());


    // ─── CREATE ─────────────────────────────────────────────────
    case 'POST':
        $data = jsonBody();

        if (empty($data['titre']) || empty($data['competence_id'])) {
            jsonResponse(['error' => 'Titre et compétence requis'], 400);
        }

        $stmt = $db->prepare('
            INSERT INTO demandes_aide (auteur_id, competence_id, titre, description, urgence)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $userId,
            (int)$data['competence_id'],
            trim($data['titre']),
            trim($data['description'] ?? ''),
            $data['urgence'] ?? 'Moyenne',
        ]);

        $newId = (int)$db->lastInsertId();

        // Notify formateurs of new request
        $skill = $db->prepare('SELECT nom FROM competences_catalogue WHERE id = ?');
        $skill->execute([(int)$data['competence_id']]);
        $skillName = $skill->fetchColumn();
        $formateurs = $db->query("SELECT id FROM utilisateurs WHERE role = 'formateur' OR role = 'administrateur'")->fetchAll();
        if (!empty($formateurs)) {
            $vals = [];
            $params = [];
            foreach ($formateurs as $f) {
                $vals[] = '(?, ?, ?, ?, ?, ?, NOW())';
                $params[] = (int)$f['id'];
                $params[] = 'nouvelle_demande';
                $params[] = 'Nouvelle demande d\'aide';
                $params[] = "Demande publiée : {$data['titre']} ({$skillName})";
                $params[] = 'demande';
                $params[] = $newId;
            }
            $db->prepare('INSERT INTO notifications (user_id, notification_type, title, message, reference_type, reference_id, created_at) VALUES ' . implode(',', $vals))->execute($params);
        }

        jsonResponse(['success' => true, 'id' => $newId, 'message' => 'Demande publiée'], 201);


    // ─── UPDATE ─────────────────────────────────────────────────
    case 'PUT':
        $reqId = (int)($_GET['id'] ?? 0);
        if (!$reqId) jsonResponse(['error' => 'ID requis'], 400);

        // Check ownership
        $check = $db->prepare('SELECT auteur_id, statut FROM demandes_aide WHERE id = ?');
        $check->execute([$reqId]);
        $demande = $check->fetch();
        if (!$demande) jsonResponse(['error' => 'Demande non trouvée'], 404);
        if ($demande['auteur_id'] != $userId && $role !== 'administrateur') {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }
        if ($demande['statut'] === 'Résolu') {
            jsonResponse(['error' => 'Impossible de modifier une demande résolue'], 400);
        }

        $data = jsonBody();
        $allowed = ['titre', 'description', 'urgence', 'competence_id'];
        $fields = [];
        $params = [];
        foreach ($allowed as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (empty($fields)) jsonResponse(['error' => 'Aucun champ à modifier'], 400);
        $params[] = $reqId;

        $db->prepare('UPDATE demandes_aide SET ' . implode(', ', $fields) . ' WHERE id = ?')
           ->execute($params);

        jsonResponse(['success' => true, 'message' => 'Demande mise à jour']);


    // ─── DELETE (soft) ──────────────────────────────────────────
    case 'DELETE':
        $reqId = (int)($_GET['id'] ?? 0);
        if (!$reqId) jsonResponse(['error' => 'ID requis'], 400);

        $check = $db->prepare('SELECT auteur_id, statut FROM demandes_aide WHERE id = ?');
        $check->execute([$reqId]);
        $demande = $check->fetch();
        if (!$demande) jsonResponse(['error' => 'Demande non trouvée'], 404);
        if ($demande['auteur_id'] != $userId && $role !== 'administrateur' && $role !== 'formateur') {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }

        // Soft delete: set statut to a cancelled-like state
        $db->prepare("UPDATE demandes_aide SET statut = 'Résolu' WHERE id = ?")
           ->execute([$reqId]);

        jsonResponse(['success' => true, 'message' => 'Demande fermée']);


    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
