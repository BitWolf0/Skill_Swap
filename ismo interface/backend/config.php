<?php
declare(strict_types=1);
/**
 * ISMO-SkillSwap v4 — Application Configuration
 */
define('APP_RUNNING', true);

// Secure session configuration
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
session_set_cookie_params([
    'lifetime' => 86400, // 24h cookie lifetime
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

// Idle session timeout — 30 min inactivity
$idleTimeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $idleTimeout) {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . BASE_URL . '/pages_stagiaire/login.php?expired=1');
    exit;
}
$_SESSION['last_activity'] = time();

// Paths
define('BASE_PATH', dirname(__DIR__));
$baseUrl   = str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH);
$baseUrl   = rtrim(strtr($baseUrl, '\\', '/'), '/');
define('BASE_URL', $baseUrl);
define('SITE_NAME', 'ISMO-SkillSwap');

// Role constants
define('ROLE_STAGIAIRE',      'stagiaire');
define('ROLE_MENTOR',         'mentor');
define('ROLE_FORMATEUR',      'formateur');
define('ROLE_ADMINISTRATEUR', 'administrateur');
define('ROLE_ADMIN',          'administrateur'); // alias for old code

// NOTE: 'mentor' is stored as a role when a stagiaire's
// mentor application is approved by an admin or formateur.

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; frame-ancestors 'none';");

// Database connection (v4 — singleton PDO)
require_once __DIR__ . '/Database.php';

// Legacy bridge — provides $db for pages still using old global
require_once __DIR__ . '/db.php';

// Helper functions
require_once __DIR__ . '/functions.php';
