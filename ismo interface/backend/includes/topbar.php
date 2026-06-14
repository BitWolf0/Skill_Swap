<?php
/**
 * ISMO-SkillSwap — Topbar Include
 */
require_once __DIR__ . '/../config.php';
$user = getCurrentUser();
$userName = $user ? ($user['prenom'] . ' ' . $user['nom']) : 'Utilisateur';
$userScore = $user ? (int)$user['points_gamification'] : 0;
$userRole = $user ? roleLabel($user['role']) : '';
$userLevel = $user ? getUserLevel((int)$user['points_gamification']) : ['name' => 'Débutant', 'level' => 1];

$unreadNotif = 0;
if ($user && isset($user['id'])) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user['id']]);
        $unreadNotif = (int)$stmt->fetchColumn();
    } catch (\Throwable $e) {
        $unreadNotif = 0;
    }
}

$basePath = $basePath ?? '..';

$role = $user['role'] ?? 'stagiaire';
$roleDir = match ($role) {
    'stagiaire' => 'pages_stagiaire',
    'mentor'    => 'pages_mentor',
    'formateur' => 'formateur_pages',
    'admin'     => 'pages_admin',
    default     => 'pages_stagiaire',
};

function rolePage(string $page, string $roleDir, string $fallbackDir = 'pages_stagiaire'): string {
    $path = __DIR__ . '/../../' . $roleDir . '/' . $page;
    return file_exists($path) ? $roleDir : $fallbackDir;
}
?>
<div class="main-wrapper">
  <header class="topbar" role="banner">
    <button class="icon-btn btn-menu" id="btn-menu" aria-label="Ouvrir le menu" type="button">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
    </button>

    <div class="sidebar-brand">
      <div class="brand-icon-wrap" aria-hidden="true">
        <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
          <rect width="32" height="32" rx="9" fill="#2563EB" />
          <path d="M8 16C8 11.582 11.582 8 16 8s8 3.582 8 8-3.582 8-8 8" stroke="#fff" stroke-width="2.5" stroke-linecap="round" />
          <circle cx="16" cy="16" r="3" fill="#fff" />
        </svg>
      </div>
      <span class="brand-text">ISMO<span>-SkillSwap</span></span>
    </div>

    <div class="search-wrap">
      <span class="search-icon" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
      </span>
      <input type="search" id="search-input" class="search-input" placeholder="Rechercher une compétence..." aria-label="Rechercher une compétence" />
      <kbd class="search-kbd">⌘K</kbd>
    </div>

    <div class="topbar-right">
      <button class="icon-btn notif-btn" id="btn-notif" aria-label="Notifications" type="button">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
          <path d="M13.73 21a2 2 0 0 1-3.46 0" />
        </svg>
        <?php if ($unreadNotif > 0): ?><span class="notif-dot" aria-label="Nouvelles notifications"></span><?php endif; ?>
      </button>
      <div class="notif-panel" id="notif-panel" hidden>
        <div class="notif-panel-head">
          <span class="notif-panel-title">Notifications</span>
          <button class="notif-mark-all" id="notif-mark-all" type="button">Marquer tout lu</button>
        </div>
        <div class="notif-panel-body" id="notif-list"></div>
      </div>

      <button class="profile-btn" id="btn-profile" type="button" aria-haspopup="true" aria-expanded="false" aria-label="Menu profil">
        <div class="profile-info">
          <span class="profile-name"><?= h($userName) ?></span>
          <span class="profile-score">Points: <?= $userScore ?></span>
        </div>
        <div class="profile-avatar" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
        </div>
      </button>

      <div class="profile-dropdown" id="profile-dropdown" role="menu" aria-label="Options du profil" hidden>
        <div class="dropdown-header">
          <div class="dropdown-avatar" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
              <circle cx="12" cy="7" r="4" />
            </svg>
          </div>
          <div class="dropdown-user-info">
            <span class="dropdown-name"><?= h($userName) ?></span>
            <span class="dropdown-rep">Niv.<?= $userLevel['level'] ?> · <?= h($userLevel['name']) ?> · Points: <?= $userScore ?></span>
          </div>
        </div>
        <ul class="dropdown-menu" role="none">
          <li role="none"><a href="<?= $basePath ?>/<?= rolePage('profile.php', $roleDir) ?>/profile.php" class="dropdown-item" role="menuitem"><span class="dd-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span> Mon profil</a></li>
          <li role="none"><a href="<?= $basePath ?>/<?= rolePage('mes_badges.php', $roleDir) ?>/mes_badges.php" class="dropdown-item" role="menuitem"><span class="dd-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span> Mes badges</a></li>

          <li role="none"><a href="<?= $basePath ?>/<?= $roleDir ?>/parametres.php" class="dropdown-item" role="menuitem"><span class="dd-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span> Paramètres</a></li>
        </ul>
        <div class="dropdown-divider"></div>
        <a href="<?= $basePath ?>/backend/auth/logout.php" class="dropdown-item dropdown-logout" role="menuitem">
          <span class="dd-icon" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
          </span>
          Déconnexion
        </a>
      </div>
    </div>
  </header>
