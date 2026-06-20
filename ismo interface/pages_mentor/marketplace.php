<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();

$pageTitle = 'ISMO-SkillSwap — Marketplace';
$currentPage = 'marketplace';
$basePath = '..';

$skillId = (int)($_GET['skill_id'] ?? 0);
$userId = (int)$user['id'];

$sql = "SELECT d.*, c.nom AS skill_name, c.categorie AS skill_category, CONCAT(u.prenom, ' ', u.nom) as owner_name FROM demandes_aide d JOIN competences_catalogue c ON d.competence_id = c.id JOIN utilisateurs u ON d.auteur_id = u.id WHERE d.statut NOT IN ('Résolu') AND d.auteur_id != ?";
$params = [$userId];

if ($skillId) {
    $sql .= ' AND d.competence_id = ?';
    $params[] = $skillId;
}
$sql .= ' ORDER BY d.creee_le DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll();

// Check if current user already made a proposal to each request
$respondedRequests = [];
if (!empty($listings)) {
    $ids = array_column($listings, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $respStmt = $db->prepare("SELECT demande_id FROM propositions_aide WHERE proposant_id = ? AND demande_id IN ($placeholders)");
    $respStmt->execute(array_merge([$userId], $ids));
    $respondedRequests = $respStmt->fetchAll(\PDO::FETCH_COLUMN);
}

// All skills for filter
$skills = $db->query("SELECT * FROM competences_catalogue ORDER BY nom")->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Marketplace d'Entraide</h1>
        <p class="page-sub">Trouvez des demandes d'aide ouvertes</p>
      </div>
      <button class="btn-publish" onclick="window.location.href='nouvelle_demande.php'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Publier
      </button>
    </div>

    <form method="get" class="filters-bar" style="display:flex;gap:12px;margin-bottom:20px;">
      <select name="skill_id" class="form-input" style="width:auto;min-width:180px;" onchange="this.form.submit()">
        <option value="">Toutes les compétences</option>
        <?php foreach ($skills as $s): ?>
        <option value="<?= $s['id'] ?>" <?= $skillId === (int)$s['id'] ? 'selected' : '' ?>><?= h($s['nom']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($skillId): ?>
      <a href="?" class="filter-btn">Réinitialiser</a>
      <?php endif; ?>
    </form>

    <?php if (empty($listings)): ?><p class="text-muted">Aucune demande ouverte pour le moment.</p>
    <?php else: ?>
    <div class="requests-list">
      <?php foreach ($listings as $item): ?>
      <div class="request-card" data-request-id="<?= $item['id'] ?>">
        <div class="request-card-top">
          <h3 class="request-title"><?= h($item['titre']) ?></h3>
          <span class="badge badge-pending">Demande</span>
        </div>
        <p class="request-desc"><?= h(mb_substr($item['description'], 0, 200)) ?></p>
        <div class="request-tags">
          <span class="tag tag-blue"><?= h($item['skill_name']) ?></span>
          <span class="tag tag-gray">par <?= h($item['owner_name']) ?></span>
        </div>
        <button class="btn-help" data-request-id="<?= $item['id'] ?>" <?= in_array($item['id'], $respondedRequests) ? 'disabled' : '' ?>>
          <?= in_array($item['id'], $respondedRequests) ? 'Déjà proposé' : 'Proposer mon aide' ?>
        </button>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>

<!-- Proposal modal -->
<div class="proposal-overlay" id="proposal-modal" style="display:none;">
  <div class="proposal-dialog">
    <div class="proposal-head">
      <h3 class="proposal-title">Proposer mon aide</h3>
      <button class="proposal-close" id="proposal-close" type="button" aria-label="Fermer">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="proposal-body">
      <input type="hidden" id="proposal-request-id" value="">
      <div class="proposal-problem">
        <h4 id="proposal-problem-title"></h4>
        <p id="proposal-problem-desc"></p>
      </div>
      <div class="proposal-field">
        <label for="proposal-text">Votre solution / démonstration</label>
        <textarea id="proposal-text" rows="6" placeholder="Expliquez étape par étape votre solution..."></textarea>
      </div>
    </div>
    <div class="proposal-foot">
      <button class="pbtn pbtn-outline" id="proposal-cancel" type="button">Annuler</button>
      <button class="pbtn pbtn-primary" id="proposal-submit" type="button">Proposer ma solution</button>
    </div>
  </div>
</div>

<style>
.proposal-overlay { position:fixed; inset:0; z-index:1200; background:rgba(15,23,42,0.5); backdrop-filter:blur(3px); align-items:center; justify-content:center; }
.proposal-dialog { background:#fff; border-radius:16px; max-width:620px; width:94%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.18); animation:proposalIn 0.2s ease-out; }
@keyframes proposalIn { from{opacity:0;transform:translateY(12px)scale(0.97)} to{opacity:1;transform:translateY(0)scale(1)} }
.proposal-head { display:flex; align-items:center; justify-content:space-between; padding:20px 24px 0; }
.proposal-title { margin:0; font-size:1.1rem; font-weight:700; color:#0f172a; }
.proposal-close { width:36px; height:36px; border:none; background:transparent; border-radius:8px; cursor:pointer; color:#94a3b8; display:flex; align-items:center; justify-content:center; transition:background 0.15s; }
.proposal-close:hover { background:#f1f5f9; color:#475569; }
.proposal-body { padding:16px 24px 20px; }
.proposal-problem { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:20px; }
.proposal-problem h4 { margin:0 0 6px; font-size:0.95rem; font-weight:600; color:#0f172a; }
.proposal-problem p { margin:0; font-size:0.85rem; color:#475569; line-height:1.5; }
.proposal-field label { display:block; font-size:0.85rem; font-weight:600; color:#334155; margin-bottom:8px; }
.proposal-field textarea { width:100%; min-height:130px; padding:12px 14px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; font-family:inherit; font-size:0.9rem; color:#0f172a; line-height:1.5; resize:vertical; transition:border-color 0.15s,box-shadow 0.15s; box-sizing:border-box; }
.proposal-field textarea:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.1); }
.proposal-field textarea::placeholder { color:#94a3b8; }
.proposal-foot { display:flex; gap:10px; justify-content:flex-end; padding:0 24px 20px; }
.pbtn { font-family:inherit; font-size:0.85rem; font-weight:600; padding:10px 20px; border-radius:10px; cursor:pointer; transition:all 0.15s; }
.pbtn-primary { background:#3b82f6; color:#fff; border:none; }
.pbtn-primary:hover { background:#2563eb; }
.pbtn-outline { background:#fff; color:#475569; border:1px solid #e2e8f0; }
.pbtn-outline:hover { background:#f8fafc; border-color:#cbd5e1; }
</style>

<script>
const CSRF_TOKEN = '<?= csrfToken() ?>';
const LISTINGS_DATA = <?= json_encode(array_map(function($item) {
    return ['id' => (int)$item['id'], 'title' => $item['titre'], 'description' => $item['description']];
}, $listings)) ?>;

document.querySelectorAll('.btn-help').forEach(btn => {
  btn.addEventListener('click', function() {
    const reqId = parseInt(this.dataset.requestId);
    const data = LISTINGS_DATA.find(r => r.id === reqId);
    if (!data) return;
    document.getElementById('proposal-request-id').value = reqId;
    document.getElementById('proposal-problem-title').textContent = data.title;
    document.getElementById('proposal-problem-desc').textContent = data.description;
    document.getElementById('proposal-text').value = '';
    document.getElementById('proposal-modal').style.display = 'flex';
  });
});
document.getElementById('proposal-close')?.addEventListener('click', () => document.getElementById('proposal-modal').style.display = 'none');
document.getElementById('proposal-cancel')?.addEventListener('click', () => document.getElementById('proposal-modal').style.display = 'none');
document.getElementById('proposal-modal')?.addEventListener('click', function(e) {
  if (e.target === this) document.getElementById('proposal-modal').style.display = 'none';
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && document.getElementById('proposal-modal').style.display === 'flex') document.getElementById('proposal-modal').style.display = 'none';
});
document.getElementById('proposal-submit')?.addEventListener('click', async () => {
  const demandeId = document.getElementById('proposal-request-id').value;
  const message = document.getElementById('proposal-text').value.trim();
  if (!demandeId) return;
  if (!message) { showToast('Veuillez écrire votre solution avant d\'envoyer', 'error'); return; }
  try {
    const resp = await fetch('../backend/api/propositions.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
      body: JSON.stringify({ demande_id: parseInt(demandeId), message })
    });
    const result = await resp.json();
    showToast(result.success ? 'Proposition envoyée !' : (result.error || 'Erreur'), result.success ? 'success' : 'error');
    if (result.success) { document.getElementById('proposal-modal').style.display = 'none'; setTimeout(() => location.reload(), 800); }
  } catch(e) { showToast('Erreur de connexion', 'error'); }
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
