<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole([ROLE_FORMATEUR, ROLE_ADMIN]);

$pageTitle = 'ISMO-SkillSwap — Statistiques';
$currentPage = 'statistique';
$basePath = '..';

$db = Database::getInstance();

$totalUsers = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalSkills = $db->query("SELECT COUNT(*) FROM competences_catalogue WHERE est_active = 1")->fetchColumn();
$totalDeclarations = $db->query("SELECT COUNT(*) FROM competences_stagiaire")->fetchColumn();
$totalValidated = $db->query("SELECT COUNT(*) FROM competences_stagiaire WHERE statut_validation = 'Validé'")->fetchColumn();
$totalDemandes = $db->query("SELECT COUNT(*) FROM demandes_aide")->fetchColumn();
$totalResolved = $db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'Résolu'")->fetchColumn();

$skillsByCat = $db->query("SELECT categorie, COUNT(*) as count FROM competences_catalogue WHERE est_active = 1 GROUP BY categorie ORDER BY count DESC")->fetchAll();

$regs = $db->query("SELECT DATE_FORMAT(date_inscription, '%Y-%m') as month, COUNT(*) as count FROM utilisateurs WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month")->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_formateur.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Statistiques</h1>
      </div>
    </div>
    <div class="quick-stats">
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalUsers ?></span><span class="qstat-label">Utilisateurs</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalDeclarations ?></span><span class="qstat-label">Déclarations</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalValidated ?></span><span class="qstat-label">Validées</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalSkills ?></span><span class="qstat-label">Compétences</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalDemandes ?></span><span class="qstat-label">Demandes</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalResolved ?></span><span class="qstat-label">Résolues</span></div></div>
    </div>

    <h2 class="section-label">Compétences par catégorie</h2>
    <table class="data-table">
      <thead><tr><th>Catégorie</th><th>Nombre</th></tr></thead>
      <tbody>
        <?php foreach ($skillsByCat as $s): ?>
        <tr><td><?= h($s['categorie'] ?: 'Non catégorisé') ?></td><td><?= (int)$s['count'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h2 class="section-label">Inscriptions (6 derniers mois)</h2>
    <table class="data-table">
      <thead><tr><th>Mois</th><th>Inscriptions</th></tr></thead>
      <tbody>
        <?php foreach ($regs as $r): ?>
        <tr><td><?= h($r['month']) ?></td><td><?= (int)$r['count'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>
<style>
.data-table { width: 100%; border-collapse: collapse; background: var(--white); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); }
.data-table th { background: var(--gray-50); padding: 12px 16px; text-align: left; font-weight: 600; font-size: 0.85rem; color: var(--gray-600); border-bottom: 1px solid var(--gray-200); }
.data-table td { padding: 12px 16px; border-bottom: 1px solid var(--gray-100); font-size: 0.9rem; }
.data-table tbody tr:hover { background: var(--gray-50); }
</style>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
