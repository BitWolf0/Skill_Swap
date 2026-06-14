<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole([ROLE_FORMATEUR, ROLE_ADMIN]);

$pageTitle = 'ISMO-SkillSwap — Recherche';
$currentPage = 'recherche';
$basePath = '..';

$db = Database::getInstance();

$query = $_GET['q'] ?? '';
$users = [];
$skills = [];

if ($query) {
    $t = "%$query%";
    $stmt = $db->prepare("SELECT id, prenom, nom, email, role, points_gamification FROM utilisateurs WHERE prenom LIKE ? OR nom LIKE ? OR email LIKE ? ORDER BY points_gamification DESC LIMIT 20");
    $stmt->execute([$t, $t, $t]);
    $users = $stmt->fetchAll();

    $stmt = $db->prepare("SELECT * FROM competences_catalogue WHERE est_active = 1 AND (nom LIKE ? OR categorie LIKE ?) LIMIT 20");
    $stmt->execute([$t, $t]);
    $skills = $stmt->fetchAll();
}

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_formateur.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Recherche</h1>
      </div>
    </div>
    <form method="get" class="search-form">
      <input type="search" name="q" class="form-input" placeholder="Rechercher un utilisateur ou une compétence..." value="<?= h($query) ?>" />
      <button type="submit" class="btn-publish">Rechercher</button>
    </form>

    <?php if ($query): ?>
      <h2>Utilisateurs (<?= count($users) ?>)</h2>
      <?php foreach ($users as $u): ?>
      <div class="result-item"><strong><?= h($u['prenom'] . ' ' . $u['nom']) ?></strong> — <?= h($u['email']) ?> (<?= roleLabel($u['role']) ?>) <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:text-bottom;margin:0 2px"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 8 6 8 6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 16 6 16 6"/><path d="M4 22h16"/><path d="M12 22V12"/><path d="M12 12c-2 0-4-2-4-6V2h8v4c0 4-2 6-4 6z"/></svg><?= (int)$u['points_gamification'] ?></div>
      <?php endforeach; ?>
      <h2>Compétences (<?= count($skills) ?>)</h2>
      <?php foreach ($skills as $s): ?>
      <div class="result-item"><strong><?= h($s['nom']) ?></strong> — <?= h($s['categorie'] ?? 'Générale') ?></div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
