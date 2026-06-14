<?php
require_once __DIR__ . '/../config.php';
requireAuth();
requireCsrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Méthode non autorisée'], 405);
}

$file = $_FILES['photo'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['error' => 'Aucun fichier ou erreur d\'upload'], 400);
}

$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowed)) {
    jsonResponse(['error' => 'Format autorisé : JPG, PNG, GIF, WebP'], 400);
}

$maxSize = 2 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    jsonResponse(['error' => 'Image trop volumineuse (max 2 Mo)'], 400);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
$filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
$uploadDir = __DIR__ . '/../../uploads/avatars/';
$dest = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    jsonResponse(['error' => 'Erreur lors de l\'enregistrement'], 500);
}

$photoUrl = '/uploads/avatars/' . $filename;

$db = Database::getInstance();
$db->prepare('UPDATE utilisateurs SET photo = ? WHERE id = ?')
   ->execute([$photoUrl, (int)$_SESSION['user_id']]);

jsonResponse(['success' => true, 'photo' => $photoUrl, 'message' => 'Photo mise à jour']);
