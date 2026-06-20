<?php
require_once __DIR__ . '/../backend/config.php';
if (isLoggedIn()) {
    header('Location: tableau_de_bord.php');
} else {
    header('Location: ../login.php');
}
exit;
