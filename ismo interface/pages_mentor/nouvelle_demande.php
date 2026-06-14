<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$_SESSION['user_id'];

$db = Database::getInstance();

$skills = $db->query("SELECT id, nom, categorie FROM competences_catalogue WHERE est_active = 1 ORDER BY nom")->fetchAll();

$editId = (int)($_GET['edit'] ?? 0);
$editDemande = null;
if ($editId) {
    $stmt = $db->prepare("SELECT * FROM demandes_aide WHERE id = ? AND auteur_id = ?");
    $stmt->execute([$editId, $userId]);
    $editDemande = $stmt->fetch();
}

$pageTitle   = $editDemande ? 'ISMO-SkillSwap — Modifier la Demande' : 'ISMO-SkillSwap — Nouvelle Demande';
$currentPage = 'nouvelle_demande';
$basePath    = '..';

include __DIR__ . '/../backend/includes/header.php';
include __DIR__ . '/../backend/includes/sidebar_mentor.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="request-form-page">
      <div class="request-form-card">
        <div class="page-head">
          <div class="page-title-wrap">
            <h1 class="page-title"><?= $editDemande ? 'Modifier la Demande' : 'Nouvelle Demande d\'Aide' ?></h1>
            <p class="page-sub"><?= $editDemande ? 'Modifiez les détails de votre demande' : 'Décrivez votre besoin et obtenez de l\'aide rapidement' ?></p>
          </div>
        </div>

        <form class="request-form" id="request-form">
          <div class="form-field">
            <label for="titre">Titre de la demande</label>
            <input type="text" id="titre" name="titre" placeholder="Ex: Aide sur les composants React" required maxlength="200" value="<?= h($editDemande['titre'] ?? '') ?>" />
          </div>

          <div class="form-field">
            <label for="competence_id">Compétence concernée</label>
            <select id="competence_id" name="competence_id" required>
              <option value="">Sélectionner une compétence</option>
              <?php foreach ($skills as $s): ?>
              <option value="<?= $s['id'] ?>" <?= $editDemande && (int)$s['id'] === (int)$editDemande['competence_id'] ? 'selected' : '' ?>>
                <?= h($s['nom']) ?> (<?= h($s['categorie']) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-field">
            <label for="description">Description détaillée</label>
            <textarea id="description" name="description" rows="6" placeholder="Expliquez votre problème en détails…" required><?= h($editDemande['description'] ?? '') ?></textarea>
          </div>

          <div class="form-grid">
            <div class="form-field">
              <label for="urgence">Niveau d'urgence</label>
              <select id="urgence" name="urgence">
                <option value="Faible" <?= $editDemande && $editDemande['urgence'] === 'Faible' ? 'selected' : '' ?>>Faible</option>
                <option value="Moyenne" <?= !$editDemande || $editDemande['urgence'] === 'Moyenne' ? 'selected' : '' ?>>Moyenne</option>
                <option value="Haute" <?= $editDemande && $editDemande['urgence'] === 'Haute' ? 'selected' : '' ?>>Haute</option>
                <option value="Critique" <?= $editDemande && $editDemande['urgence'] === 'Critique' ? 'selected' : '' ?>>Critique</option>
              </select>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary btn-publish">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <?= $editDemande ? 'Mettre à jour' : 'Publier la demande' ?>
            </button>
          </div>
        </form>
      </div>

      <div class="request-tips-card">
        <h3>Conseils pour une bonne demande</h3>
        <div class="tips-list">
          <div class="tip-item">
            <h3>Soyez précis</h3>
            <p>Décrivez clairement votre problème, ce que vous avez déjà essayé et ce dont vous avez besoin.</p>
          </div>
          <div class="tip-item">
            <h3>Choisissez la bonne compétence</h3>
            <p>Sélectionnez la compétence qui correspond le mieux à votre besoin pour attirer la bonne personne.</p>
          </div>
          <div class="tip-item">
            <h3>Indiquez l'urgence</h3>
            <p>Utilisez le niveau d'urgence adapté pour que les mentors puissent prioriser votre demande.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
'use strict';
const CSRF_TOKEN = '<?= csrfToken() ?>';
const isEdit = <?= $editDemande ? 'true' : 'false' ?>;
const editId = <?= $editId ?: 0 ?>;

document.getElementById('request-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const data = Object.fromEntries(formData);
  data.competence_id = parseInt(data.competence_id);

  const btn = this.querySelector('.btn-publish');
  btn.disabled = true;
  btn.textContent = 'Publication…';

  try {
    const url = isEdit
      ? `../backend/api/demandes.php?id=${editId}`
      : '../backend/api/demandes.php';

    const res = await fetch(url, {
      method: isEdit ? 'PUT' : 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
      body: JSON.stringify(data),
    });
    const result = await res.json();
    if (result.success) {
      showToast(isEdit ? 'Demande mise à jour !' : 'Demande publiée avec succès !', 'success');
      setTimeout(() => window.location.href = 'marketplace.php', 1200);
    } else {
      showToast(result.error || 'Erreur lors de la publication', 'error');
      btn.disabled = false;
      btn.textContent = isEdit ? 'Mettre à jour' : 'Publier la demande';
    }
  } catch (err) {
    showToast('Erreur de connexion', 'error');
    btn.disabled = false;
    btn.textContent = isEdit ? 'Mettre à jour' : 'Publier la demande';
  }
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
