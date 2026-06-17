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

$validated = $db->query('
    SELECT cs.*, c.nom AS competence_nom, c.categorie AS competence_categorie,
           u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom,
           u.filiere AS utilisateur_filiere
    FROM competences_stagiaire cs
    JOIN competences_catalogue c ON cs.competence_id = c.id
    JOIN utilisateurs u ON cs.utilisateur_id = u.id
    WHERE cs.statut_validation = "Validé"
    ORDER BY cs.date_validation DESC
    LIMIT 5
')->fetchAll();

$totalValidated = (int)$db->query("SELECT COUNT(*) FROM competences_stagiaire WHERE statut_validation = 'Validé'")->fetchColumn();

$recentStagiaires = $db->query("
    SELECT id, prenom, nom, filiere, date_inscription
    FROM utilisateurs
    WHERE role = 'stagiaire'
    ORDER BY date_inscription DESC
    LIMIT 5
")->fetchAll();

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
      <div class="qstat-card">
        <div class="qstat-icon qstat-purple"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
        <div class="qstat-info"><span class="qstat-value"><?= $totalValidated ?></span><span class="qstat-label">Compétences validées</span></div>
      </div>
    </div>

    <h2 class="section-label">Validations en attente</h2>
    <?php if (empty($pending)): ?><p class="text-muted">Aucune compétence en attente de validation.</p>
    <?php else: ?>
    <div class="validations-list">
      <?php foreach ($pending as $p): ?>
      <div class="formateur-validation-card">
        <div class="validation-info">
          <strong><?= h($p['utilisateur_prenom']) ?> <?= h($p['utilisateur_nom']) ?></strong>
          <span class="validation-meta"><?= h($p['competence_nom']) ?> — <?= h($p['niveau_estime']) ?></span>
        </div>
        <div class="validation-actions">
          <button class="btn-validate btn-validate-ok" data-id="<?= $p['id'] ?>" data-statut="Validé">Valider</button>
          <button class="btn-validate btn-validate-no" data-id="<?= $p['id'] ?>" data-statut="Refusé">Refuser</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="dashboard-grid">
      <div class="table-card">
        <h2 class="table-card-title">Récents validations</h2>
        <?php if (empty($validated)): ?>
        <p class="text-muted">Aucune validation récente.</p>
        <?php else: ?>
        <div class="responsive-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Apprenant</th>
                <th>Compétence</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($validated as $v): ?>
              <tr>
                <td class="cell-name"><?= h($v['utilisateur_prenom']) ?> <?= h($v['utilisateur_nom']) ?></td>
                <td class="cell-muted"><?= h($v['competence_nom']) ?></td>
                <td class="cell-date"><?= date('d/m/Y', strtotime($v['date_validation'] ?? $v['date_declaration'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

      <div class="table-card">
        <h2 class="table-card-title">Nouveaux apprenants</h2>
        <?php if (empty($recentStagiaires)): ?>
        <p class="text-muted">Aucun apprenant récent.</p>
        <?php else: ?>
        <div class="responsive-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Filière</th>
                <th>Inscrit le</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentStagiaires as $s): ?>
              <tr>
                <td class="cell-name"><?= h($s['prenom']) ?> <?= h($s['nom']) ?></td>
                <td class="cell-muted"><?= h($s['filiere'] ?? '-') ?></td>
                <td class="cell-date"><?= date('d/m/Y', strtotime($s['date_inscription'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
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
