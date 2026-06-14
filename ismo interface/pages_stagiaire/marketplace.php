<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();

$pageTitle  = 'ISMO-SkillSwap — Marketplace d\'Entraide';
$currentPage = 'marketplace';
$basePath    = '..';

$db = Database::getInstance();

// Fetch skills for filter dropdown
$skills = $db->query('SELECT id, nom, categorie FROM competences_catalogue WHERE est_active = 1 ORDER BY nom')->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
include __DIR__ . '/../backend/includes/sidebar_stagiaire.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>
<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Marketplace d'Entraide</h1>
        <p class="page-sub">Trouvez des demandes d'aide ou proposez votre expertise</p>
      </div>
      <button class="btn-publish" onclick="window.location.href='nouvelle_demande.php'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Publier une demande
      </button>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
      <input type="text" class="form-input search-input" id="filter-search" placeholder="Rechercher par mot-clé…" />
      <select class="form-input" id="filter-skill">
        <option value="">Toutes les compétences</option>
        <?php foreach ($skills as $s): ?>
        <option value="<?= $s['id'] ?>"><?= h($s['nom']) ?> (<?= h($s['categorie']) ?>)</option>
        <?php endforeach; ?>
      </select>
      <select class="form-input" id="filter-status">
        <option value="">Tous les statuts</option>
        <option value="Ouvert">Ouvert</option>
        <option value="En cours">En cours</option>
      </select>
    </div>

    <div class="requests-container" id="requests-container">
      <div class="empty-state">
        <p>Chargement des demandes…</p>
      </div>
    </div>
  </section>
</main>

<script>
'use strict';

const container = document.getElementById('requests-container');
const searchEl  = document.getElementById('filter-search');
const skillEl   = document.getElementById('filter-skill');
const statusEl  = document.getElementById('filter-status');

let debounceTimer;

function loadRequests() {
  const params = new URLSearchParams();
  const search = searchEl.value.trim();
  const skill  = skillEl.value;
  const status = statusEl.value;

  if (search) params.set('search', search);
  if (skill)  params.set('competence_id', skill);
  if (status) params.set('statut', status);

  fetch('../backend/api/demandes.php?' + params.toString())
    .then(r => r.json())
    .then(data => {
      if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>Aucune demande trouvée. Soyez le premier à publier !</p></div>';
        return;
      }
      container.innerHTML = data.map(d => `
        <a href="demande_detail.php?id=${d.id}" class="request-card" style="display:block; text-decoration:none; color:inherit;">
          <div class="request-card-top">
            <h3 class="request-title">${esc(d.titre)}</h3>
            <span class="badge badge-${d.statut === 'Ouvert' ? 'pending' : 'active'}">${d.statut}</span>
          </div>
          <p class="request-desc">${esc(d.description ? d.description.substring(0, 200) : '')}</p>
          <div class="request-tags">
            <span class="tag tag-blue">${esc(d.competence_nom)}</span>
            <span class="tag tag-gray">${d.nb_propositions || 0} proposition(s)</span>
            <span class="tag tag-${d.urgence === 'Haute' || d.urgence === 'Critique' ? 'urgent' : 'gray'}">${d.urgence}</span>
          </div>
          <div class="request-meta">
            <span>Par ${esc(d.auteur_prenom)} ${esc(d.auteur_nom)}</span>
            <span>${new Date(d.creee_le).toLocaleDateString('fr-FR')}</span>
          </div>
        </a>
      `).join('');
    })
    .catch(() => {
      container.innerHTML = '<div class="empty-state"><p>Erreur de chargement. Veuillez réessayer.</p></div>';
    });
}

function esc(s) {
  if (!s) return '';
  const div = document.createElement('div');
  div.textContent = s;
  return div.innerHTML;
}

// Event listeners with debounce
searchEl.addEventListener('input', () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(loadRequests, 300);
});
skillEl.addEventListener('change', loadRequests);
statusEl.addEventListener('change', loadRequests);

// Initial load
loadRequests();
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
