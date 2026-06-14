<?php
/**
 * Migration: Award points for existing validated competences
 * Run: php backend/migrate_points.php
 */
define('APP_RUNNING', true);
require_once __DIR__ . '/Database.php';

$db = Database::getInstance();

echo "=== Point Migration ===\n\n";

// Get all users with validated skills
$stmt = $db->query("
    SELECT u.id, u.prenom, u.nom, u.points_gamification,
           (SELECT COUNT(*) FROM competences_stagiaire cs 
            WHERE cs.utilisateur_id = u.id AND cs.statut_validation = 'Validé') AS validated_count
    FROM utilisateurs u
    HAVING validated_count > 0
    ORDER BY u.id
");
$users = $stmt->fetchAll();

if (empty($users)) {
    echo "No users with validated skills found.\n";
} else {
    echo "Awarding points for validated skills:\n";
    foreach ($users as $u) {
        $expected = (int)$u['validated_count'] * 10;
        $current = (int)$u['points_gamification'];
        $diff = $expected - $current;
        echo "  [{$u['id']}] {$u['prenom']} {$u['nom']}: {$u['validated_count']} skill(s) = {$expected}pts expected, {$current}pts current";
        if ($diff > 0) {
            $db->prepare('UPDATE utilisateurs SET points_gamification = points_gamification + ? WHERE id = ?')
               ->execute([$diff, $u['id']]);
            echo " → awarded {$diff}pts";
        } elseif ($diff === 0) {
            echo " → already correct";
        }
        echo "\n";
    }
}

// Now check auto-badges for all users
require_once __DIR__ . '/functions.php';

echo "\nChecking auto-badges for all users...\n";
$allStmt = $db->query("SELECT id, prenom, nom FROM utilisateurs ORDER BY id");
$allUsers = $allStmt->fetchAll();
foreach ($allUsers as $u) {
    $uid = (int)$u['id'];
    verifierBadgesAutomatiques($uid);
    echo "  [{$uid}] {$u['prenom']} {$u['nom']} → badges checked\n";
}

echo "\n=== Final State ===\n";
$finalStmt = $db->query("
    SELECT id, prenom, nom, role, points_gamification 
    FROM utilisateurs ORDER BY role, points_gamification DESC
");
echo sprintf("%-4s %-16s %-16s %-14s %s\n", 'ID', 'Prénom', 'Nom', 'Rôle', 'Points');
echo str_repeat('-', 66) . "\n";
while ($u = $finalStmt->fetch()) {
    echo sprintf("%-4d %-16s %-16s %-14s %d\n", $u['id'], $u['prenom'], $u['nom'], $u['role'], $u['points_gamification']);
}

echo "\nBadges awarded:\n";
$badgesStmt = $db->query("
    SELECT bs.utilisateur_id, u.prenom, u.nom, b.nom AS badge_nom, bs.obtenu_le
    FROM badges_stagiaire bs
    JOIN badges b ON bs.badge_id = b.id
    JOIN utilisateurs u ON bs.utilisateur_id = u.id
    ORDER BY bs.utilisateur_id, bs.obtenu_le
");
$badges = $badgesStmt->fetchAll();
if (empty($badges)) {
    echo "  (none)\n";
} else {
    foreach ($badges as $b) {
        echo "  [{$b['utilisateur_id']}] {$b['prenom']} {$b['nom']} → {$b['badge_nom']} ({$b['obtenu_le']})\n";
    }
}

echo "\nDone.\n";
