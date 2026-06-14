<?php
/**
 * LEGACY — This endpoint has been replaced by backend/api/competences.php (v4).
 * 
 * The old skills / users schema no longer exists.
 * Consult backend/api/competences.php for the current skills catalogue + declaration API.
 */
require_once __DIR__ . '/../config.php';
http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'error' => 'Cette API est obsolète. Utilisez /api/competences.php à la place.',
    'code'  => 'GONE',
], JSON_UNESCAPED_UNICODE);
exit;
