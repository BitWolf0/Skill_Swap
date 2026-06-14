<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole(ROLE_ADMIN);

$pageTitle = 'ISMO-SkillSwap — Statistiques Admin';
$currentPage = 'statistiques_admin';
$basePath = '..';

$db = Database::getInstance();

$totalUsers = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalStagiaires = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'stagiaire'")->fetchColumn();
$totalFormateurs = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'formateur'")->fetchColumn();
$totalAdmins = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'administrateur'")->fetchColumn();
$totalSkills = $db->query("SELECT COUNT(*) FROM competences_catalogue WHERE est_active = 1")->fetchColumn();
$totalDeclarations = $db->query("SELECT COUNT(*) FROM competences_stagiaire")->fetchColumn();
$totalValidated = $db->query("SELECT COUNT(*) FROM competences_stagiaire WHERE statut_validation = 'Validé'")->fetchColumn();
$totalDemandes = $db->query("SELECT COUNT(*) FROM demandes_aide")->fetchColumn();
$totalResolved = $db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'Résolu'")->fetchColumn();
$totalPropositions = $db->query("SELECT COUNT(*) FROM propositions_aide")->fetchColumn();
$totalBadges = $db->query("SELECT COUNT(*) FROM badges WHERE est_actif = 1")->fetchColumn();
$totalBadgesAttribues = $db->query("SELECT COUNT(*) FROM badges_stagiaire")->fetchColumn();

$recentBadges = $db->query("SELECT b.nom, CONCAT(u.prenom, ' ', u.nom) as user_name, bs.obtenu_le FROM badges_stagiaire bs JOIN badges b ON bs.badge_id = b.id JOIN utilisateurs u ON bs.utilisateur_id = u.id ORDER BY bs.obtenu_le DESC LIMIT 10")->fetchAll();

$topSkills = $db->query("SELECT cc.nom, COUNT(cs.id) as declared FROM competences_catalogue cc LEFT JOIN competences_stagiaire cs ON cc.id = cs.competence_id WHERE cc.est_active = 1 GROUP BY cc.id ORDER BY declared DESC LIMIT 10")->fetchAll();

$topRequested = $db->query("SELECT cc.nom, COUNT(d.id) as requested FROM competences_catalogue cc LEFT JOIN demandes_aide d ON cc.id = d.competence_id GROUP BY cc.id ORDER BY requested DESC LIMIT 10")->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Statistiques de la Plateforme</h1>
      </div>
    </div>

    <div class="quick-stats">
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalUsers ?></span><span class="qstat-label">Total utilisateurs</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalStagiaires ?></span><span class="qstat-label">Stagiaires</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalFormateurs ?></span><span class="qstat-label">Formateurs</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalAdmins ?></span><span class="qstat-label">Admins</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalSkills ?></span><span class="qstat-label">Compétences</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalDeclarations ?></span><span class="qstat-label">Déclarations</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalValidated ?></span><span class="qstat-label">Validées</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalDemandes ?></span><span class="qstat-label">Demandes</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalResolved ?></span><span class="qstat-label">Résolues</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalPropositions ?></span><span class="qstat-label">Propositions</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalBadges ?></span><span class="qstat-label">Badges actifs</span></div></div>
      <div class="qstat-card"><div class="qstat-info"><span class="qstat-value"><?= $totalBadgesAttribues ?></span><span class="qstat-label">Badges attribués</span></div></div>
    </div>

    <h2 class="section-label">Top 10 des compétences déclarées</h2>
    <table class="data-table">
      <thead><tr><th>Compétence</th><th>Déclarations</th></tr></thead>
      <tbody>
        <?php foreach ($topSkills as $ts): ?>
        <tr><td><?= h($ts['nom']) ?></td><td><?= (int)$ts['declared'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h2 class="section-label">Top 10 des compétences les plus demandées</h2>
    <table class="data-table">
      <thead><tr><th>Compétence</th><th>Demandes</th></tr></thead>
      <tbody>
        <?php foreach ($topRequested as $tr): ?>
        <tr><td><?= h($tr['nom']) ?></td><td><?= (int)$tr['requested'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h2 class="section-label">Derniers badges attribués</h2>
    <table class="data-table">
      <thead><tr><th>Badge</th><th>Utilisateur</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($recentBadges as $rb): ?>
        <tr><td><?= h($rb['nom']) ?></td><td><?= h($rb['user_name']) ?></td><td><?= date('d/m/Y', strtotime($rb['obtenu_le'])) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
