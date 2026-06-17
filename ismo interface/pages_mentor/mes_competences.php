<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$user['id'];

$pageTitle = 'ISMO-SkillSwap — Mes Compétences';
$currentPage = 'mes_competences';
$basePath = '..';

$userSkills = $db->prepare("SELECT cs.*, cc.nom AS skill_name, cc.categorie AS skill_category, CONCAT(v.prenom, ' ', v.nom) as validated_by_name FROM competences_stagiaire cs JOIN competences_catalogue cc ON cs.competence_id = cc.id LEFT JOIN utilisateurs v ON cs.validateur_id = v.id WHERE cs.utilisateur_id = ? ORDER BY cs.date_declaration DESC");
$userSkills->execute([$userId]);
$userSkills = $userSkills->fetchAll();

$allSkills = $db->prepare("SELECT * FROM competences_catalogue WHERE id NOT IN (SELECT competence_id FROM competences_stagiaire WHERE utilisateur_id = ?) ORDER BY nom");
$allSkills->execute([$userId]);
$allSkills = $allSkills->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Mes Compétences</h1>
        <p class="page-sub">Déclarez et gérez vos compétences</p>
      </div>
      <button class="btn-publish" id="btn-declare" onclick="document.getElementById('declare-modal').removeAttribute('hidden')">
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
          <h3><?= h($sk['skill_name']) ?></h3>
          <span class="badge badge-<?= $sk['statut_validation'] === 'Validé' ? 'active' : ($sk['statut_validation'] === 'Refusé' ? 'closed' : 'pending') ?>">
            <?= h($sk['statut_validation']) ?>
          </span>
        </div>
        <p class="skill-category"><?= h($sk['skill_category']) ?></p>
        <div class="skill-meta">
          <span>Niveau: <strong><?= h($sk['niveau_estime']) ?></strong></span>
        </div>
        <?php if ($sk['validated_by_name']): ?>
        <p class="skill-validated-by"><?= $sk['statut_validation'] === 'Validé' ? 'Validé' : 'Refusé' ?> par <?= h($sk['validated_by_name']) ?></p>
        <?php endif; ?>
        <?php if ($sk['motif_refus']): ?>
        <p class="skill-rejection-reason" style="color:#ef4444;font-size:0.82rem;margin-top:6px;">Motif : <?= h($sk['motif_refus']) ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>

<!-- Declare Skill Modal -->
<div class="modal" id="declare-modal" hidden aria-hidden="true">
  <div class="modal-backdrop" onclick="this.closest('.modal').hidden=true"></div>
  <div class="modal-content">
    <h2>Déclarer une compétence</h2>
    <form id="declare-form" method="post" action="../backend/api/competences.php?action=declarer">
      <div class="form-group">
        <label for="skill-select">Compétence</label>
        <select id="skill-select" name="competence_id" class="form-input" required>
          <option value="">Choisir...</option>
          <?php foreach ($allSkills as $sk): ?>
          <option value="<?= $sk['id'] ?>"><?= h($sk['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="skill-level">Niveau estimé</label>
        <select id="skill-level" name="niveau_estime" class="form-input">
          <option value="Débutant">Débutant</option>
          <option value="Intermédiaire" selected>Intermédiaire</option>
          <option value="Avancé">Avancé</option>
        </select>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-sm btn-secondary" onclick="document.getElementById('declare-modal').hidden=true">Annuler</button>
        <button type="submit" class="btn-sm btn-primary">Déclarer</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('declare-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = Object.fromEntries(new FormData(this));

  try {
    const resp = await fetch('../backend/api/competences.php?action=declarer', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: JSON.stringify(data)
    });
    if (!resp.ok) { showToast('Erreur serveur ' + resp.status, 'error'); return; }
    let result;
    try { result = await resp.json(); } catch {
      showToast('Réponse invalide du serveur (vérifiez que MySQL est démarré)', 'error');
      return;
    }
    if (result.success) {
      showToast('Compétence déclarée !', 'success');
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur de connexion : ' + (err.message || 'requête échouée'), 'error');
  }
});
</script>

<style>
.skills-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
.skill-card { background: var(--white); border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.skill-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
.skill-card-header h3 { margin: 0; font-size: 1.05rem; }
.skill-category { color: #9CA3AF; font-size: 0.85rem; margin: 4px 0; }
.skill-meta { display: flex; gap: 16px; font-size: 0.9rem; color: #4B5563; margin: 8px 0; }
.skill-validated-by { font-size: 0.82rem; color: #6B7280; }
.skill-rejection-reason { font-size: 0.82rem; color: #EF4444; }
.modal { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; }
.modal:not([hidden]) { display: flex; }
.modal-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
.modal-content { position: relative; background: #fff; border-radius: 12px; padding: 24px; max-width: 460px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
.modal-content h2 { margin-top: 0; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-weight: 600; font-size: 0.85rem; color: var(--gray-700); }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
</style>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
