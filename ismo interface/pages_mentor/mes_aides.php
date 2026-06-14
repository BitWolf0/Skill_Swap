<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$user['id'];

$pageTitle = 'ISMO-SkillSwap — Mes Aides';
$currentPage = 'mes_aides';
$basePath = '..';

$stmt = $db->prepare("SELECT p.*, d.titre, d.description as post_desc, d.statut as post_status, d.auteur_id as owner_id, c.nom AS skill_name, CONCAT(u.prenom, ' ', u.nom) as requester_name, d.note_mentor as rating_by_owner FROM propositions_aide p JOIN demandes_aide d ON p.demande_id = d.id JOIN competences_catalogue c ON d.competence_id = c.id JOIN utilisateurs u ON d.auteur_id = u.id WHERE p.proposant_id = ? ORDER BY p.creee_le DESC");
$stmt->execute([$userId]);
$responses = $stmt->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Mes Aides</h1>
        <p class="page-sub">Historique de vos propositions d'aide</p>
      </div>
    </div>

    <?php if (empty($responses)): ?>
    <div class="empty-state"><p>Vous n'avez pas encore proposé votre aide.</p></div>
    <?php else: ?>
    <div class="requests-list">
      <?php foreach ($responses as $r): ?>
      <div class="request-card" data-response-id="<?= $r['id'] ?>" data-request-id="<?= $r['demande_id'] ?>">
        <div class="request-card-top">
          <h3 class="request-title"><?= h($r['titre']) ?></h3>
          <span class="badge badge-<?= $r['statut'] === 'Acceptée' ? 'active' : ($r['statut'] === 'En attente' ? 'pending' : 'done') ?>">
            <?= h($r['statut']) ?>
          </span>
          <?php if ($r['post_status'] === 'Résolu'): ?>
          <span class="badge badge-done">Résolu</span>
          <?php endif; ?>
        </div>
        <p class="request-desc"><?= h(mb_substr($r['post_desc'], 0, 150)) ?></p>
        <div class="request-tags">
          <span class="tag tag-blue"><?= h($r['skill_name']) ?></span>
          <span class="tag tag-gray">Demandé par <?= h($r['requester_name']) ?></span>
        </div>

        <?php if ($r['rating_by_owner']): ?>
        <p class="rating-display" style="margin-top:8px;">Note reçue: <?php for ($_s = 0; $_s < (int)$r['rating_by_owner']; $_s++): ?>
          <svg class="star-icon star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <?php endfor; ?>/5</p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
