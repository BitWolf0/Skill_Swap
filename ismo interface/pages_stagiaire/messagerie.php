<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser($db);
$pageTitle = 'ISMO-SkillSwap — Messagerie';
$currentPage = 'mes_demandes';
$basePath = '..';
include __DIR__ . '/../backend/includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/messagerie.css" />
<?php include __DIR__ . '/../backend/includes/sidebar_stagiaire.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>
<main class="content-area messagerie-page" id="main-content">
  <section class="content-main" aria-label="Messagerie">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Messagerie</h1>
        <p class="page-sub">Consultez et gérez toutes vos conversations</p>
      </div>
    </div>
    <div class="inbox-toolbar">
      <div class="inbox-search-wrap">
        <span class="search-icon" aria-hidden="true">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
        <input type="text" id="inbox-search" placeholder="Rechercher une conversation..." aria-label="Rechercher une conversation" />
      </div>
      <button class="btn-new-conv" id="btn-new-conv" type="button">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouvelle conversation
      </button>
    </div>
    <div class="conv-list" id="conv-list-container"></div>

<input type="hidden" id="current-user-id" value="<?= (int)$user['user_id'] ?>" />
<input type="hidden" id="current-user-name" value="<?= h(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>" />
  </section>
  <aside class="content-sidebar" aria-label="Statistiques">
    <div class="widget">
      <div class="widget-header">
        <span class="widget-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <h2 class="widget-title">Activité</h2>
      </div>
      <div class="stats-box">
        <div class="stat-item"><span class="stat-value" id="stat-conv-count">0</span><span class="stat-label">Conversations</span></div>
        <div class="stat-item"><span class="stat-value" id="stat-unread">0</span><span class="stat-label">Non lues</span></div>
      </div>
    </div>
    <div class="widget">
      <div class="widget-header">
        <span class="widget-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L15.09 8.26H22L17.55 12.25L19.64 18.5L12 14.01L4.36 18.5L6.45 12.25L2 8.26H8.91L12 2Z"/></svg></span>
        <h2 class="widget-title">Conseil</h2>
      </div>
      <p class="text-sm text-gray-500 leading-relaxed">Les mentors les plus réactifs sont mis en avant. N'hésitez pas à relancer si vous n'avez pas de réponse sous 48h.</p>
    </div>
  </aside>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>

<!-- New conversation modal -->
<div class="modal-overlay" id="new-conv-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-header">
      <h3 class="modal-title">Nouvelle conversation</h3>
      <button class="modal-close" id="newconv-close" type="button" aria-label="Fermer"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label" for="newconv-user-search">Destinataire</label>
        <input type="text" id="newconv-user-search" class="form-input" placeholder="Rechercher un utilisateur..." autocomplete="off" />
        <input type="hidden" id="newconv-selected-user" value="" />
        <div class="user-search-results" id="newconv-user-results"></div>
      </div>
      <div class="form-group">
        <label class="form-label" for="newconv-subject">Sujet (optionnel)</label>
        <input type="text" id="newconv-subject" class="form-input" placeholder="Objet de la conversation..." />
      </div>
      <div class="form-group">
        <label class="form-label" for="newconv-message">Message</label>
        <textarea id="newconv-message" class="form-input form-textarea" rows="4" placeholder="Écrivez votre message..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" id="newconv-cancel" type="button">Annuler</button>
      <button class="btn btn-primary" id="newconv-send" type="button">Envoyer</button>
    </div>
  </div>
</div>

<script src="../assets/js/messagerie.js"></script>
</body>
</html>
