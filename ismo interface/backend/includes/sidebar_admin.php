<?php
$user = getCurrentUser();
$userName = $user ? ($user['prenom'] . ' ' . $user['nom']) : 'Administrateur';
?>
<aside class="sidebar open" id="sidebar" aria-label="Navigation principale">
  <nav class="sidebar-nav" aria-label="Menu principal">
    <ul role="list">
      <li>
        <a href="tableau_de_bord.php" class="nav-item <?= $currentPage === 'tableau_de_bord' ? 'active' : '' ?>" <?= $currentPage === 'tableau_de_bord' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
          <span class="nav-label">Tableau de bord</span>
        </a>
      </li>
      <li>
        <a href="gestion_comptes.php" class="nav-item <?= $currentPage === 'gestion_comptes' ? 'active' : '' ?>" id="nav-accounts" <?= $currentPage === 'gestion_comptes' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
          <span class="nav-label">Gestion comptes</span>
        </a>
      </li>
      <li>
        <a href="moderation.php" class="nav-item <?= $currentPage === 'moderation' ? 'active' : '' ?>" id="nav-moderation" <?= $currentPage === 'moderation' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
          <span class="nav-label">Modération</span>
        </a>
      </li>
      <li>
        <a href="gestion_badges.php" class="nav-item <?= $currentPage === 'gestion_badges' ? 'active' : '' ?>" id="nav-badges" <?= $currentPage === 'gestion_badges' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
          <span class="nav-label">Badges</span>
        </a>
      </li>
      <li>
        <a href="statistiques_admin.php" class="nav-item <?= $currentPage === 'statistiques_admin' ? 'active' : '' ?>" id="nav-stats" <?= $currentPage === 'statistiques_admin' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg></span>
          <span class="nav-label">Statistiques</span>
        </a>
      </li>
      <li>
        <a href="catalogue_admin.php" class="nav-item <?= $currentPage === 'catalogue_admin' ? 'active' : '' ?>" id="nav-catalogue" <?= $currentPage === 'catalogue_admin' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
          <span class="nav-label">Catalogue</span>
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
        <span class="mini-role">Administrateur</span>
      </div>
    </div>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
