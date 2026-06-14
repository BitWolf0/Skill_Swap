<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole([ROLE_FORMATEUR, ROLE_ADMINISTRATEUR]);
$user = getCurrentUser();
$userId = (int)$user['id'];

$pageTitle = 'ISMO-SkillSwap — Tableau de Bord Formateur';
$currentPage = 'tableau_de_bord';
$basePath = '..';

$db = Database::getInstance();

$stmt = $db->query("SELECT COUNT(*) FROM competences_stagiaire WHERE statut_validation = 'En attente'");
$pendingValidations = (int)$stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM utilisateurs");
$totalUsers = (int)$stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'Ouvert'");
$openRequests = (int)$stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'stagiaire'");
$stagiaires = (int)$stmt->fetchColumn();

$pending = $db->query('
    SELECT cs.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
           u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom,
           u.filiere AS utilisateur_filiere
    FROM competences_stagiaire cs
    JOIN competences_catalogue c ON cs.competence_id = c.id
    JOIN utilisateurs u ON cs.utilisateur_id = u.id
    WHERE cs.statut_validation = "En attente"
    ORDER BY cs.date_declaration ASC
    LIMIT 10
')->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_formateur.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Tableau de bord Formateur</h1>
      </div>
    </div>
    <div class="quick-stats">
      <div class="qstat-card">
        <div class="qstat-icon qstat-blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
        <div class="qstat-info"><span class="qstat-value"><?= $pendingValidations ?></span><span class="qstat-label">Validations en attente</span></div>
      </div>
      <div class="qstat-card">
        <div class="qstat-icon qstat-green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        <div class="qstat-info"><span class="qstat-value"><?= $stagiaires ?></span><span class="qstat-label">Apprenants</span></div>
      </div>
      <div class="qstat-card">
        <div class="qstat-icon qstat-orange"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
        <div class="qstat-info"><span class="qstat-value"><?= $openRequests ?></span><span class="qstat-label">Demandes ouvertes</span></div>
      </div>
    </div>

    <h2 class="section-label">Validations en attente</h2>
    <?php if (empty($pending)): ?><p class="text-muted">Aucune compétence en attente de validation.</p>
    <?php else: ?>
    <div class="validations-list">
      <?php foreach ($pending as $p): ?>
      <div class="validation-card" style="display:flex;justify-content:space-between;align-items:center;background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:8px;">
        <div class="validation-info">
          <strong><?= h($p['utilisateur_prenom']) ?> <?= h($p['utilisateur_nom']) ?></strong>
          <span style="display:block;color:#6B7280;font-size:0.85rem;"><?= h($p['competence_nom']) ?> — <?= h($p['niveau_estime']) ?></span>
        </div>
        <div class="validation-actions" style="display:flex;gap:8px;">
          <button class="btn-sm btn-primary btn-validate" data-id="<?= $p['id'] ?>" data-statut="Validé" style="background:#10B981;color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;">Valider</button>
          <button class="btn-sm btn-secondary btn-validate" data-id="<?= $p['id'] ?>" data-statut="Refusé" style="background:#EF4444;color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;">Refuser</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>

<script>
document.querySelectorAll('.btn-validate').forEach(function(btn) {
  btn.addEventListener('click', async function() {
    const id = this.dataset.id;
    const statut = this.dataset.statut;
    if (!id) return;
    if (statut === 'Refusé') {
      const motif = prompt('Motif du refus (optionnel) :');
      if (motif === null) return;
      await updateDeclaration(id, statut, motif);
    } else {
      if (!confirm('Valider cette compétence ?')) return;
      await updateDeclaration(id, statut, '');
    }
  });
});

async function updateDeclaration(declId, statut, motif) {
  try {
    const resp = await fetch('../backend/api/competences.php?action=valider', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: JSON.stringify({ declaration_id: parseInt(declId), statut, motif })
    });
    const result = await resp.json();
    if (result.success) {
      showToast(result.message, 'success');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
}
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
