<?php
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$method = $_SERVER['REQUEST_METHOD'];
$sessionUserId = (int)$_SESSION['user_id'];
$sessionRole = $_SESSION['user_role'];
$action = $_GET['action'] ?? '';
$db = Database::getInstance();

switch ($method) {
    case 'POST':
        $data = jsonBody();

        // ─── Stagiaire applies to be mentor ─────────────────
        if ($action === 'postuler') {
            $motivation = trim($data['motivation'] ?? '');
            $experience = trim($data['experience'] ?? '');

            if (!$motivation) jsonResponse(['error' => 'La motivation est requise'], 400);

            // Check existing pending application
            $check = $db->prepare("SELECT id, statut FROM mentor_applications WHERE utilisateur_id = ? ORDER BY id DESC LIMIT 1");
            $check->execute([$sessionUserId]);
            $existing = $check->fetch();
            if ($existing && $existing['statut'] === 'En attente') {
                jsonResponse(['error' => 'Vous avez déjà une candidature en attente'], 409);
            }
            if ($existing && $existing['statut'] === 'Approuvé') {
                jsonResponse(['error' => 'Vous êtes déjà mentor'], 409);
            }

            $db->prepare("INSERT INTO mentor_applications (utilisateur_id, motivation, experience) VALUES (?, ?, ?)")
               ->execute([$sessionUserId, $motivation, $experience ?: null]);

            jsonResponse(['success' => true, 'message' => 'Candidature envoyée avec succès'], 201);
        }

        jsonResponse(['error' => 'Action inconnue'], 400);

    case 'GET':
        // ─── Admin/Formateur: list applications ────────────
        if (!in_array($sessionRole, ['formateur', 'administrateur'])) {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }

        $statutFilter = $_GET['statut'] ?? 'En attente';
        $stmt = $db->prepare("
            SELECT ma.*, u.nom, u.prenom, u.email, u.filiere, u.points_gamification,
                   (SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = ma.utilisateur_id AND statut_validation = 'Validé') as competences_validees
            FROM mentor_applications ma
            JOIN utilisateurs u ON ma.utilisateur_id = u.id
            WHERE ma.statut = ?
            ORDER BY ma.date_soumission DESC
        ");
        $stmt->execute([$statutFilter]);
        jsonResponse($stmt->fetchAll());

    case 'PUT':
        // ─── Admin/Formateur: approve/reject ───────────────
        if (!in_array($sessionRole, ['formateur', 'administrateur'])) {
            jsonResponse(['error' => 'Non autorisé'], 403);
        }

        $applicationId = (int)($_GET['id'] ?? 0);
        if (!$applicationId) jsonResponse(['error' => 'ID requis'], 400);

        $data = jsonBody();
        $newStatut = $data['statut'] ?? '';
        if (!in_array($newStatut, ['Approuvé', 'Refusé'])) {
            jsonResponse(['error' => 'Statut invalide'], 400);
        }

        $stmt = $db->prepare("SELECT utilisateur_id FROM mentor_applications WHERE id = ?");
        $stmt->execute([$applicationId]);
        $app = $stmt->fetch();
        if (!$app) jsonResponse(['error' => 'Candidature introuvable'], 404);

        $db->prepare("UPDATE mentor_applications SET statut = ?, date_traitement = NOW(), traite_par_id = ? WHERE id = ?")
           ->execute([$newStatut, $sessionUserId, $applicationId]);

        if ($newStatut === 'Approuvé') {
            $db->prepare("UPDATE utilisateurs SET role = 'mentor' WHERE id = ?")
               ->execute([$app['utilisateur_id']]);
            ajouterNotification((int)$app['utilisateur_id'], 'mentor_approuve', 'Félicitations !', 'Votre candidature mentor a été approuvée', 'mentor', $applicationId);
        } else {
            ajouterNotification((int)$app['utilisateur_id'], 'mentor_refuse', 'Candidature refusée', 'Votre candidature mentor a été refusée', 'mentor', $applicationId);
        }

        jsonResponse(['success' => true, 'message' => 'Candidature ' . ($newStatut === 'Approuvé' ? 'approuvée' : 'refusée')]);

    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
}
