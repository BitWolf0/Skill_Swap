<?php
require_once __DIR__ . '/../config.php';
$user = getCurrentUser();
$userName = $user ? ($user['prenom'] . ' ' . $user['nom']) : 'Mentor';
$userRole = 'Mentor';

$demandeCount = 0;
if ($user && isset($user['id'])) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM demandes_aide WHERE auteur_id = ? AND statut IN ('Ouvert','En cours')");
        $stmt->execute([$user['id']]);
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
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
          <span class="nav-label">Tableau de bord</span>
        </a>
      </li>
      <li>
        <a href="mes_demandes.php" class="nav-item <?= $currentPage === 'mes_demandes' ? 'active' : '' ?>" id="nav-requests" <?= $currentPage === 'mes_demandes' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
          <span class="nav-label">Mes Demandes</span>
          <?php if ($demandeCount > 0): ?><span class="nav-badge"><?= $demandeCount ?></span><?php endif; ?>
        </a>
      </li>
      <li>
        <a href="mes_aides.php" class="nav-item <?= $currentPage === 'mes_aides' ? 'active' : '' ?>" id="nav-aides" <?= $currentPage === 'mes_aides' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
          <span class="nav-label">Mes Aides</span>
        </a>
      </li>
      <li>
        <a href="marketplace.php" class="nav-item <?= $currentPage === 'marketplace' ? 'active' : '' ?>" id="nav-marketplace" <?= $currentPage === 'marketplace' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></span>
          <span class="nav-label">Marketplace</span>
        </a>
      </li>
      <li>
        <a href="mes_badges.php" class="nav-item <?= $currentPage === 'mes_badges' ? 'active' : '' ?>" id="nav-badges" <?= $currentPage === 'mes_badges' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span>
          <span class="nav-label">Mes Badges</span>
        </a>
      </li>
      <li>
        <a href="mes_competences.php" class="nav-item <?= $currentPage === 'mes_competences' ? 'active' : '' ?>" id="nav-competences" <?= $currentPage === 'mes_competences' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L15.09 8.26H22L17.55 12.25L19.64 18.5L12 14.01L4.36 18.5L6.45 12.25L2 8.26H8.91L12 2Z"/></svg></span>
          <span class="nav-label">Mes Compétences</span>
        </a>
      </li>
      <li>
        <a href="classement.php" class="nav-item <?= $currentPage === 'classement' ? 'active' : '' ?>" id="nav-classement" <?= $currentPage === 'classement' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 6 9 6 9z"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 18 9 18 9z"/><path d="M4 22h16"/><path d="M10 22V2h4v20"/></svg></span>
          <span class="nav-label">Classement</span>
        </a>
      </li>
    </ul>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user-mini">
      <div class="mini-avatar" aria-hidden="true">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="mini-info">
        <span class="mini-name"><?= h($userName) ?></span>
        <span class="mini-role">Mentor</span>
      </div>
    </div>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
