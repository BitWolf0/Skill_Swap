<?php
/**
 * LEGACY — This endpoint has been replaced by backend/api/propositions.php (v4).
 * 
 * The old skill_responses / users schema no longer exists.
 * Consult backend/api/propositions.php for the current proposal CRUD API.
 */
require_once __DIR__ . '/../config.php';
http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'error' => 'Cette API est obsolète. Utilisez /api/propositions.php à la place.',
    'code'  => 'GONE',
], JSON_UNESCAPED_UNICODE);
exit;
