<?php
/**
 * ISMO-SkillSwap v4 — Registration Endpoint
 * POST: nom, prenom, email, password, role
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Méthode non autorisée'], 405);
}

requireCsrf();

$nom      = trim($_POST['nom'] ?? '');
$prenom   = trim($_POST['prenom'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = trim($_POST['role'] ?? 'stagiaire');
$filiere  = trim($_POST['filiere'] ?? '');

// Validate required fields
if (empty($nom) || empty($prenom)) {
    jsonResponse(['error' => 'Nom et prénom requis'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['error' => 'Email invalide'], 400);
}
if ($role !== 'administrateur' && !str_ends_with($email, '@ofppt-edu.ma')) {
    jsonResponse(['error' => 'Seuls les emails @ofppt-edu.ma sont autorisés'], 400);
}
if (strlen($password) < 8) {
    jsonResponse(['error' => 'Le mot de passe doit contenir au moins 8 caractères'], 400);
}

// Validate role
$allowedRoles = ['stagiaire', 'formateur', 'administrateur'];
if (!in_array($role, $allowedRoles)) {
    $role = 'stagiaire';
}

$db = Database::getInstance();

// Check email uniqueness
$stmt = $db->prepare('SELECT id FROM utilisateurs WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonResponse(['error' => 'Cet email est déjà utilisé'], 409);
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Insert user (inactive — requires admin validation)
$stmt = $db->prepare('
    INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, filiere, est_actif)
    VALUES (?, ?, ?, ?, ?, ?, 0)
');
$stmt->execute([$nom, $prenom, $email, $passwordHash, $role, $filiere ?: null]);

jsonResponse([
    'success'  => true,
    'message'  => 'Votre compte a été créé. Un administrateur doit valider votre inscription avant de pouvoir vous connecter.',
], 201);
