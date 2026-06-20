<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();

$user = getCurrentUser();
if (!$user) redirect('../login.php');

$userId = (int)$user['id'];
$pageTitle = 'ISMO-SkillSwap — Tableau de bord';
$currentPage = 'dashboard';
$basePath = '..';

$db = Database::getInstance();

$stats = [];

$stats['points'] = (int)$user['points_gamification'];

$stmt = $db->prepare('SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = ?');
$stmt->execute([$userId]);
$stats['skills_count'] = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = ? AND statut_validation = 'Validé'");
$stmt->execute([$userId]);
$stats['verified_skills'] = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM demandes_aide WHERE mentor_id = ? AND statut = 'Résolu'");
$stmt->execute([$userId]);
$stats['people_helped'] = (int)$stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) FROM badges_stagiaire WHERE utilisateur_id = ?');
$stmt->execute([$userId]);
$stats['badges'] = (int)$stmt->fetchColumn();

$recentRequests = $db->prepare("
    SELECT d.*, cc.nom AS skill_name
    FROM demandes_aide d
    JOIN competences_catalogue cc ON d.competence_id = cc.id
    WHERE d.auteur_id = ?
    ORDER BY d.creee_le DESC LIMIT 5
");
$recentRequests->execute([$userId]);
$recentRequests = $recentRequests->fetchAll();

$userSkills = $db->prepare("
    SELECT cs.*, cc.nom AS skill_name, cc.categorie AS skill_category
    FROM competences_stagiaire cs
    JOIN competences_catalogue cc ON cs.competence_id = cc.id
    WHERE cs.utilisateur_id = ?
    ORDER BY cs.date_declaration DESC LIMIT 5
");
$userSkills->execute([$userId]);
$userSkills = $userSkills->fetchAll();

$totalPossible = 15;
$progressPct = min(round(($stats['verified_skills'] / $totalPossible) * 100), 100);

include __DIR__ . '/../backend/includes/header.php';
?>

  <?php include __DIR__ . '/../backend/includes/sidebar_stagiaire.php'; ?>
  <?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

  <main class="content-area has-sidebar" id="main-content">
    <section class="content-main" aria-label="Contenu principal">
      <div class="page-head">
        <div class="page-title-wrap">
          <h1 class="page-title">Bienvenue sur votre tableau de bord</h1>
          <p class="page-sub">Publiez une demande ou consultez vos compétences</p>
        </div>
        <button class="btn-publish" id="btn-publish" type="button" onclick="window.location.href='nouvelle_demande.php'">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Publier une demande d'aide
        </button>
      </div>

<?php $levelInfo = getUserLevel($stats['points']); ?>
      <div class="quick-stats" aria-label="Statistiques rapides">
        <div class="qstat-card" id="qstat-level">
          <div class="qstat-icon qstat-purple" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="qstat-info">
            <span class="qstat-value">Niv.<?= $levelInfo['level'] ?></span>
            <span class="qstat-label"><?= h($levelInfo['name']) ?> · <?= $stats['points'] ?> pts</span>
          </div>
        </div>
        <div class="qstat-card" id="qstat-points">
          <div class="qstat-icon qstat-blue" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="qstat-info">
            <span class="qstat-value"><?= $stats['points'] ?></span>
            <span class="qstat-label">Points de gamification</span>
          </div>
        </div>
        <div class="qstat-card" id="qstat-helped">
          <div class="qstat-icon qstat-green" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="qstat-info">
            <span class="qstat-value"><?= $stats['people_helped'] ?></span>
            <span class="qstat-label">Personnes aidées</span>
          </div>
        </div>
        <div class="qstat-card" id="qstat-badges">
          <div class="qstat-icon qstat-orange" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
          </div>
          <div class="qstat-info">
            <span class="qstat-value"><?= $stats['badges'] ?></span>
            <span class="qstat-label">Badges obtenus</span>
          </div>
        </div>
      </div>

      <div class="level-progress" style="margin-bottom: 1.5rem; padding: 1rem">
        <div class="lp-header">
          <span class="lp-label">Niveau <?= $levelInfo['level'] ?> — <?= h($levelInfo['name']) ?></span>
          <span class="lp-points"><?= $levelInfo['points'] ?> / <?= $levelInfo['next_min'] ?> pts</span>
        </div>
        <div class="lp-track">
          <div class="lp-fill" style="width:<?= $levelInfo['progress'] ?>%;background:linear-gradient(90deg,#8B5CF6,#6366F1);transition:width .6s ease"></div>
        </div>
        <p class="lp-hint">
          <?php if ($levelInfo['progress'] < 100): ?>
          Plus que <?= $levelInfo['next_min'] - $levelInfo['points'] ?> points pour le niveau <?= $levelInfo['level'] + 1 ?>
          <?php else: ?>
          Niveau maximum atteint !
          <?php endif; ?>
        </p>
      </div>

      <div class="section-label-row">
        <h2 class="section-label">Demandes en cours</h2>
        <a href="mes_demandes.php" class="see-all-link">Voir tout</a>
      </div>

      <div class="requests-list" role="list">
        <?php if (empty($recentRequests)): ?>
          <div class="empty-state !text-gray-500">
            <p>Aucune demande pour le moment. <a href="nouvelle_demande.php">Publiez votre première demande</a>.</p>
          </div>
        <?php else: ?>
          <?php foreach ($recentRequests as $req): ?>
          <article class="request-card" role="listitem">
            <div class="request-card-top">
              <h3 class="request-title"><?= h($req['titre']) ?></h3>
              <span class="badge badge-<?= $req['statut'] === 'Ouvert' ? 'pending' : ($req['statut'] === 'En cours' ? 'active' : 'completed') ?>">
                <?= h($req['statut']) ?>
              </span>
            </div>
            <p class="request-desc"><?= h(mb_substr($req['description'], 0, 150)) ?><?= mb_strlen($req['description']) > 150 ? '…' : '' ?></p>
            <div class="request-tags">
              <span class="tag tag-blue"><?= h($req['skill_name']) ?></span>
              <span class="tag tag-gray">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?= timeAgo($req['creee_le']) ?>
              </span>
            </div>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <aside class="content-sidebar" aria-label="Widgets">
      <div class="widget widget-passport" id="widget-passport">
        <div class="widget-header">
          <span class="widget-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span>
          <h2 class="widget-title">Mon Passeport</h2>
        </div>
        <ul class="passport-list" role="list">
          <?php foreach ($userSkills as $skill): ?>
          <li class="passport-item">
            <div class="passport-item-icon pi-blue" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
            <div class="passport-item-body">
              <span class="passport-item-name"><?= h($skill['skill_name']) ?></span>
              <span class="passport-item-desc"><?= h($skill['skill_category']) ?></span>
            </div>
            <span class="passport-level pl-blue"><?= h($skill['niveau_estime']) ?></span>
          </li>
          <?php endforeach; ?>
          <?php if (empty($userSkills)): ?>
          <li class="passport-item !text-gray-400">Aucune compétence déclarée</li>
          <?php endif; ?>
        </ul>
        <button class="btn-passport-pdf" id="btn-passport-pdf" type="button" onclick="window.location.href='passeport_pdf.php'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Télécharger PDF
        </button>
      </div>

      <div class="widget widget-progress" id="widget-progress">
        <div class="widget-header">
          <span class="widget-icon widget-icon-green" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          <h2 class="widget-title">Progression</h2>
        </div>
        <div class="progress-info">
          <span class="progress-label">Compétences validées</span>
          <span class="progress-count"><?= $stats['verified_skills'] ?>/<?= $totalPossible ?></span>
        </div>
        <div class="progress-track" role="progressbar" aria-valuenow="<?= $progressPct ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?= $progressPct ?>% des compétences validées">
          <div class="progress-fill" id="progress-fill" style="width: <?= $progressPct ?>%"></div>
        </div>
        <p class="progress-hint"><?= $totalPossible - $stats['verified_skills'] > 0 ? 'Encore ' . ($totalPossible - $stats['verified_skills']) . ' compétences pour compléter votre profil !' : 'Profil complet !' ?></p>
      </div>
    </aside>
  </main>

<?php
function timeAgo($timestamp): string {
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return 'à l\'instant';
    if ($diff < 3600) return 'il y a ' . floor($diff / 60) . 'min';
    if ($diff < 86400) return 'il y a ' . floor($diff / 3600) . 'h';
    if ($diff < 604800) return 'il y a ' . floor($diff / 86400) . 'j';
    return date('d/m/Y', strtotime($timestamp));
}

include __DIR__ . '/../backend/includes/footer.php'; ?>
