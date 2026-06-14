<?php
/**
 * ISMO-SkillSwap v4 — Shared Helper Functions
 */
if (!defined('APP_RUNNING')) { http_response_code(403); die('Accès direct interdit'); }

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication — redirect if not logged in
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages_stagiaire/login.php');
        exit;
    }
}

/**
 * Require specific role(s)
 * Accepts: 'stagiaire', 'formateur', 'administrateur', or array thereof
 */
function requireRole(array|string $roles): void {
    requireAuth();
    $userRole = $_SESSION['user_role'] ?? '';
    $roles    = (array)$roles;
    if (!in_array($userRole, $roles)) {
        $dashboardMap = [
            'stagiaire'      => 'pages_stagiaire/dashboard.php',
            'formateur'      => 'formateur_pages/tableau_de_bord.php',
            'administrateur' => 'pages_admin/tableau_de_bord.php',
        ];
        $target = $dashboardMap[$userRole] ?? 'pages_stagiaire/login.php';
        header('Location: ' . BASE_URL . '/' . $target);
        exit;
    }
}

/**
 * Get current logged-in user data (v4 schema)
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db   = Database::getInstance();
    $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

/**
 * Get any user by ID (v4 schema)
 */
function getUserById(int $userId): ?array {
    $db   = Database::getInstance();
    $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE id = ?');
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: null;
}

/**
 * Check if a user (stagiaire) qualifies as a mentor
 * Mentor = stagiaire with at least one validated skill OR
 *          points_gamification >= 10
 */
function estMentor(int $userId): bool {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) FROM mentor_applications WHERE utilisateur_id = ? AND statut = 'Approuvé'");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Redirect with optional flash message
 */
function redirect(string $url, ?string $message = null, string $type = 'info'): void {
    if ($message) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
    header("Location: $url");
    exit;
}

/**
 * Sanitize output string
 */
function h(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrf(string $token): bool {
    $expected = $_SESSION['csrf_token'] ?? '';
    if (empty($expected) || empty($token)) return false;
    return hash_equals($expected, $token);
}

function csrfField(): string {
    return '<input type="hidden" name="_token" value="' . csrfToken() . '" />';
}

/**
 * Require valid CSRF token for state-changing requests
 */
function requireCsrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') return;
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['_token'] ?? '';
    if (!verifyCsrf($token)) {
        jsonResponse(['error' => 'Token CSRF invalide'], 403);
    }
}

/**
 * Return JSON response
 */
function jsonResponse(mixed $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get JSON from request body
 */
function jsonBody(): ?array {
    $raw = file_get_contents('php://input');
    return $raw ? json_decode($raw, true) : null;
}

/**
 * Get role display name in French (v4)
 */
function roleLabel(string $role): string {
    return match ($role) {
        'stagiaire'      => 'Stagiaire',
        'mentor'         => 'Mentor',
        'formateur'      => 'Formateur',
        'administrateur' => 'Administrateur',
        default          => $role,
    };
}

/**
 * Add gamification points to a user and auto-assign badges
 */
/**
 * Calculate user level based on gamification points (v4)
 * Level 1: 0-19 pts, Level 2: 20-49, Level 3: 50-99, Level 4: 100-199, Level 5: 200+
 */
function getUserLevel(int $points): array {
    $levels = [
        1 => ['min' => 0,   'max' => 19,  'name' => 'Débutant'],
        2 => ['min' => 20,  'max' => 49,  'name' => 'Apprenti'],
        3 => ['min' => 50,  'max' => 99,  'name' => 'Confirmé'],
        4 => ['min' => 100, 'max' => 199, 'name' => 'Expert'],
        5 => ['min' => 200, 'max' => PHP_INT_MAX, 'name' => 'Maître'],
    ];
    $level = 1;
    foreach ($levels as $num => $def) {
        if ($points >= $def['min']) $level = $num;
    }
    $def = $levels[$level];
    $nextDef = $levels[$level + 1] ?? null;
    $progress = $nextDef
        ? min(100, max(0, ($points - $def['min']) * 100 / max(1, $nextDef['min'] - $def['min'])))
        : 100;
    return [
        'level'       => $level,
        'name'        => $def['name'],
        'points'      => $points,
        'current_min' => $def['min'],
        'next_min'    => $nextDef['min'] ?? $def['min'],
        'progress'    => $progress,
    ];
}

/**
 * Create a notification record for a user
 */
function ajouterNotification(int $userId, string $type, string $titre, string $message, ?string $refType = null, ?int $refId = null): void {
    $db = Database::getInstance();
    $stmt = $db->prepare('INSERT INTO notifications (user_id, notification_type, title, message, reference_type, reference_id) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $type, $titre, $message, $refType, $refId]);
}

function ajouterPoints(int $userId, int $points, string $raison = ''): void {
    $db = Database::getInstance();
    $db->prepare('UPDATE utilisateurs SET points_gamification = points_gamification + ? WHERE id = ?')
       ->execute([$points, $userId]);
    verifierBadgesAutomatiques($userId);
}

/**
 * Auto-assign badges based on gamification points threshold
 */
function verifierBadgesAutomatiques(int $userId): void {
    $db = Database::getInstance();

    $stmt = $db->prepare('SELECT points_gamification FROM utilisateurs WHERE id = ?');
    $stmt->execute([$userId]);
    $points = (int)$stmt->fetchColumn();

    $badges = $db->query('SELECT * FROM badges WHERE points_requis > 0 AND est_actif = 1')->fetchAll();

    foreach ($badges as $badge) {
        if ($points < (int)$badge['points_requis']) continue;

        $check = $db->prepare('SELECT id FROM badges_stagiaire WHERE utilisateur_id = ? AND badge_id = ?');
        $check->execute([$userId, $badge['id']]);
        if ($check->fetch()) continue;

        $db->prepare('INSERT INTO badges_stagiaire (utilisateur_id, badge_id, attribue_par, motif) VALUES (?, ?, "système", ?)')
           ->execute([$userId, $badge['id'], 'Badge débloqué automatiquement']);
        ajouterNotification($userId, 'badge', 'Badge débloqué !', "Vous avez atteint {$badge['points_requis']} points et débloqué le badge « {$badge['nom']} »", 'badge', (int)$badge['id']);
    }
}
