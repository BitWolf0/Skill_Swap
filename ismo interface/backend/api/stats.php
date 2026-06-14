<?php
/**
 * ISMO-SkillSwap — Statistics & Dashboard API
 * GET /api/stats.php?dashboard=user    — user dashboard data
 * GET /api/stats.php?dashboard=admin   — platform stats (admin)
 * GET /api/stats.php?dashboard=mentor  — mentor stats
 * GET /api/stats.php?dashboard=formateur — formateur stats
 * GET /api/stats.php?top=mentors       — top mentors ranking
 */
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

$db = Database::getInstance();
$userId = (int)$_SESSION['user_id'];
$dashboard = $_GET['dashboard'] ?? '';
$top = $_GET['top'] ?? '';

// ─── Top mentors ───
if ($top === 'mentors') {
    $stmt = $db->prepare("
        SELECT u.id AS user_id, u.prenom AS first_name, u.nom AS last_name, u.photo AS profile_picture_url,
               u.points_gamification AS reputation_score, u.points_gamification AS skill_points,
               (SELECT COUNT(*) FROM propositions_aide p WHERE p.proposant_id = u.id AND p.statut = 'Acceptée') as helps_count,
               (SELECT ROUND(AVG(d.note_mentor), 1) FROM demandes_aide d WHERE d.mentor_id = u.id AND d.note_mentor IS NOT NULL) as avg_rating,
               (SELECT COUNT(*) FROM badges_stagiaire bs WHERE bs.utilisateur_id = u.id) as badges_count
        FROM utilisateurs u
        WHERE u.role = 'mentor' AND u.est_actif = 1
        ORDER BY u.points_gamification DESC
        LIMIT 20
    ");
    $stmt->execute();
    jsonResponse($stmt->fetchAll());
}

switch ($dashboard) {
    case 'user':
        $data = [
            'user_id' => $userId,
            'reputation_score' => 0,
            'skill_points' => 0,
            'skills_count' => 0,
            'verified_skills_count' => 0,
            'requests_count' => 0,
            'accepted_responses_count' => 0,
            'badges_count' => 0,
            'unread_notifications_count' => 0,
        ];

        $u = $db->prepare('SELECT points_gamification FROM utilisateurs WHERE id = ?');
        $u->execute([$userId]);
        $user = $u->fetch();
        if ($user) {
            $data['reputation_score'] = (int)$user['points_gamification'];
            $data['skill_points'] = (int)$user['points_gamification'];
        }

        $c = $db->prepare('SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = ?');
        $c->execute([$userId]); $data['skills_count'] = (int)$c->fetchColumn();

        $c = $db->prepare("SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = ? AND statut_validation = 'Validé'");
        $c->execute([$userId]); $data['verified_skills_count'] = (int)$c->fetchColumn();

        $c = $db->prepare("SELECT COUNT(*) FROM demandes_aide WHERE auteur_id = ?");
        $c->execute([$userId]); $data['requests_count'] = (int)$c->fetchColumn();

        $c = $db->prepare("SELECT COUNT(*) FROM propositions_aide WHERE proposant_id = ? AND statut = 'Acceptée'");
        $c->execute([$userId]); $data['accepted_responses_count'] = (int)$c->fetchColumn();

        $c = $db->prepare('SELECT COUNT(*) FROM badges_stagiaire WHERE utilisateur_id = ?');
        $c->execute([$userId]); $data['badges_count'] = (int)$c->fetchColumn();

        // Recent requests
        $recent = $db->prepare("SELECT d.*, c.nom AS skill_name FROM demandes_aide d JOIN competences_catalogue c ON d.competence_id = c.id WHERE d.auteur_id = ? ORDER BY d.creee_le DESC LIMIT 5");
        $recent->execute([$userId]);
        $data['recent_requests'] = $recent->fetchAll();

        jsonResponse($data);

    case 'admin':
        requireRole(ROLE_ADMIN);
        $stats = [];

        $c = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
        $stats['total_users'] = (int)$c;

        $c = $db->query("SELECT COUNT(*) FROM demandes_aide")->fetchColumn();
        $stats['total_requests'] = (int)$c;

        $c = $db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'Résolu'")->fetchColumn();
        $stats['resolved_requests'] = (int)$c;

        // Users by role
        $byRole = $db->prepare("SELECT role, COUNT(*) as count FROM utilisateurs GROUP BY role");
        $byRole->execute();
        $stats['users_by_role'] = $byRole->fetchAll();

        // Recent registrations
        $recent = $db->prepare("SELECT id AS user_id, prenom AS first_name, nom AS last_name, email, role, date_inscription AS created_at FROM utilisateurs ORDER BY date_inscription DESC LIMIT 10");
        $recent->execute();
        $stats['recent_users'] = $recent->fetchAll();

        // Skills by category
        $cats = $db->prepare("SELECT categorie AS skill_category, COUNT(*) as count FROM competences_catalogue GROUP BY categorie");
        $cats->execute();
        $stats['skills_by_category'] = $cats->fetchAll();

        jsonResponse($stats);

    case 'mentor':
        $stmt = $db->prepare("
            SELECT
                (SELECT COUNT(*) FROM propositions_aide WHERE proposant_id = ?) as total_responses,
                (SELECT COUNT(*) FROM propositions_aide WHERE proposant_id = ? AND statut = 'Acceptée') as accepted_responses,
                (SELECT ROUND(AVG(d.note_mentor), 1) FROM demandes_aide d WHERE d.mentor_id = ? AND d.note_mentor IS NOT NULL) as avg_rating,
                (SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = ? AND statut_validation = 'Validé') as verified_skills,
                0 as total_offers
        ");
        $stmt->execute([$userId, $userId, $userId, $userId]);
        jsonResponse($stmt->fetch() ?: []);

    case 'formateur':
        requireRole(ROLE_FORMATEUR);
        $stmt = $db->prepare("
            SELECT
                (SELECT COUNT(*) FROM competences_stagiaire WHERE statut_validation = 'En attente') as pending_validations,
                (SELECT COUNT(*) FROM competences_stagiaire WHERE validateur_id = ? AND statut_validation != 'En attente') as total_validations,
                (SELECT COUNT(*) FROM utilisateurs WHERE role IN ('stagiaire','mentor') AND est_actif = 1) as active_stagiaires,
                (SELECT COUNT(*) FROM mentor_applications WHERE statut = 'En attente') as pending_mentor_apps,
                (SELECT COUNT(*) FROM demandes_aide WHERE statut != 'Résolu') as open_requests
        ");
        $stmt->execute([$userId]);
        jsonResponse($stmt->fetch() ?: []);

    default:
        jsonResponse(['error' => 'Dashboard non spécifié'], 400);
}
