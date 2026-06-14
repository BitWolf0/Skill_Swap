<?php
/**
 * ISMO-SkillSwap v4 — Database Connection (Legacy Bridge)
 * 
 * Provides the global $db variable for backwards compatibility
 * with pages still referencing $db directly.
 * New code should use Database::getInstance() instead.
 */
if (!defined('APP_RUNNING')) { http_response_code(403); die('Accès direct interdit'); }

$db = Database::getInstance();
