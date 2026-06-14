<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser($db);
$pageTitle = 'ISMO-SkillSwap — Conversation';
$currentPage = 'mes_demandes';
$basePath = '..';
include __DIR__ . '/../backend/includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/messagerie.css" />
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>
<main class="content-area conv-page" id="main-content">
  <section class="content-main" aria-label="Conversation">
    <div class="conv-header">
      <a href="messagerie.php" class="btn-back" aria-label="Retour à la messagerie">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      </a>
      <div class="conv-header-info">
        <h1 class="conv-header-title" id="conv-header-title">Conversation</h1>
        <p class="conv-header-sub" id="conv-header-sub">Chargement...</p>
      </div>
    </div>
    <input type="hidden" id="current-user-id" value="<?= (int)$user['user_id'] ?>" />
    <input type="hidden" id="current-user-name" value="<?= h(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>" />
    <div class="messages-area" id="messages-container">
      <div class="empty-state">
        <div class="empty-state-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
        <div class="empty-state-title">Chargement...</div>
      </div>
    </div>
    <div class="msg-input-area">
      <button class="btn-attach" type="button" aria-label="Joindre un fichier">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
      </button>
      <div class="msg-input-wrap">
        <textarea id="msg-input" rows="1" placeholder="Écrivez votre message..." aria-label="Écrire un message"></textarea>
      </div>
      <button class="btn-send" id="btn-send" type="button" aria-label="Envoyer le message">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
  </section>
  <aside class="content-sidebar" aria-label="Détails de la demande">
    <div class="widget">
      <div class="widget-header">
        <span class="widget-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span>
        <h2 class="widget-title">Participant</h2>
      </div>
      <div class="participant-list">
        <div class="participant-item">
          <div class="participant-avatar blue" id="sidebar-participant-avatar">
            <span id="sidebar-participant-initials">?</span>
          </div>
          <div class="participant-info">
            <span class="participant-name" id="sidebar-participant-name">—</span>
            <span class="participant-role" id="sidebar-participant-role">—</span>
          </div>
        </div>
      </div>
    </div>
    <div class="widget">
      <div class="widget-header">
        <span class="widget-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
        <h2 class="widget-title">Actions</h2>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px;">
        <button class="btn-publish" type="button" style="width:100%;justify-content:center;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
          Voir la demande
        </button>
      </div>
    </div>
  </aside>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
<script src="../assets/js/messagerie.js"></script>
</body>
</html>
