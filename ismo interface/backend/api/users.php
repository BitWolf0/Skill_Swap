<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$method = $_SERVER['REQUEST_METHOD'];
$userId = (int)($_GET['id'] ?? 0);
$db = Database::getInstance();

switch ($method) {
    case 'GET':
        if ($userId) {
            $stmt = $db->prepare('SELECT id, prenom, nom, email, role, points_gamification, filiere, bio, photo, date_inscription, derniere_connexion FROM utilisateurs WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if (!$user) jsonResponse(['error' => 'Utilisateur non trouvé'], 404);

            $skills = $db->prepare('SELECT cc.nom, cc.categorie, cs.niveau_estime, cs.statut_validation FROM competences_stagiaire cs JOIN competences_catalogue cc ON cs.competence_id = cc.id WHERE cs.utilisateur_id = ?');
            $skills->execute([$userId]);
            $user['skills'] = $skills->fetchAll();

            $badges = $db->prepare('SELECT b.nom, b.icone, b.categorie, bs.obtenu_le FROM badges_stagiaire bs JOIN badges b ON bs.badge_id = b.id WHERE bs.utilisateur_id = ?');
            $badges->execute([$userId]);
            $user['badges'] = $badges->fetchAll();

            jsonResponse($user);
        }

        $role = $_GET['role'] ?? '';
        $search = $_GET['search'] ?? '';

        $sql = 'SELECT id, prenom, nom, email, role, points_gamification, filiere FROM utilisateurs WHERE 1=1';
        $params = [];

        if ($role) {
            $sql .= ' AND role = ?';
            $params[] = $role;
        }
        if ($search) {
            $sql .= ' AND (prenom LIKE ? OR nom LIKE ? OR email LIKE ?)';
            $t = "%$search%";
            $params[] = $t;
            $params[] = $t;
            $params[] = $t;
        }
        $sql .= ' ORDER BY date_inscription DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse($stmt->fetchAll());

    case 'PUT':
        $action = $_GET['action'] ?? '';
        $currentId = (int)$_SESSION['user_id'];
        $targetId = $userId ?: $currentId;

        // ─── Admin: toggle account status ───────────────
        if ($action === 'toggle-status') {
            if ($_SESSION['user_role'] !== 'administrateur') {
                jsonResponse(['error' => 'Non autorisé'], 403);
            }
            if (!$targetId) jsonResponse(['error' => 'ID requis'], 400);
            if ($targetId === $currentId) jsonResponse(['error' => 'Vous ne pouvez pas vous suspendre vous-même'], 400);

            $stmt = $db->prepare("SELECT est_actif FROM utilisateurs WHERE id = ?");
            $stmt->execute([$targetId]);
            $cur = $stmt->fetchColumn();
            if ($cur === false) jsonResponse(['error' => 'Utilisateur non trouvé'], 404);

            $newVal = $cur ? 0 : 1;
            $db->prepare("UPDATE utilisateurs SET est_actif = ? WHERE id = ?")
               ->execute([$newVal, $targetId]);

            if ($newVal) {
                ajouterNotification($targetId, 'compte_active', 'Compte activé', 'Votre compte a été activé par un administrateur.', 'user', $targetId);
            } else {
                ajouterNotification($targetId, 'compte_suspendu', 'Compte suspendu', 'Votre compte a été suspendu par un administrateur.', 'user', $targetId);
            }

            jsonResponse(['success' => true, 'est_actif' => (bool)$newVal, 'message' => $newVal ? 'Compte activé' : 'Compte suspendu']);
        }

        // ─── Admin: promote user to admin ─────────────────
        if ($action === 'promote_admin') {
            if ($_SESSION['user_role'] !== 'administrateur') {
                jsonResponse(['error' => 'Non autorisé'], 403);
            }
            if (!$targetId) jsonResponse(['error' => 'ID requis'], 400);

            $stmt = $db->prepare("SELECT role FROM utilisateurs WHERE id = ?");
            $stmt->execute([$targetId]);
            $curRole = $stmt->fetchColumn();
            if ($curRole === false) jsonResponse(['error' => 'Utilisateur non trouvé'], 404);
            if ($curRole === 'administrateur') jsonResponse(['error' => 'Cet utilisateur est déjà administrateur'], 400);

            $db->prepare("UPDATE utilisateurs SET role = 'administrateur' WHERE id = ?")
               ->execute([$targetId]);

            jsonResponse(['success' => true, 'message' => 'Utilisateur promu administrateur']);
        }

        // ─── Admin: demote admin back to stagiaire ──────────
        if ($action === 'demote_admin') {
            if ($_SESSION['user_role'] !== 'administrateur') {
                jsonResponse(['error' => 'Non autorisé'], 403);
            }
            if (!$targetId) jsonResponse(['error' => 'ID requis'], 400);
            if ($targetId === $currentId) jsonResponse(['error' => 'Vous ne pouvez pas vous rétrograder vous-même'], 400);

            $stmt = $db->prepare("SELECT role FROM utilisateurs WHERE id = ?");
            $stmt->execute([$targetId]);
            $curRole = $stmt->fetchColumn();
            if ($curRole === false) jsonResponse(['error' => 'Utilisateur non trouvé'], 404);
            if ($curRole !== 'administrateur') jsonResponse(['error' => 'Cet utilisateur n\'est pas administrateur'], 400);

            // No history of previous role stored — reset to stagiaire by default
            $db->prepare("UPDATE utilisateurs SET role = 'stagiaire' WHERE id = ?")
               ->execute([$targetId]);

            jsonResponse(['success' => true, 'message' => 'Administrateur rétrogradé au rôle Stagiaire']);
        }

        if ($targetId !== $currentId && $_SESSION['user_role'] !== 'administrateur') {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }

        $data = jsonBody();
        if (!$data) jsonResponse(['error' => 'Données invalides'], 400);

        $fields = [];
        $params = [];
        $allowed = ['prenom', 'nom', 'filiere', 'disponible', 'bio', 'photo'];
        foreach ($allowed as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (empty($fields)) jsonResponse(['error' => 'Aucun champ à modifier'], 400);

        $params[] = $targetId;
        try {
            $db->prepare('UPDATE utilisateurs SET ' . implode(', ', $fields) . ' WHERE id = ?')
               ->execute($params);
        } catch (\PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la mise à jour'], 500);
        }

        jsonResponse(['success' => true, 'message' => 'Profil mis à jour']);

    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
