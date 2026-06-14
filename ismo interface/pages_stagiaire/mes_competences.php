<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$_SESSION['user_id'];

$db = Database::getInstance();

$pageTitle  = 'ISMO-SkillSwap — Mes Compétences';
$currentPage = 'mes_competences';
$basePath   = '..';

// Get user's declared skills
$userSkills = $db->prepare('
    SELECT cs.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
           v.nom AS validateur_nom, v.prenom AS validateur_prenom
    FROM competences_stagiaire cs
    JOIN competences_catalogue c ON cs.competence_id = c.id
    LEFT JOIN utilisateurs v ON cs.validateur_id = v.id
    WHERE cs.utilisateur_id = ?
    ORDER BY cs.date_declaration DESC
');
$userSkills->execute([$userId]);
$userSkills = $userSkills->fetchAll();

// Get available skills for declaration (not already declared by user)
$declaredIds = array_map(fn($s) => $s['competence_id'], $userSkills);
if ($declaredIds) {
    $placeholders = implode(',', array_fill(0, count($declaredIds), '?'));
    $allSkills = $db->prepare("SELECT * FROM competences_catalogue WHERE est_active = 1 AND id NOT IN ($placeholders) ORDER BY nom");
    $allSkills->execute($declaredIds);
} else {
    $allSkills = $db->query("SELECT * FROM competences_catalogue WHERE est_active = 1 ORDER BY nom");
}
$allSkills = $allSkills->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
include __DIR__ . '/../backend/includes/sidebar_stagiaire.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Mes Compétences</h1>
        <p class="page-sub">Déclarez et gérez vos compétences</p>
      </div>
      <button class="btn-publish" onclick="openDeclareModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Déclarer une compétence
      </button>
    </div>

    <?php if (empty($userSkills)): ?>
    <div class="empty-state"><p>Vous n'avez pas encore déclaré de compétences.</p></div>
    <?php else: ?>
    <div class="skills-grid">
      <?php foreach ($userSkills as $sk): ?>
      <div class="skill-card">
        <div class="skill-card-header">
          <h3><?= h($sk['competence_nom']) ?></h3>
          <span class="badge badge-<?= $sk['statut_validation'] === 'Validé' ? 'active' : ($sk['statut_validation'] === 'Refusé' ? 'closed' : 'pending') ?>">
            <?= $sk['statut_validation'] ?>
          </span>
        </div>
        <p class="skill-category"><?= h($sk['competence_categorie']) ?></p>
        <div class="skill-meta">
          <span>Niveau: <strong><?= h($sk['niveau_estime']) ?></strong></span>
        </div>
        <?php if ($sk['validateur_nom']): ?>
        <p class="skill-validated-by">
          <?= $sk['statut_validation'] === 'Validé' ? 'Validé' : 'Refusé' ?> par
          <?= h($sk['validateur_prenom']) ?> <?= h($sk['validateur_nom']) ?>
          <?php if ($sk['date_validation']): ?>
            le <?= date('d/m/Y', strtotime($sk['date_validation'])) ?>
          <?php endif; ?>
        </p>
        <?php endif; ?>
        <?php if ($sk['motif_refus']): ?>
        <p class="skill-rejection-reason">Motif : <?= h($sk['motif_refus']) ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>

<!-- Declare Skill Modal -->
<div class="modal" id="declare-modal" style="display:none;" aria-hidden="true">
  <div class="modal-backdrop" onclick="closeDeclareModal()"></div>
  <div class="modal-content">
    <h2>Déclarer une compétence</h2>
    <form id="declare-form">
      <div class="form-group">
        <label for="competence_id">Compétence</label>
        <select id="competence_id" name="competence_id" class="form-input" required>
          <option value="">Choisir…</option>
          <?php foreach ($allSkills as $sk): ?>
          <option value="<?= $sk['id'] ?>"><?= h($sk['nom']) ?> (<?= h($sk['categorie']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="niveau_estime">Niveau estimé</label>
        <select id="niveau_estime" name="niveau_estime" class="form-input">
          <option value="Débutant">Débutant</option>
          <option value="Intermédiaire" selected>Intermédiaire</option>
          <option value="Avancé">Avancé</option>
        </select>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-sm btn-secondary" onclick="closeDeclareModal()">Annuler</button>
        <button type="submit" class="btn-sm btn-primary">Déclarer</button>
      </div>
    </form>
  </div>
</div>

<script>
'use strict';

function openDeclareModal() {
  document.getElementById('declare-modal').style.display = 'flex';
}
function closeDeclareModal() {
  document.getElementById('declare-modal').style.display = 'none';
}

document.getElementById('declare-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const data = Object.fromEntries(formData);
  data.competence_id = parseInt(data.competence_id);

  try {
    const resp = await fetch('../backend/api/competences.php?action=declarer', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: JSON.stringify(data)
    });
    const result = await resp.json();
    if (result.success) {
      showToast('Compétence déclarée ! En attente de validation.', 'success');
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
});
</script>

<style>
.modal[style*="display: flex"] { display: flex !important; }
.modal { position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; }
.modal-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
.modal-content { position: relative; background: #fff; border-radius: 12px; padding: 24px; max-width: 460px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
.modal-content h2 { margin-top: 0; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
.skills-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
.skill-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.skill-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
.skill-card-header h3 { margin: 0; font-size: 1.05rem; }
.skill-category { color: #9CA3AF; font-size: 0.85rem; margin: 4px 0; }
.skill-meta { display: flex; gap: 16px; font-size: 0.9rem; color: #4B5563; margin: 8px 0; }
.skill-validated-by { font-size: 0.82rem; color: #6B7280; }
.skill-rejection-reason { font-size: 0.82rem; color: #EF4444; }
</style>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
