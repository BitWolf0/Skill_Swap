<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$_SESSION['user_id'];

$db = Database::getInstance();

$reqId = (int)($_GET['id'] ?? 0);
if (!$reqId) {
    header('Location: marketplace.php');
    exit;
}

// Fetch request with full joins
$stmt = $db->prepare('
    SELECT d.*,
           c.nom AS competence_nom, c.categorie AS competence_categorie,
           a.id AS auteur_id, a.nom AS auteur_nom, a.prenom AS auteur_prenom,
           a.filiere AS auteur_filiere,
           m.id AS mentor_id, m.nom AS mentor_nom, m.prenom AS mentor_prenom
    FROM demandes_aide d
    JOIN competences_catalogue c ON d.competence_id = c.id
    JOIN utilisateurs a ON d.auteur_id = a.id
    LEFT JOIN utilisateurs m ON d.mentor_id = m.id
    WHERE d.id = ?
');
$stmt->execute([$reqId]);
$demande = $stmt->fetch();

if (!$demande) {
    header('Location: marketplace.php');
    exit;
}

$isAuthor  = $demande['auteur_id'] == $userId;
$isMentor  = $demande['mentor_id'] == $userId && $demande['mentor_id'] !== null;

// Fetch proposals for this request
$props = $db->prepare('
    SELECT p.*, u.nom, u.prenom, u.disponible
    FROM propositions_aide p
    JOIN utilisateurs u ON p.proposant_id = u.id
    WHERE p.demande_id = ?
    ORDER BY p.creee_le DESC
');
$props->execute([$reqId]);
$propositions = $props->fetchAll();

$hasProposed = false;
$userSolution = '';
foreach ($propositions as $p) {
    if ((int)$p['proposant_id'] === $userId) {
        $hasProposed = true;
        if ($p['message']) {
            $userSolution = $p['message'];
        }
        break;
    }
}

// Check if the assigned mentor has submitted a solution
$mentorHasSolution = false;
$mentorSolution = '';
if ($demande['mentor_id']) {
    $solCheck = $db->prepare("SELECT message FROM propositions_aide WHERE demande_id = ? AND proposant_id = ? AND statut = 'Acceptée' AND message IS NOT NULL AND message != '' LIMIT 1");
    $solCheck->execute([$reqId, $demande['mentor_id']]);
    $mentorSolution = $solCheck->fetchColumn();
    $mentorHasSolution = (bool)$mentorSolution;
}

$pageTitle   = 'ISMO-SkillSwap — ' . $demande['titre'];
$currentPage = 'demande_detail';
$basePath    = '..';

include __DIR__ . '/../backend/includes/header.php';
$sidebar = match ($user['role']) {
    'mentor'       => 'sidebar_mentor',
    'formateur'    => 'sidebar_formateur',
    'administrateur' => 'sidebar_admin',
    default        => 'sidebar_stagiaire',
};
include __DIR__ . '/../backend/includes/' . $sidebar . '.php';
include __DIR__ . '/../backend/includes/topbar.php';
?>

<main class="content-area" id="main-content">
  <section class="content-main" style="max-width:900px;margin:0 auto">

    <!-- Back link -->
    <a href="marketplace.php" class="back-link">&larr; Retour au marketplace</a>

    <!-- Request card -->
    <div class="detail-card">
      <div class="detail-header">
        <div>
          <h1 class="detail-title"><?= h($demande['titre']) ?></h1>
          <div class="detail-tags">
            <span class="badge badge-<?= $demande['statut'] === 'Ouvert' ? 'pending' : ($demande['statut'] === 'En cours' ? 'active' : 'completed') ?>">
              <?= $demande['statut'] ?>
            </span>
            <span class="tag tag-blue"><?= h($demande['competence_nom']) ?></span>
            <span class="tag tag-<?= $demande['urgence'] === 'Haute' || $demande['urgence'] === 'Critique' ? 'urgent' : 'gray' ?>">
              Urgence : <?= $demande['urgence'] ?>
            </span>
          </div>
        </div>
        <div class="author-info">
          <span class="avatar-initials"><?= mb_substr(h($demande['auteur_prenom']), 0, 1) ?><?= mb_substr(h($demande['auteur_nom']), 0, 1) ?></span>
          <div>
            <strong><?= h($demande['auteur_prenom']) ?> <?= h($demande['auteur_nom']) ?></strong>
            <small><?= h($demande['auteur_filiere'] ?? 'Filière non spécifiée') ?></small>
          </div>
        </div>
      </div>

      <p class="detail-description"><?= nl2br(h($demande['description'])) ?></p>
      <p class="detail-date">Publiée le <?= date('d/m/Y à H:i', strtotime($demande['creee_le'])) ?></p>
    </div>

    <!-- Mentor info (if assigned) -->
    <?php if ($demande['statut'] === 'En cours' && $demande['mentor_id']): ?>
    <div class="detail-card mentor-assigned">
      <h3>Mentor assigné</h3>
      <div class="author-info">
        <span class="avatar-initials"><?= mb_substr(h($demande['mentor_prenom']), 0, 1) ?><?= mb_substr(h($demande['mentor_nom']), 0, 1) ?></span>
        <div>
          <strong><?= h($demande['mentor_prenom']) ?> <?= h($demande['mentor_nom']) ?></strong>
          <small>À aidé sur cette demande</small>
        </div>
      </div>
      <?php if ($mentorHasSolution): ?>
      <div class="solution-box" style="margin-top:16px">
        <strong class="solution-label">Solution du mentor :</strong>
        <p class="proposal-message" style="margin-top:8px"><?= nl2br(h($mentorSolution)) ?></p>
      </div>
      <?php endif; ?>
      <?php if ($isAuthor): ?>
        <?php if ($mentorHasSolution): ?>
        <button class="btn-primary" onclick="resolveRequest()" style="margin-top:16px">Marquer comme résolu</button>
        <?php else: ?>
        <p class="text-muted" style="margin-top:12px">En attente de la solution du mentor…</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Resolved info + rating -->
    <?php if ($demande['statut'] === 'Résolu'): ?>
    <div class="detail-card resolved-card">
      <h3>Demande résolue</h3>
      <p>Résolue le <?= date('d/m/Y à H:i', strtotime($demande['date_resolution'])) ?></p>
      <?php if ($demande['mentor_id']): ?>
      <div class="author-info" style="margin-top:12px">
        <span class="avatar-initials"><?= mb_substr(h($demande['mentor_prenom']), 0, 1) ?><?= mb_substr(h($demande['mentor_nom']), 0, 1) ?></span>
        <div>
          <strong><?= h($demande['mentor_prenom']) ?> <?= h($demande['mentor_nom']) ?></strong>
        </div>
      </div>
      <?php if ($mentorHasSolution): ?>
      <div class="solution-box" style="margin-top:14px">
        <strong class="solution-label">Solution apportée :</strong>
        <p class="proposal-message" style="margin-top:8px"><?= nl2br(h($mentorSolution)) ?></p>
      </div>
      <?php endif; ?>
      <?php endif; ?>
      <?php if ($demande['note_mentor']): ?>
      <div class="rating-display">
        Note : <?php $_n = (int)$demande['note_mentor']; for ($_i = 0; $_i < 5; $_i++): ?>
          <?php if ($_i < $_n): ?>
            <svg class="star-icon star-filled" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          <?php else: ?>
            <svg class="star-icon star-empty" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($demande['commentaire_mentor']): ?>
        <p class="comment">"<?= h($demande['commentaire_mentor']) ?>"</p>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Submit / update solution (for already assigned mentor when status is En cours) -->
    <?php if ($demande['statut'] === 'En cours' && $isMentor): ?>
    <div class="detail-card">
      <h3>Votre solution</h3>
      <p>Problème : <strong><?= h($demande['titre']) ?></strong></p>
      <div class="problem-box">
        <p class="problem-text"><?= nl2br(h($demande['description'])) ?></p>
      </div>
      <form id="solution-form">
        <input type="hidden" name="demande_id" value="<?= $reqId ?>" />
        <div class="form-group">
          <label for="solution-message">Détaillez votre solution / démonstration</label>
          <textarea id="solution-message" name="message" class="form-input solution-textarea" rows="6" placeholder="Expliquez étape par étape votre solution..."><?= h($userSolution ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn-primary"><?= $userSolution ? 'Mettre à jour la solution' : 'Soumettre la solution' ?></button>
      </form>
    </div>
    <?php endif; ?>

    <!-- Proposals section (visible only to author or when proposing) -->
    <?php if ($demande['statut'] === 'Ouvert'): ?>

      <!-- Propose help button (for non-author, non-mentor) -->
      <?php if (!$isAuthor && !$hasProposed): ?>
      <div class="detail-card">
        <h3>Proposer votre aide</h3>
        <p>Vous souhaitez aider <?= h($demande['auteur_prenom']) ?> ? Cliquez ci-dessous pour voir le détail du problème et proposer votre solution.</p>
        <button class="btn-primary" onclick="openProposalModal()">Proposer mon aide</button>
      </div>
      <?php endif; ?>

      <!-- List of proposals (author only) -->
      <?php if ($isAuthor): ?>
      <div class="detail-card">
        <h3>Propositions reçues (<?= count($propositions) ?>)</h3>
        <?php if (empty($propositions)): ?>
        <p class="text-muted">Aucune proposition pour le moment.</p>
        <?php else: ?>
        <?php foreach ($propositions as $p): ?>
        <div class="proposal-item" data-id="<?= $p['id'] ?>" data-statut="<?= $p['statut'] ?>">
          <div class="author-info">
            <span class="avatar-initials"><?= mb_substr(h($p['prenom']), 0, 1) ?><?= mb_substr(h($p['nom']), 0, 1) ?></span>
            <div>
              <strong><?= h($p['prenom']) ?> <?= h($p['nom']) ?></strong>
              <?php if ($p['disponible']): ?><span class="tag tag-green tag-dispo">Dispo</span><?php endif; ?>
              <small>Proposé le <?= date('d/m/Y', strtotime($p['creee_le'])) ?></small>
            </div>
          </div>
          <?php if ($p['message']): ?>
          <div class="solution-box">
            <strong class="solution-label">Solution proposée :</strong>
            <p class="proposal-message"><?= nl2br(h($p['message'])) ?></p>
          </div>
          <?php endif; ?>
          <div class="proposal-status">
            <?php if ($p['statut'] === 'En attente'): ?>
            <span class="badge badge-pending">En attente</span>
            <div class="proposal-actions">
              <button class="btn-sm btn-primary" onclick="acceptProposal(<?= $p['id'] ?>)">Accepter</button>
              <button class="btn-sm btn-secondary" onclick="rejectProposal(<?= $p['id'] ?>)">Refuser</button>
            </div>
            <?php elseif ($p['statut'] === 'Acceptée'): ?>
            <span class="badge badge-completed">Acceptée</span>
            <?php elseif ($p['statut'] === 'Refusée'): ?>
            <span class="badge badge-closed">Refusée</span>
            <?php elseif ($p['statut'] === 'Retirée'): ?>
            <span class="badge badge-closed">Retirée</span>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <?php endif; ?>

    <?php endif; ?>

  </section>
</main>

<!-- Solution Proposal Modal -->
<div class="proposal-overlay" id="proposal-modal">
  <div class="proposal-dialog">
    <div class="proposal-head">
      <h3 class="proposal-title">Proposer mon aide</h3>
      <button class="proposal-close" onclick="closeProposalModal()" type="button" aria-label="Fermer">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="proposal-body">
      <div class="proposal-problem">
        <h4><?= h($demande['titre']) ?></h4>
        <p><?= nl2br(h($demande['description'])) ?></p>
      </div>
      <form id="propose-form">
        <input type="hidden" name="demande_id" value="<?= $reqId ?>" />
        <div class="proposal-field">
          <label for="propose-message">Votre solution / démonstration</label>
          <textarea id="propose-message" name="message" rows="6" placeholder="Expliquez étape par étape votre solution..."></textarea>
        </div>
        <div class="proposal-foot">
          <button type="button" class="pbtn pbtn-outline" onclick="closeProposalModal()">Annuler</button>
          <button type="submit" class="pbtn pbtn-primary">Proposer ma solution</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Resolve Modal -->
<div class="modal-overlay" id="resolve-modal">
  <div class="modal-card">
    <div class="modal-card-header">
      <h3>Résoudre la demande</h3>
      <button class="modal-close-btn" onclick="closeModal()" type="button" aria-label="Fermer">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-card-body">
      <p>Confirmez-vous que <strong><?= h($demande['mentor_prenom'] ?? 'le mentor') ?> <?= h($demande['mentor_nom'] ?? '') ?></strong> a bien résolu votre demande ?</p>
      <form id="resolve-form">
        <div class="form-group">
          <label>Note pour <?= h($demande['mentor_prenom']) ?> (1-5)</label>
          <div class="star-rating" id="star-rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <button type="button" class="star-btn" data-value="<?= $i ?>"><svg class="star-icon star-empty" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></button>
            <?php endfor; ?>
          </div>
          <input type="hidden" name="note" id="rating-value" value="0" />
        </div>
        <div class="form-group">
          <label for="resolve-comment">Commentaire (optionnel)</label>
          <textarea id="resolve-comment" name="commentaire" class="form-input" rows="2" placeholder="Merci pour votre aide !"></textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="pbtn pbtn-outline" onclick="closeModal()">Annuler</button>
          <button type="submit" class="pbtn pbtn-primary">Confirmer la résolution</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
'use strict';

const userId = <?= $userId ?>;
const CSRF_TOKEN = '<?= csrfToken() ?>';

// ── Proposal modal ──
function openProposalModal() { document.getElementById('proposal-modal').style.display = 'flex'; }
function closeProposalModal() { document.getElementById('proposal-modal').style.display = 'none'; }

// ── Star rating ──
const stars = document.querySelectorAll('.star-btn');
const ratingInput = document.getElementById('rating-value');
stars.forEach(btn => {
  btn.addEventListener('click', () => {
    const val = parseInt(btn.dataset.value);
    ratingInput.value = val;
    stars.forEach((s, i) => {
      s.innerHTML = i < val
        ? '<svg class="star-icon star-filled" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>'
        : '<svg class="star-icon star-empty" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
    });
  });
});

// ── Propose help (modal) ──
const proposeForm = document.getElementById('propose-form');
if (proposeForm) {
  proposeForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(proposeForm);
    const data = Object.fromEntries(formData);
    const message = (data.message || '').trim();
    if (!message) { showToast('Veuillez écrire votre solution avant d\'envoyer', 'error'); return; }
    data.message = message;

    try {
      const res = await fetch('../backend/api/propositions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
        body: JSON.stringify(data),
      });
      const result = await res.json();
      if (result.success) {
        showToast(result.message, 'success');
        closeProposalModal();
        setTimeout(() => location.reload(), 1000);
      } else {
        showToast(result.error || 'Erreur', 'error');
      }
    } catch (err) {
      showToast('Erreur réseau', 'error');
    }
  });
}

// ── Submit / update solution (assigned mentor) ──
const solutionForm = document.getElementById('solution-form');
if (solutionForm) {
  solutionForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(solutionForm);
    const data = Object.fromEntries(formData);
    const message = (data.message || '').trim();
    if (!message) { showToast('Veuillez écrire votre solution avant d\'envoyer', 'error'); return; }
    data.message = message;

    try {
      const res = await fetch('../backend/api/propositions.php?action=update_solution', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
        body: JSON.stringify({ demande_id: parseInt(data.demande_id), message: data.message }),
      });
      const result = await res.json();
      if (result.success) {
        showToast(result.message, 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        showToast(result.error || 'Erreur', 'error');
      }
    } catch (err) {
      showToast('Erreur réseau', 'error');
    }
  });
}

// ── Accept proposal ──
function acceptProposal(propId) {
  if (!confirm('Accepter cette proposition ? Les autres seront refusées.')) return;
  updateProposal(propId, 'Acceptée');
}

function rejectProposal(propId) {
  if (!confirm('Refuser cette proposition ?')) return;
  updateProposal(propId, 'Refusée');
}

async function updateProposal(propId, statut) {
  try {
    const res = await fetch(`../backend/api/propositions.php?id=${propId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
      body: JSON.stringify({ statut }),
    });
    const result = await res.json();
    if (result.success) {
      showToast(result.message, 'success');
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur réseau', 'error');
  }
}

// ── Resolve request ──
function resolveRequest() {
  document.getElementById('resolve-modal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('resolve-modal').style.display = 'none';
}

// Close modals on overlay click
document.getElementById('proposal-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeProposalModal();
});
document.getElementById('resolve-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

const resolveForm = document.getElementById('resolve-form');
if (resolveForm) {
  resolveForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(resolveForm);
    const data = Object.fromEntries(formData);

    if (!data.note || parseInt(data.note) < 1) {
      showToast('Veuillez donner une note (1-5) au mentor', 'error');
      return;
    }

    try {
      const res = await fetch(`../backend/api/propositions.php?action=resoudre&id=<?= $reqId ?>`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
        body: JSON.stringify(data),
      });
      const result = await res.json();
      if (result.success) {
        showToast(result.message, 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        showToast(result.error || 'Erreur', 'error');
      }
    } catch (err) {
      showToast('Erreur réseau', 'error');
    }
  });
}
</script>



<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
