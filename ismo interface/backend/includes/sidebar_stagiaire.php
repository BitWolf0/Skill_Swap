<?php
/**
 * ISMO-SkillSwap v4 — Stagiaire Sidebar
 * Expects: $currentPage (e.g., 'dashboard', 'marketplace')
 */
$user = getCurrentUser();
$userName = $user ? ($user['prenom'] . ' ' . $user['nom']) : 'Stagiaire';
$userRole = $user ? roleLabel($user['role']) : 'Stagiaire';
$userId   = $user ? (int)$user['id'] : 0;
$demandeCount = 0;
if ($userId) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM demandes_aide WHERE auteur_id = ? AND statut IN ('Ouvert','En cours')");
        $stmt->execute([$userId]);
        $demandeCount = (int)$stmt->fetchColumn();
    } catch (\Throwable $e) {
        $demandeCount = 0;
    }
}
?>
<aside class="sidebar open" id="sidebar" aria-label="Navigation principale">
  <nav class="sidebar-nav" aria-label="Menu principal">
    <ul role="list">
      <li>
        <a href="dashboard.php" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>" id="nav-dashboard" <?= $currentPage === 'dashboard' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </span>
          <span class="nav-label">Tableau de bord</span>
        </a>
      </li>
      <li>
        <a href="mes_demandes.php" class="nav-item <?= $currentPage === 'mes_demandes' ? 'active' : '' ?>" id="nav-requests" <?= $currentPage === 'mes_demandes' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span class="nav-label">Mes Demandes</span>
          <?php if ($demandeCount > 0): ?><span class="nav-badge"><?= $demandeCount ?></span><?php endif; ?>
        </a>
      </li>
      <li>
        <a href="mes_badges.php" class="nav-item <?= $currentPage === 'mes_badges' ? 'active' : '' ?>" id="nav-badges" <?= $currentPage === 'mes_badges' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
          </span>
          <span class="nav-label">Mes Badges</span>
        </a>
      </li>
      <li>
        <a href="mes_competences.php" class="nav-item <?= $currentPage === 'mes_competences' ? 'active' : '' ?>" id="nav-competances" <?= $currentPage === 'mes_competences' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L15.09 8.26H22L17.55 12.25L19.64 18.5L12 14.01L4.36 18.5L6.45 12.25L2 8.26H8.91L12 2Z"/></svg>
          </span>
          <span class="nav-label">Mes Compétences</span>
        </a>
      </li>
      <li>
        <a href="passeport_pdf.php" class="nav-item <?= $currentPage === 'passeport_pdf' ? 'active' : '' ?>" id="nav-passport" <?= $currentPage === 'passeport_pdf' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </span>
          <span class="nav-label">Passeport PDF</span>
        </a>
      </li>
      <li>
        <a href="mentor_apply.php" class="nav-item <?= $currentPage === 'mentor_apply' ? 'active' : '' ?>" id="nav-mentor" <?= $currentPage === 'mentor_apply' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </span>
          <span class="nav-label">Devenir Mentor</span>
        </a>
      </li>
    </ul>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user-mini">
      <div class="mini-avatar" aria-hidden="true">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="mini-info">
        <span class="mini-name"><?= h($userName) ?></span>
        <span class="mini-role"><?= h($userRole) ?></span>
      </div>
    </div>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
