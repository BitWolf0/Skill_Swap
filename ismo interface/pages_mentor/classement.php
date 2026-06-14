<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();

$db = Database::getInstance();

$pageTitle   = 'ISMO-SkillSwap — Classement';
$currentPage = 'classement';
$basePath    = '..';

$filiereFilter = trim($_GET['filiere'] ?? '');
$fieres = $db->query('SELECT DISTINCT filiere FROM utilisateurs WHERE filiere IS NOT NULL AND filiere != \'\' ORDER BY filiere')->fetchAll();

$sql = '
    SELECT u.id, u.nom, u.prenom, u.filiere, u.role, u.points_gamification,
           (SELECT COUNT(*) FROM demandes_aide WHERE mentor_id = u.id AND statut = \'Résolu\') AS aides_fournies,
           (SELECT COALESCE(ROUND(AVG(note_mentor), 1), 0) FROM demandes_aide WHERE mentor_id = u.id AND note_mentor IS NOT NULL) AS note_moyenne,
           (SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = u.id AND statut_validation = \'Validé\') AS competences_validees
    FROM utilisateurs u
    WHERE 1=1';

$params = [];
if ($filiereFilter) {
    $sql .= ' AND u.filiere = ?';
    $params[] = $filiereFilter;
}

$sql .= ' ORDER BY u.points_gamification DESC LIMIT 20';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$ranking = $stmt->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
include __DIR__ . '/../backend/includes/sidebar_mentor.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Classement</h1>
        <p class="page-sub">Top des mentors — Les membres les plus actifs de la communauté</p>
      </div>
    </div>

    <div class="rank-filters">
      <form method="get" class="filter-form">
        <label for="filiere">Filière :</label>
        <select name="filiere" id="filiere" onchange="this.form.submit()">
          <option value="">Toutes les filières</option>
          <?php foreach ($fieres as $f): ?>
          <option value="<?= h($f['filiere']) ?>" <?= $filiereFilter === $f['filiere'] ? 'selected' : '' ?>><?= h($f['filiere']) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if ($filiereFilter): ?>
        <a href="classement.php" class="reset-link">Réinitialiser</a>
        <?php endif; ?>
      </form>
    </div>

    <?php if (empty($ranking)): ?>
    <div class="empty-state"><p>Aucun membre trouvé.</p></div>
    <?php else: ?>
    <div class="rank-grid">
      <?php foreach ($ranking as $i => $u): ?>
      <div class="rank-card <?= $i < 3 ? 'top top-' . ($i + 1) : '' ?>">
        <div class="rank-card-inner">
          <div class="rank-badge-num">#<?= $i + 1 ?></div>

          <div class="rank-avatar-wrap">
            <div class="rank-avatar <?= $i < 3 ? 'top-avatar' : '' ?>">
              <?= mb_substr(h($u['prenom']), 0, 1) ?><?= mb_substr(h($u['nom']), 0, 1) ?>
            </div>
            <?php if ($i < 3): ?>
            <div class="rank-crown"><?php if ($i === 0): ?><span class="icon-crown" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #F59E0B;"><path d="M2 4l3 12h14l3-12-6 7-4-7-4 7-6-7z"/><path d="M2 20h20"/></svg></span><?php elseif ($i === 1): ?><span class="icon-medal" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #94A3B8;"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span><?php else: ?><span class="icon-medal" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #FDBA74;"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span><?php endif; ?></div>
            <?php endif; ?>
          </div>

          <div class="rank-name"><?= h($u['prenom']) ?> <?= h($u['nom']) ?></div>
          <?php if ($u['filiere']): ?>
          <div class="rank-filiere"><?= h($u['filiere']) ?></div>
          <?php endif; ?>

          <div class="rank-divider"></div>

          <div class="rank-stats">
            <div class="rank-stat highlight">
              <span class="stat-icon"><span class="icon-star" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #F59E0B;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span></span>
              <span class="stat-value"><?= (int)$u['points_gamification'] ?></span>
              <span class="stat-label">Points</span>
            </div>
            <div class="rank-stat">
              <span class="stat-icon"><span class="icon-handshake" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12c0 2.5-2 5-5 5h-2l-4 3v-3H9c-3 0-5-2-5-5s2-5 5-5h1"/><path d="M16 7l2-2 4 4-4 4-2-2"/><path d="M8 12l-2 2-4-4 4-4 2 2"/></svg></span></span>
              <span class="stat-value"><?= (int)$u['aides_fournies'] ?></span>
              <span class="stat-label">Aides</span>
            </div>
            <div class="rank-stat">
              <span class="stat-icon"><span class="icon-star" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #F59E0B;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span></span>
              <span class="stat-value"><?= number_format((float)$u['note_moyenne'], 1) ?></span>
              <span class="stat-label">Note</span>
            </div>
            <div class="rank-stat">
              <span class="stat-icon"><span class="icon-scroll" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span></span>
              <span class="stat-value"><?= (int)$u['competences_validees'] ?></span>
              <span class="stat-label">Comp.</span>
            </div>
          </div>

          <a href="../pages_mentor/profile.php?id=<?= $u['id'] ?>" class="rank-profile-link">Voir le profil <span class="icon-arrow" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>

<style>
.rank-filters { margin-bottom: 28px; }
.rank-filters .filter-form { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.rank-filters label { font-size: 0.9rem; font-weight: 600; color: var(--gray-600); }
.rank-filters select { padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); font: inherit; font-size: 0.9rem; color: var(--gray-800); background: var(--white); min-width: 180px; cursor: pointer; }
.rank-filters select:focus { outline: none; border-color: var(--blue-500); box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
.reset-link { font-size: 0.85rem; font-weight: 600; color: var(--blue-600); text-decoration: none; }
.reset-link:hover { text-decoration: underline; }

.rank-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

.rank-card { border-radius: 16px; background: var(--white); box-shadow: 0 1px 4px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s; }
.rank-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
.rank-card-inner { padding: 28px 24px 20px; display: flex; flex-direction: column; align-items: center; text-align: center; }

.rank-card.top { position: relative; }
.rank-card.top-1 { background: linear-gradient(180deg, #FFFBEB 0%, #fff 60%); border: 2px solid #F59E0B; box-shadow: 0 4px 20px rgba(245,158,11,0.15); }
.rank-card.top-2 { background: linear-gradient(180deg, #F8FAFC 0%, #fff 60%); border: 2px solid #CBD5E1; }
.rank-card.top-3 { background: linear-gradient(180deg, #FFF7ED 0%, #fff 60%); border: 2px solid #FDBA74; }

.rank-badge-num { align-self: flex-end; font-size: 0.8rem; font-weight: 700; color: var(--gray-400); background: var(--gray-100); padding: 4px 10px; border-radius: 20px; margin-bottom: 8px; }
.top-1 .rank-badge-num { background: #FEF3C7; color: #D97706; }
.top-2 .rank-badge-num { background: #F1F5F9; color: #64748B; }
.top-3 .rank-badge-num { background: #FFEDD5; color: #EA580C; }

.rank-avatar-wrap { position: relative; margin-bottom: 12px; }
.rank-avatar { width: 68px; height: 68px; border-radius: 50%; background: linear-gradient(135deg, var(--blue-500), var(--blue-700)); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.3rem; letter-spacing: 1px; box-shadow: 0 4px 12px rgba(37,99,235,0.2); }
.rank-avatar.top-avatar { width: 80px; height: 80px; font-size: 1.5rem; }
.top-1 .rank-avatar { background: linear-gradient(135deg, #F59E0B, #D97706); box-shadow: 0 4px 14px rgba(245,158,11,0.3); }
.top-2 .rank-avatar { background: linear-gradient(135deg, #94A3B8, #64748B); box-shadow: 0 4px 14px rgba(100,116,139,0.25); }
.top-3 .rank-avatar { background: linear-gradient(135deg, #FDBA74, #EA580C); box-shadow: 0 4px 14px rgba(234,88,12,0.25); }

.rank-crown { position: absolute; top: -8px; right: -8px; display: flex; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15)); animation: crownPop 0.4s ease-out; }
@keyframes crownPop { 0% { transform: scale(0) rotate(-20deg); } 70% { transform: scale(1.2) rotate(5deg); } 100% { transform: scale(1) rotate(0); } }

.rank-name { font-size: 1.1rem; font-weight: 700; color: var(--gray-900); margin-bottom: 2px; }
.rank-filiere { font-size: 0.82rem; color: var(--gray-500); margin-bottom: 12px; }

.rank-divider { width: 50px; height: 3px; border-radius: 2px; background: var(--gray-200); margin-bottom: 16px; }
.top-1 .rank-divider { background: #F59E0B; }
.top-2 .rank-divider { background: #94A3B8; }
.top-3 .rank-divider { background: #FDBA74; }

.rank-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; width: 100%; margin-bottom: 16px; }
.rank-stat { display: flex; flex-direction: column; align-items: center; gap: 2px; padding: 8px 4px; border-radius: 10px; background: var(--gray-50); }
.rank-stat.highlight { background: var(--blue-50); }
.top-1 .rank-stat.highlight { background: #FEF3C7; }
.top-2 .rank-stat.highlight { background: #F1F5F9; }
.top-3 .rank-stat.highlight { background: #FFEDD5; }
.stat-icon { font-size: 1rem; }
.stat-value { font-weight: 700; font-size: 1.05rem; color: var(--gray-800); }
.stat-label { font-size: 0.7rem; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; }

.rank-profile-link { display: inline-block; font-size: 0.85rem; font-weight: 600; color: var(--blue-600); text-decoration: none; padding: 6px 16px; border-radius: 20px; border: 1px solid var(--blue-200); transition: all 0.2s; }
.rank-profile-link:hover { background: var(--blue-50); border-color: var(--blue-400); }

@media (max-width: 680px) {
  .rank-grid { grid-template-columns: 1fr; }
  .rank-card-inner { padding: 20px 16px; }
}
</style>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
