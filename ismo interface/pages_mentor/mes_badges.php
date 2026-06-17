<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$user['id'];

$pageTitle = 'ISMO-SkillSwap — Mes Badges';
$currentPage = 'mes_badges';
$basePath = '..';

$stmt = $db->prepare("SELECT b.*, bs.obtenu_le FROM badges_stagiaire bs JOIN badges b ON bs.badge_id = b.id WHERE bs.utilisateur_id = ? ORDER BY bs.obtenu_le DESC");
$stmt->execute([$userId]);
$myBadges = $stmt->fetchAll();

$allBadges = $db->prepare("SELECT * FROM badges ORDER BY nom");
$allBadges->execute();
$allBadges = $allBadges->fetchAll();
$earnedIds = array_column($myBadges, 'id');

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Mes Badges</h1>
        <p class="page-sub">Vous avez obtenu <?= count($myBadges) ?> badge(s)</p>
      </div>
    </div>

    <h2 class="section-label">Badges obtenus</h2>
    <?php if (empty($myBadges)): ?>
    <div class="empty-state"><p>Vous n'avez pas encore de badges. Aidez les autres pour en gagner !</p></div>
    <?php else: ?>
    <div class="badges-grid">
      <?php foreach ($myBadges as $b): ?>
      <div class="badge-card earned">
        <div class="badge-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
        <h3><?= h($b['nom']) ?></h3>
        <p><?= h($b['description']) ?></p>
        <small>Obtenu le <?= date('d/m/Y', strtotime($b['obtenu_le'])) ?></small>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <h2 class="section-label" style="margin-top:2rem">Badges disponibles</h2>
    <div class="badges-grid">
      <?php foreach ($allBadges as $b): ?>
      <div class="badge-card <?= in_array($b['id'], $earnedIds) ? 'earned' : 'locked' ?>">
        <div class="badge-icon"><?php if (in_array($b['id'], $earnedIds)): ?><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg><?php else: ?><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><?php endif; ?></div>
        <h3><?= h($b['nom']) ?></h3>
        <p><?= h($b['description']) ?></p>
        <?php if ((int)$b['points_requis'] > 0): ?><span class="badge-points">+<?= (int)$b['points_requis'] ?> pts</span><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<style>
.badges-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); }
.badge-card { background: #fff; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 2px solid var(--gray-200); transition: transform var(--transition), box-shadow var(--transition); }
.badge-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.badge-card.earned { border-color: var(--blue-200); background: rgba(var(--blue-500-rgb), .06); }
.badge-card.locked { opacity: .7; }
.badge-card h3 { font-size: 1rem; margin: 8px 0 4px; color: var(--gray-900); }
.badge-card p { font-size: .85rem; color: var(--gray-500); margin: 0; }
.badge-card small { font-size: .78rem; color: var(--gray-400); display: block; margin-top: 8px; }
.badge-icon { width: 48px; height: 48px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
.badge-points { display: inline-block; margin-top: 8px; padding: 3px 10px; border-radius: 999px; background: var(--blue-600); color: #fff; font-size: .75rem; font-weight: 700; }
</style>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
