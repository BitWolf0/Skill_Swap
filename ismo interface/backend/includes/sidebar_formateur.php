<?php
$user = getCurrentUser();
$userName = $user ? ($user['prenom'] . ' ' . $user['nom']) : 'Formateur';
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
        <a href="validation_demande.php" class="nav-item <?= $currentPage === 'validation_demande' ? 'active' : '' ?>" id="nav-validation" <?= $currentPage === 'validation_demande' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
          <span class="nav-label">Validation</span>
        </a>
      </li>
      <li>
        <a href="statistique.php" class="nav-item <?= $currentPage === 'statistique' ? 'active' : '' ?>" id="nav-stats" <?= $currentPage === 'statistique' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="3" x2="3" y2="20"/><line x1="21" y1="20" x2="3" y2="20"/><rect x="5" y="12" width="3" height="8"/><rect x="11" y="8" width="3" height="12"/><rect x="17" y="4" width="3" height="16"/></svg></span>
          <span class="nav-label">Statistiques</span>
        </a>
      </li>
      <li>
        <a href="catalogue.php" class="nav-item <?= $currentPage === 'catalogue' ? 'active' : '' ?>" id="nav-catalogue" <?= $currentPage === 'catalogue' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
          <span class="nav-label">Catalogue</span>
        </a>
      </li>
      <li>
        <a href="recherche.php" class="nav-item <?= $currentPage === 'recherche' ? 'active' : '' ?>" id="nav-recherche" <?= $currentPage === 'recherche' ? 'aria-current="page"' : '' ?>>
          <span class="nav-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
          <span class="nav-label">Recherche</span>
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
        <span class="mini-role">Formateur</span>
      </div>
    </div>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>
