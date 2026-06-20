<?php
/**
 * ISMO-SkillSwap v4 — Logout
 */
require_once __DIR__ . '/../config.php';

$_SESSION = [];
session_destroy();

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

header('Location: ' . BASE_URL . '/login.php');
exit;
