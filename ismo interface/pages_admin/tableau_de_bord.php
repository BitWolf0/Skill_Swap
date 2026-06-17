<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole(ROLE_ADMINISTRATEUR);

$pageTitle = 'ISMO-SkillSwap — Administration';
$currentPage = 'tableau_de_bord';
$basePath = '..';

$db = Database::getInstance();

$totalUsers = (int)$db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalStagiaires = (int)$db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'stagiaire'")->fetchColumn();
$totalFormateurs = (int)$db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'formateur'")->fetchColumn();
$totalAdmins = (int)$db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'administrateur'")->fetchColumn();

$totalSkills = (int)$db->query("SELECT COUNT(*) FROM competences_catalogue WHERE est_active = 1")->fetchColumn();
$declaredSkills = (int)$db->query("SELECT COUNT(*) FROM competences_stagiaire")->fetchColumn();
$validatedSkills = (int)$db->query("SELECT COUNT(*) FROM competences_stagiaire WHERE statut_validation = 'Validé'")->fetchColumn();
$pendingSkills = (int)$db->query("SELECT COUNT(*) FROM competences_stagiaire WHERE statut_validation = 'En attente'")->fetchColumn();

$totalRequests = (int)$db->query("SELECT COUNT(*) FROM demandes_aide")->fetchColumn();
$openRequests = (int)$db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'Ouvert'")->fetchColumn();
$resolvedRequests = (int)$db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'Résolu'")->fetchColumn();
$inProgress = (int)$db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'En cours'")->fetchColumn();

$totalBadges = (int)$db->query("SELECT COUNT(*) FROM badges WHERE est_actif = 1")->fetchColumn();
$assignedBadges = (int)$db->query("SELECT COUNT(*) FROM badges_stagiaire")->fetchColumn();

$totalPoints = (int)$db->query("SELECT COALESCE(SUM(points_gamification), 0) FROM utilisateurs")->fetchColumn();

$recentUsers = $db->query("SELECT id, prenom, nom, email, role, filiere, date_inscription FROM utilisateurs ORDER BY date_inscription DESC LIMIT 8")->fetchAll();

$totalPropositions = (int)$db->query("SELECT COUNT(*) FROM propositions_aide")->fetchColumn();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Tableau de bord Admin</h1>
        <p class="page-sub">Supervisez la plateforme — <?= $totalUsers ?> utilisateurs, <?= $validatedSkills ?> compétences validées</p>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?= $totalUsers ?></div>
        <div class="stat-label">Utilisateurs</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalStagiaires ?></div>
        <div class="stat-label">Stagiaires</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalFormateurs ?></div>
        <div class="stat-label">Formateurs</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $pendingSkills ?></div>
        <div class="stat-label">Validations en attente</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $validatedSkills ?></div>
        <div class="stat-label">Compétences validées</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalSkills ?></div>
        <div class="stat-label">Compétences au catalogue</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $openRequests ?></div>
        <div class="stat-label">Demandes ouvertes</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $resolvedRequests ?></div>
        <div class="stat-label">Demandes résolues</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalBadges ?></div>
        <div class="stat-label">Badges actifs</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $assignedBadges ?></div>
        <div class="stat-label">Badges attribués</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalPoints ?></div>
        <div class="stat-label">Points gamifiés (total)</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalPropositions ?></div>
        <div class="stat-label">Propositions d'aide</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $inProgress ?></div>
        <div class="stat-label">En cours</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalRequests ?></div>
        <div class="stat-label">Demandes totales</div>
      </div>
    </div>

    <div class="table-card">
      <h2 class="table-card-title">Derniers inscrits</h2>
      <div class="responsive-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Filière</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentUsers as $u): ?>
            <tr>
              <td class="cell-name"><?= h($u['prenom'] . ' ' . $u['nom']) ?></td>
              <td class="cell-muted"><?= h($u['email']) ?></td>
              <td class="cell-role"><?= roleLabel($u['role']) ?></td>
              <td class="cell-muted"><?= h($u['filiere'] ?? '-') ?></td>
              <td class="cell-date"><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
