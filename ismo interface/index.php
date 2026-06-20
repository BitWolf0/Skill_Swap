<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'] ?? 'stagiaire';
    $redirect = match ($role) {
        'mentor' => 'pages_mentor/dashboard.php',
        'formateur' => 'formateur_pages/tableau_de_bord.php',
        'administrateur' => 'pages_admin/tableau_de_bord.php',
        default => 'pages_stagiaire/dashboard.php',
    };
    header('Location: ' . $redirect);
    exit;
}
header('Location: login.php');
exit;
