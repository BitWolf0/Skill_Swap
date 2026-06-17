<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole(['formateur', 'administrateur']);

$db = Database::getInstance();

$pageTitle   = 'ISMO-SkillSwap — Validation des Compétences';
$currentPage = 'validation_demande';
$basePath    = '..';

// Pending declarations
$pending = $db->query('
    SELECT cs.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
           u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom,
           u.filiere AS utilisateur_filiere
    FROM competences_stagiaire cs
    JOIN competences_catalogue c ON cs.competence_id = c.id
    JOIN utilisateurs u ON cs.utilisateur_id = u.id
    WHERE cs.statut_validation = "En attente"
    ORDER BY cs.date_declaration ASC
')->fetchAll();

// History (last 50 validated/rejected)
$history = $db->query('
    SELECT cs.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
           u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom,
           v.nom AS validateur_nom, v.prenom AS validateur_prenom
    FROM competences_stagiaire cs
    JOIN competences_catalogue c ON cs.competence_id = c.id
    JOIN utilisateurs u ON cs.utilisateur_id = u.id
    LEFT JOIN utilisateurs v ON cs.validateur_id = v.id
    WHERE cs.statut_validation != "En attente"
    ORDER BY cs.date_validation DESC
    LIMIT 50
')->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
include __DIR__ . '/../backend/includes/sidebar_formateur.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>

<main class="content-area" id="main-content">
  <section class="content-main">

    <!-- Pending declarations -->
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Validation des Compétences</h1>
        <p class="page-sub"><?= count($pending) ?> déclaration(s) en attente</p>
      </div>
    </div>

    <?php if (empty($pending)): ?>
    <div class="info-card">
      <p>Aucune déclaration en attente de validation.</p>
    </div>
    <?php else: ?>
    <div class="pending-list">
      <?php foreach ($pending as $p): ?>
      <div class="validation-card" data-id="<?= $p['id'] ?>">
        <div class="validation-left">
          <div class="user-info">
            <div class="user-avatar-sm"><?= mb_substr(h($p['utilisateur_prenom']), 0, 1) ?><?= mb_substr(h($p['utilisateur_nom']), 0, 1) ?></div>
            <div>
              <strong><?= h($p['utilisateur_prenom']) ?> <?= h($p['utilisateur_nom']) ?></strong>
              <small><?= h($p['utilisateur_filiere'] ?? 'N/A') ?></small>
            </div>
          </div>
          <div class="skill-info">
            <span class="tag tag-blue"><?= h($p['competence_nom']) ?></span>
            <span class="tag tag-gray"><?= h($p['niveau_estime']) ?></span>
            <small class="date">Déclaré le <?= date('d/m/Y', strtotime($p['date_declaration'])) ?></small>
          </div>
        </div>
        <div class="validation-actions">
          <button class="btn-validate btn-validate-ok" onclick="valider(<?= $p['id'] ?>, 'Validé')">Valider</button>
          <button class="btn-validate btn-validate-no" onclick="refuser(<?= $p['id'] ?>)">Refuser</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <hr style="margin: 32px 0; border: none; border-top: 1px solid #E5E7EB;" />

    <!-- History -->
    <h2 style="font-size:1.1rem; margin-bottom:16px;">Historique</h2>
    <?php if (empty($history)): ?>
    <p class="text-muted">Aucun historique.</p>
    <?php else: ?>
    <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>Étudiant</th><th>Compétence</th><th>Niveau</th><th>Statut</th><th>Validé par</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($history as $h): ?>
        <tr>
          <td><?= h($h['utilisateur_prenom']) ?> <?= h($h['utilisateur_nom']) ?></td>
          <td><?= h($h['competence_nom']) ?></td>
          <td><?= h($h['niveau_estime']) ?></td>
          <td><span class="badge badge-<?= $h['statut_validation'] === 'Validé' ? 'active' : 'closed' ?>"><?= $h['statut_validation'] ?></span></td>
          <td><?= $h['validateur_prenom'] ? h($h['validateur_prenom']) . ' ' . h($h['validateur_nom']) : '-' ?></td>
          <td><?= $h['date_validation'] ? date('d/m/Y', strtotime($h['date_validation'])) : '-' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
  </section>
</main>

<!-- Refuse Modal -->
<div class="modal-overlay" id="refuse-modal" style="display:none;">
  <div class="modal-card">
    <h3>Refuser la compétence</h3>
    <p>Veuillez indiquer un motif de refus (optionnel).</p>
    <form id="refuse-form">
      <input type="hidden" name="declaration_id" id="refuse-id" value="" />
      <div class="form-group">
        <label for="refuse-motif">Motif du refus</label>
        <textarea id="refuse-motif" name="motif" class="form-input" rows="3" placeholder="Ex: Niveau insuffisant, documentation manquante…"></textarea>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-sm btn-secondary" onclick="document.getElementById('refuse-modal').style.display='none'">Annuler</button>
        <button type="submit" class="btn-validate btn-validate-no">Confirmer le refus</button>
      </div>
    </form>
  </div>
</div>

<script>
'use strict';

function valider(declId, statut) {
  if (!confirm('Valider cette compétence ?')) return;
  updateDeclaration(declId, statut, '');
}

function refuser(declId) {
  document.getElementById('refuse-id').value = declId;
  document.getElementById('refuse-modal').style.display = 'flex';
}

document.getElementById('refuse-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const declId = document.getElementById('refuse-id').value;
  const motif = document.getElementById('refuse-motif').value;
  updateDeclaration(parseInt(declId), 'Refusé', motif);
});

async function updateDeclaration(declId, statut, motif) {
  try {
    const res = await fetch('../backend/api/competences.php?action=valider', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: JSON.stringify({ declaration_id: declId, statut, motif })
    });
    const result = await res.json();
    if (result.success) {
      showToast(result.message, 'success');
      document.getElementById('refuse-modal').style.display = 'none';
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur réseau', 'error');
  }
}
</script>

<style>
.pending-list { display: flex; flex-direction: column; gap: 12px; }
.validation-card { display: flex; justify-content: space-between; align-items: flex-start; background: var(--white); border-radius: var(--radius-md); padding: 16px 20px; box-shadow: var(--shadow-sm); gap: 12px; border: 1px solid var(--gray-100); transition: box-shadow var(--transition); }
.validation-card:hover { box-shadow: var(--shadow-md); }
.validation-left { display: flex; flex-direction: column; gap: 10px; flex: 1; min-width: 0; }
.user-info { display: flex; align-items: center; gap: 10px; }
.user-info strong { font-size: 0.9rem; color: var(--gray-800); }
.user-info small { display: block; color: var(--gray-400); font-size: 0.8rem; margin-top: 1px; }
.user-avatar-sm { width: 38px; height: 38px; border-radius: 50%; background: var(--blue-600); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0; }
.skill-info { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.skill-info .date { color: var(--gray-400); font-size: 0.78rem; }
.validation-actions { display: flex; gap: 8px; flex-shrink: 0; align-items: flex-start; flex-wrap: wrap; }
.info-card { background: #fff; border-radius: 12px; padding: 24px; color: #6B7280; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-card { background: #fff; border-radius: 12px; padding: 24px; max-width: 450px; width: 90%; }
.modal-card h3 { margin-top: 0; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
.table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.data-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.data-table th { background: var(--gray-50); padding: 12px 16px; text-align: left; font-weight: 600; font-size: 0.85rem; color: var(--gray-600); border-bottom: 1px solid var(--gray-200); }
.data-table td { padding: 12px 16px; border-bottom: 1px solid var(--gray-100); font-size: 0.9rem; }
.data-table tbody tr:hover { background: var(--gray-50); }
.badge-closed { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #FEF2F2; color: #EF4444; }
</style>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
