<?php
/**
 * Minimal router for PHP dev server (php -S).
 * Serves static assets directly, routes PHP requests normally.
 * Mock Database.php handles DB queries so pages render offline.
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$fullPath = __DIR__ . $path;

// Serve static files directly
if ($path !== '/' && file_exists($fullPath) && !preg_match('/\.php$/i', $path)) {
    return false;
}

// Pre-set session for auth bypass
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 86400, 'path' => '/']);
    session_start();
}

// Route PHP files
$pathClean = strtok($path, '?');
$targetFile = __DIR__ . $pathClean;

if (file_exists($targetFile) && preg_match('/\.php$/i', $pathClean)) {
    $pageName = basename($pathClean, '.php');

    // Set auth session for all pages EXCEPT login/inscription
    if (!in_array($pageName, ['login', 'inscription'], true)) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = match(true) {
            str_contains($pathClean, 'pages_admin') => 'administrateur',
            str_contains($pathClean, 'formateur_pages') => 'formateur',
            str_contains($pathClean, 'pages_mentor') => 'mentor',
            default => 'stagiaire',
        };
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
    }

    $currentPage = $pageName;
    $basePath = '..';
    require $targetFile;
} else {
    http_response_code(404);
    echo '404 - ' . htmlspecialchars($pathClean);
}
