<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$user['id'];

$pageTitle = 'ISMO-SkillSwap — Mentor Dashboard';
$currentPage = 'dashboard';
$basePath = '..';

// Stats
$stmt = $db->prepare("SELECT COUNT(*) FROM propositions_aide WHERE proposant_id = ?");
$stmt->execute([$userId]);
$totalResponses = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM propositions_aide WHERE proposant_id = ? AND statut = 'Acceptée'");
$stmt->execute([$userId]);
$accepted = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT ROUND(AVG(d.note_mentor), 1) FROM demandes_aide d JOIN propositions_aide p ON p.demande_id = d.id WHERE p.proposant_id = ? AND d.note_mentor IS NOT NULL");
$stmt->execute([$userId]);
$avgRating = $stmt->fetchColumn() ?: '—';

// Open help requests
$stmt = $db->prepare("SELECT d.*, c.nom AS skill_name, CONCAT(u.prenom, ' ', u.nom) as owner_name FROM demandes_aide d JOIN competences_catalogue c ON d.competence_id = c.id JOIN utilisateurs u ON d.auteur_id = u.id WHERE d.statut NOT IN ('Résolu') ORDER BY d.creee_le DESC LIMIT 10");
$stmt->execute();
$openRequests = $stmt->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Tableau de bord Mentor</h1>
        <p class="page-sub">Aidez les autres et développez votre réputation</p>
      </div>
    </div>

    <div class="quick-stats">
      <div class="qstat-card">
        <div class="qstat-icon qstat-blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
        <div class="qstat-info">
          <span class="qstat-value"><?= $totalResponses ?></span>
          <span class="qstat-label">Propositions envoyées</span>
        </div>
      </div>
      <div class="qstat-card">
        <div class="qstat-icon qstat-green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
        <div class="qstat-info">
          <span class="qstat-value"><?= $accepted ?></span>
          <span class="qstat-label">Aides acceptées</span>
        </div>
      </div>
      <div class="qstat-card">
        <div class="qstat-icon qstat-orange"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
        <div class="qstat-info">
          <span class="qstat-value"><?= $avgRating ?></span>
          <span class="qstat-label">Note moyenne</span>
        </div>
      </div>
    </div>

    <h2 class="section-label">Demandes d'aide ouvertes</h2>
    <?php if (empty($openRequests)): ?><p class="text-muted">Aucune demande ouverte pour le moment.</p>
    <?php else: ?>
    <div class="requests-list">
      <?php foreach ($openRequests as $req): ?>
      <div class="request-card">
        <div class="request-card-top">
          <h3 class="request-title"><?= h($req['titre'])?></h3>
          <span class="badge badge-pending"><?= h($req['statut'])?></span>
        </div>
        <p class="request-desc"><?= h(mb_substr($req['description'], 0, 150))?></p>
        <div class="request-tags">
          <span class="tag tag-blue"><?= h($req['skill_name'])?></span>
          <span class="tag tag-gray">par <?= h($req['owner_name'])?></span>
        </div>
        <button class="btn-sm btn-offer" onclick="openDashProposal(<?= $req['id']?>)">Proposer mon aide</button>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>

<!-- Proposal modal -->
<div class="proposal-overlay" id="dash-proposal-modal" style="display:none;">
  <div class="proposal-dialog">
    <div class="proposal-head">
      <h3 class="proposal-title">Proposer mon aide</h3>
      <button class="proposal-close" onclick="closeDashProposal()" type="button" aria-label="Fermer">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="proposal-body">
      <div class="proposal-problem">
        <h4 id="dash-problem-title"></h4>
        <p id="dash-problem-desc"></p>
      </div>
      <div class="proposal-field">
        <label for="dash-solution-text">Votre solution / démonstration</label>
        <textarea id="dash-solution-text" rows="6" placeholder="Expliquez étape par étape votre solution..."></textarea>
      </div>
    </div>
    <div class="proposal-foot">
      <button class="pbtn pbtn-outline" onclick="closeDashProposal()" type="button">Annuler</button>
      <button class="pbtn pbtn-primary" id="dash-submit-solution" type="button">Proposer ma solution</button>
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

.request-card .btn-offer {
  background: var(--blue-600);
  color: var(--white);
  border: none;
  width: 100%;
  margin-top: 6px;
  font-weight: 600;
  justify-content: center;
  padding: 9px 14px;
  font-size: .85rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background var(--transition), transform var(--transition);
}
.request-card .btn-offer:hover {
  background: var(--blue-700);
  transform: translateY(-1px);
}
</style>

<script>
const CSRF_TOKEN = '<?= csrfToken() ?>';
const REQUESTS_DATA = <?= json_encode(array_map(function($r) {
    return ['id' => (int)$r['id'], 'title' => $r['titre'], 'description' => $r['description']];
}, $openRequests)) ?>;

let dashProposalId = null;

function openDashProposal(requestId) {
  const data = REQUESTS_DATA.find(r => r.id === requestId);
  if (!data) return;
  dashProposalId = requestId;
  document.getElementById('dash-problem-title').textContent = data.title;
  document.getElementById('dash-problem-desc').textContent = data.description;
  document.getElementById('dash-solution-text').value = '';
  document.getElementById('dash-proposal-modal').style.display = 'flex';
}

function closeDashProposal() {
  document.getElementById('dash-proposal-modal').style.display = 'none';
  dashProposalId = null;
}

document.getElementById('dash-proposal-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeDashProposal();
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && document.getElementById('dash-proposal-modal').style.display === 'flex') closeDashProposal();
});

document.getElementById('dash-submit-solution')?.addEventListener('click', async () => {
  if (!dashProposalId) return;
  const message = document.getElementById('dash-solution-text').value.trim();
  try {
    const resp = await fetch('../backend/api/propositions.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
      body: JSON.stringify({ demande_id: parseInt(dashProposalId), message })
    });
    const result = await resp.json();
    showToast(result.success ? 'Proposition envoyée !' : (result.error || 'Erreur'), result.success ? 'success' : 'error');
    if (result.success) { closeDashProposal(); setTimeout(() => location.reload(), 800); }
  } catch(e) {
    showToast('Erreur de connexion', 'error');
  }
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
