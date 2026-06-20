<?php
require_once __DIR__ . '/../backend/config.php';
if (isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: ../login.php');
}
exit;
