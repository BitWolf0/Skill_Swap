<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$_SESSION['user_id'];

$db = Database::getInstance();

$pageTitle  = 'ISMO-SkillSwap — Mes Badges';
$currentPage = 'mes_badges';
$basePath   = '..';

$points = (int)$user['points_gamification'];

$stmt = $db->prepare('
    SELECT b.*, bs.obtenu_le, bs.motif
    FROM badges_stagiaire bs
    JOIN badges b ON bs.badge_id = b.id
    WHERE bs.utilisateur_id = ?
    ORDER BY bs.obtenu_le DESC
');
$stmt->execute([$userId]);
$myBadges = $stmt->fetchAll();

$allBadges = $db->query('SELECT * FROM badges WHERE est_actif = 1 ORDER BY points_requis ASC, nom')->fetchAll();
$earnedIds = array_map(fn($b) => $b['id'], $myBadges);

include __DIR__ . '/../backend/includes/header.php';
include __DIR__ . '/../backend/includes/sidebar_stagiaire.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Mes Badges</h1>
        <p class="page-sub"><?= count($myBadges) ?> badge(s) obtenu(s) — <?= $points ?> points de gamification</p>
      </div>
    </div>

    <div class="points-bar">
      <div class="points-stat">
        <strong><?= $points ?></strong>
        <span>Points totaux</span>
      </div>
      <div class="points-stat">
        <strong><?= count($myBadges) ?></strong>
        <span>Badges débloqués</span>
      </div>
      <div class="points-stat">
        <strong><?= count($allBadges) ?></strong>
        <span>Badges disponibles</span>
      </div>
    </div>

    <h2 class="section-label">Badges obtenus</h2>
    <?php if (empty($myBadges)): ?>
    <div class="empty-state"><p>Vous n'avez pas encore de badges. Aidez les autres et faites valider vos compétences pour en gagner !</p></div>
    <?php else: ?>
    <div class="badges-grid">
      <?php foreach ($myBadges as $b): ?>
      <div class="badge-card earned">
        <div class="badge-icon">
          <?php if ($b['icone']): ?>
          <span style="font-size:2rem"><?= h($b['icone']) ?></span>
          <?php else: ?>
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
          <?php endif; ?>
        </div>
        <h3><?= h($b['nom']) ?></h3>
        <p><?= h($b['description'] ?? '') ?></p>
        <small>Obtenu le <?= date('d/m/Y', strtotime($b['obtenu_le'])) ?></small>
        <?php if ($b['motif']): ?><p class="badge-reason"><?= h($b['motif']) ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <h2 class="section-label" style="margin-top:2rem">Badges disponibles</h2>
    <div class="badges-grid">
      <?php foreach ($allBadges as $b): ?>
      <?php $isEarned = in_array($b['id'], $earnedIds); ?>
      <div class="badge-card <?= $isEarned ? 'earned' : 'locked' ?>">
        <div class="badge-icon">
          <?php if ($b['icone']): ?>
          <span style="font-size:2rem;<?= $isEarned ? '' : 'opacity:.3' ?>"><?= h($b['icone']) ?></span>
          <?php else: ?>
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="<?= $isEarned ? '1' : '.3' ?>"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
          <?php endif; ?>
        </div>
        <h3><?= h($b['nom']) ?></h3>
        <p><?= h($b['description'] ?? '') ?></p>
        <?php if ($b['points_requis'] > 0): ?>
        <div class="badge-progress">
          <div class="progress-track">
            <div class="progress-fill" style="width: <?= min(100, round($points / $b['points_requis'] * 100)) ?>%"></div>
          </div>
          <span class="progress-text"><?= min($points, $b['points_requis']) ?> / <?= $b['points_requis'] ?> pts</span>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<style>
.points-bar { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.points-stat { background: #fff; border-radius: 12px; padding: 16px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); text-align: center; flex: 1; min-width: 120px; }
.points-stat strong { display: block; font-size: 1.5rem; color: var(--primary); }
.points-stat span { font-size: 0.85rem; color: #6B7280; }
.badges-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); }
.badge-card { background: #fff; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.badge-card.locked { opacity: 0.7; }
.badge-card h3 { font-size: 1rem; margin: 8px 0 4px; }
.badge-card p { font-size: 0.85rem; color: #6B7280; margin: 0; }
.badge-card small { font-size: 0.78rem; color: #9CA3AF; }
.badge-icon { width: 48px; height: 48px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
.badge-reason { font-size: 0.8rem; color: var(--primary); margin-top: 4px; }
.badge-progress { margin-top: 10px; }
.progress-track { height: 6px; background: #E5E7EB; border-radius: 3px; overflow: hidden; }
.progress-fill { height: 100%; background: var(--primary); border-radius: 3px; transition: width .3s; }
.progress-text { font-size: 0.75rem; color: #9CA3AF; display: block; margin-top: 2px; }
</style>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
