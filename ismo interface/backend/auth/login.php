<?php
/**
 * ISMO-SkillSwap v4 — Login Endpoint
 * POST: email, password
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Méthode non autorisée'], 405);
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = !empty($_POST['remember']);

if (empty($email) || empty($password)) {
    jsonResponse(['error' => 'Email et mot de passe requis'], 400);
}

$db = Database::getInstance();

$stmt = $db->prepare('SELECT * FROM utilisateurs WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['mot_de_passe'])) {
    jsonResponse(['error' => 'Email ou mot de passe incorrect'], 401);
}

// Check if account is active
if (isset($user['est_actif']) && !$user['est_actif']) {
    if ($user['derniere_connexion'] === null) {
        jsonResponse(['error' => 'Votre compte est en attente de validation par un administrateur.'], 403);
    }
    jsonResponse(['error' => 'Votre compte a été suspendu. Contactez un administrateur.'], 403);
}

// Regenerate session to prevent fixation
session_regenerate_id(true);

// Set session
$_SESSION['user_id']    = (int)$user['id'];
$_SESSION['user_role']  = $user['role'];
$_SESSION['user_nom']   = $user['nom'];
$_SESSION['user_prenom']= $user['prenom'];
$_SESSION['user_email'] = $user['email'];

// Update last connexion
$db->prepare('UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?')
   ->execute([$user['id']]);

// Redirect target based on role
$redirect = match ($user['role']) {
    'stagiaire'      => BASE_URL . '/pages_stagiaire/dashboard.php',
    'mentor'         => BASE_URL . '/pages_mentor/dashboard.php',
    'formateur'      => BASE_URL . '/formateur_pages/tableau_de_bord.php',
    'administrateur' => BASE_URL . '/pages_admin/tableau_de_bord.php',
    default          => BASE_URL . '/pages_stagiaire/dashboard.php',
};

jsonResponse([
    'success'  => true,
    'user'     => [
        'id'     => (int)$user['id'],
        'nom'    => $user['nom'],
        'prenom' => $user['prenom'],
        'email'  => $user['email'],
        'role'   => $user['role'],
    ],
    'redirect' => $redirect,
]);
