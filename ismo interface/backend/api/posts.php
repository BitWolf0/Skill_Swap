<?php
/**
 * LEGACY — This endpoint has been replaced by backend/api/demandes.php (v4).
 * 
 * The old skill_posts / users schema no longer exists.
 * Consult backend/api/demandes.php for the current help-request CRUD API.
 */
require_once __DIR__ . '/../config.php';
http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'error' => 'Cette API est obsolète. Utilisez /api/demandes.php à la place.',
    'code'  => 'GONE',
], JSON_UNESCAPED_UNICODE);
exit;
