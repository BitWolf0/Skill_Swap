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

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalUsers ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Utilisateurs</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalStagiaires ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Stagiaires</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalFormateurs ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Formateurs</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $pendingSkills ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Validations en attente</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $validatedSkills ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Compétences validées</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalSkills ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Compétences au catalogue</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $openRequests ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Demandes ouvertes</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $resolvedRequests ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Demandes résolues</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalBadges ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Badges actifs</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $assignedBadges ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Badges attribués</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalPoints ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Points gamifiés (total)</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalPropositions ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Propositions d'aide</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $inProgress ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">En cours</div>
      </div>
      <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);display:flex;flex-direction:column;gap:4px;">
        <div style="font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.2;"><?= $totalRequests ?></div>
        <div style="font-size:0.82rem;font-weight:500;color:#475569;line-height:1.4;">Demandes totales</div>
      </div>
    </div>

    <div style="background:#fff;padding:24px;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08);">
      <h2 style="font-size:1.05rem;font-weight:700;color:#1e293b;margin:0 0 16px 0;">Derniers inscrits</h2>
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
          <thead>
            <tr style="background:#f8fafc;">
              <th style="padding:12px 16px;font-size:0.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">Nom</th>
              <th style="padding:12px 16px;font-size:0.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">Email</th>
              <th style="padding:12px 16px;font-size:0.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">Rôle</th>
              <th style="padding:12px 16px;font-size:0.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">Filière</th>
              <th style="padding:12px 16px;font-size:0.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentUsers as $u): ?>
            <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
              <td style="padding:14px 16px;font-weight:600;color:#1e293b;"><?= h($u['prenom'] . ' ' . $u['nom']) ?></td>
              <td style="padding:14px 16px;color:#64748b;"><?= h($u['email']) ?></td>
              <td style="padding:14px 16px;color:#475569;"><?= roleLabel($u['role']) ?></td>
              <td style="padding:14px 16px;color:#475569;"><?= h($u['filiere'] ?? '-') ?></td>
              <td style="padding:14px 16px;color:#94a3b8;font-weight:500;"><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
