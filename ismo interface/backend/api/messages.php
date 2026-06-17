<?php
declare(strict_types=1);
/**
 * ISMO-SkillSwap — Messaging API
 *
 * Messagerie non requise par le cc3.md.
 * Les notifications sont gérées via /api/notifications.php
 */
http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Messagerie non disponible. Utilisez les notifications.'], JSON_UNESCAPED_UNICODE);
