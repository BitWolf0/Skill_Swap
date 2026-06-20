<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole(ROLE_ADMIN);

$pageTitle = 'ISMO-SkillSwap — Catalogue Admin';
$currentPage = 'catalogue_admin';
$basePath = '..';

$db = Database::getInstance();

$stmt = $db->query("SELECT cc.*, (SELECT COUNT(*) FROM competences_stagiaire WHERE competence_id = cc.id) as declared_count FROM competences_catalogue cc ORDER BY cc.categorie, cc.nom");
$skills = $stmt->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Gestion du Catalogue</h1>
        <p class="page-sub">Ajoutez, modifiez ou désactivez des compétences</p>
      </div>
      <button class="btn-publish" onclick="document.getElementById('add-skill-modal').classList.add('active')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter
      </button>
    </div>

    <table class="data-table">
      <thead><tr><th>Nom</th><th>Catégorie</th><th>Déclarations</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($skills as $s): ?>
        <tr>
          <td><?= h($s['nom']) ?></td>
          <td><?= h($s['categorie'] ?? 'Non catégorisé') ?></td>
          <td><?= (int)$s['declared_count'] ?></td>
          <td><span class="badge badge-<?= $s['est_active'] ? 'active' : 'closed' ?>"><?= $s['est_active'] ? 'Actif' : 'Inactif' ?></span></td>
          <td>
            <?php if ($s['est_active']): ?>
            <a href="../backend/api/competences.php?action=supprimer&id=<?= $s['id'] ?>" class="btn-sm btn-secondary" onclick="event.preventDefault(); if(confirm('Désactiver ?')) fetch(this.href, {method:'DELETE', headers:{'X-CSRF-TOKEN':'<?= csrfToken() ?>'}}).then(()=>location.reload())">Désactiver</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="modal-overlay" id="add-skill-modal">
      <div class="modal-overlay-bg" onclick="document.getElementById('add-skill-modal').classList.remove('active')"></div>
      <div class="modal-content">
        <div class="modal-header">
          <h2>Ajouter une compétence</h2>
          <button type="button" class="modal-close" onclick="document.getElementById('add-skill-modal').classList.remove('active')">&times;</button>
        </div>
        <form method="post" action="../backend/api/competences.php?action=ajouter" id="add-skill-form">
          <div class="modal-body">
            <div class="form-group">
              <label for="skill-name">Nom</label>
              <input type="text" id="skill-name" name="nom" class="form-input" required />
            </div>
            <div class="form-group">
              <label for="skill-cat">Catégorie</label>
              <input type="text" id="skill-cat" name="categorie" class="form-input" placeholder="Ex: Programmation, Base de données..." />
            </div>
            <div class="form-group">
              <label for="skill-desc">Description</label>
              <textarea id="skill-desc" name="description" class="form-input" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-sm btn-secondary" onclick="document.getElementById('add-skill-modal').classList.remove('active')">Annuler</button>
            <button type="submit" class="btn-sm btn-primary">Ajouter</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>
<script>
document.getElementById('add-skill-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = Object.fromEntries(new FormData(this));
  try {
    const resp = await fetch(this.action, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' }, body: JSON.stringify(data) });
    const r = await resp.json();
    if (r.success) { showToast('Compétence ajoutée !', 'success'); setTimeout(() => location.reload(), 800); }
    else showToast(r.error || 'Erreur', 'error');
  } catch(e) { showToast('Erreur', 'error'); }
});
</script>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
