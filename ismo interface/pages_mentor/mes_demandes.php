<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$_SESSION['user_id'];

$db = Database::getInstance();

$pageTitle  = 'ISMO-SkillSwap — Mes Demandes';
$currentPage = 'mes_demandes';
$basePath   = '..';

$stmt = $db->prepare('
    SELECT d.*, c.nom AS competence_nom,
           (SELECT COUNT(*) FROM propositions_aide WHERE demande_id = d.id) AS nb_propositions
    FROM demandes_aide d
    JOIN competences_catalogue c ON d.competence_id = c.id
    WHERE d.auteur_id = ?
    ORDER BY d.creee_le DESC
');
$stmt->execute([$userId]);
$requests = $stmt->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
include __DIR__ . '/../backend/includes/sidebar_mentor.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Mes Demandes</h1>
        <p class="page-sub">Suivez vos demandes d'aide publiées</p>
      </div>
      <button class="btn-publish" onclick="window.location.href='nouvelle_demande.php'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouvelle demande
      </button>
    </div>

    <div class="filters-bar">
      <button class="filter-btn active" data-filter="all">Toutes</button>
      <button class="filter-btn" data-filter="Ouvert">Ouvertes</button>
      <button class="filter-btn" data-filter="En cours">En cours</button>
      <button class="filter-btn" data-filter="Résolu">Résolues</button>
    </div>

    <div class="requests-container">
      <?php if (empty($requests)): ?>
      <div class="empty-state"><p>Aucune demande pour le moment.</p></div>
      <?php else: ?>
      <?php foreach ($requests as $req): ?>
      <a href="demande_detail.php?id=<?= $req['id'] ?>" class="request-card" style="display:block; text-decoration:none; color:inherit;">
        <div class="request-card-top">
          <h3 class="request-title"><?= h($req['titre']) ?></h3>
          <span class="badge badge-<?= $req['statut'] === 'Ouvert' ? 'pending' : ($req['statut'] === 'En cours' ? 'active' : 'completed') ?>">
            <?= $req['statut'] ?>
          </span>
        </div>
        <p class="request-desc"><?= h(mb_substr($req['description'] ?? '', 0, 200)) ?></p>
        <div class="request-tags">
          <span class="tag tag-blue"><?= h($req['competence_nom']) ?></span>
          <span class="tag tag-gray"><?= $req['nb_propositions'] ?> proposition(s)</span>
          <?php if ($req['urgence'] === 'Haute' || $req['urgence'] === 'Critique'): ?>
          <span class="tag tag-urgent"><?= $req['urgence'] ?></span>
          <?php endif; ?>
        </div>
        <div class="request-meta" style="font-size:0.85rem; color:#9CA3AF; margin-top:8px;">
          <?= date('d/m/Y', strtotime($req['creee_le'])) ?>
        </div>
      </a>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<script>
'use strict';
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('.request-card').forEach(card => {
      const status = card.querySelector('.badge')?.textContent.trim() || '';
      if (filter === 'all' || status === filter) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
