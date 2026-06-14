<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole(ROLE_ADMINISTRATEUR);

$db = Database::getInstance();
$pageTitle = 'ISMO-SkillSwap — Gestion des Badges';
$currentPage = 'gestion_badges';
$basePath = '..';

// Fetch all badges
$badges = $db->query("
    SELECT b.*,
           (SELECT COUNT(*) FROM badges_stagiaire WHERE badge_id = b.id) AS nb_attributions
    FROM badges b
    ORDER BY b.est_actif DESC, b.categorie, b.nom
")->fetchAll();

// Fetch all users for the assign modal
$users = $db->query("SELECT id, prenom, nom, role, email FROM utilisateurs ORDER BY nom, prenom")->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>
<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Gestion des Badges</h1>
        <p class="page-sub">Créez, attribuez et gérez les badges de la plateforme</p>
      </div>
      <div class="page-actions">
        <button class="btn-publish" onclick="document.getElementById('modal-add-badge').classList.add('open')">
          + Nouveau badge
        </button>
      </div>
    </div>

    <?php if (empty($badges)): ?>
    <div class="card" style="padding:2rem;text-align:center;">
      <p style="font-size:1.1rem;color:var(--text-muted);">Aucun badge créé pour le moment.</p>
    </div>
    <?php else: ?>
    <div class="badges-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
      <?php foreach ($badges as $b): ?>
      <div class="card" style="padding:20px;position:relative;opacity:<?= $b['est_actif'] ? '1' : '0.5' ?>;">
        <div style="display:flex;align-items:flex-start;gap:16px;">
          <div style="font-size:2rem;width:48px;height:48px;display:flex;align-items:center;justify-content:center;background:var(--bg-card-hover);border-radius:12px;flex-shrink:0;">
            <?php if ($b['icone']): ?><?= h($b['icone']) ?><?php else: ?><span class="icon-medal" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></span><?php endif; ?>
          </div>
          <div style="flex:1;min-width:0;">
            <h3 style="margin:0 0 4px;font-size:1rem;"><?= h($b['nom']) ?></h3>
            <p style="margin:0 0 8px;font-size:0.85rem;color:var(--text-muted);">
              <?= h($b['description'] ?: 'Aucune description') ?>
            </p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;font-size:0.8rem;">
              <?php if ($b['categorie']): ?><span class="tag tag-blue"><?= h($b['categorie']) ?></span><?php endif; ?>
              <?php if ($b['points_requis'] > 0): ?><span class="tag tag-orange"><span class="icon-trophy" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg></span> <?= (int)$b['points_requis'] ?> pts</span><?php endif; ?>
              <span class="tag <?= $b['est_actif'] ? 'tag-green' : 'tag-gray' ?>">
                <?= $b['est_actif'] ? 'Actif' : 'Désactivé' ?>
              </span>
              <span class="tag"><span class="icon-users" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span> <?= $b['nb_attributions'] ?> attribution(s)</span>
            </div>
          </div>
        </div>
        <div style="margin-top:16px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid var(--border-color);padding-top:12px;">
          <button class="btn-sm btn-primary" style="border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:0.85rem;background:var(--accent-color);color:#fff;"
                  onclick="openAssign(<?= $b['id'] ?>, '<?= h(addslashes($b['nom'])) ?>')">
            Attribuer
          </button>
          <?php if ($b['est_actif']): ?>
          <button class="btn-sm btn-danger" style="border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:0.85rem;background:#e74c3c;color:#fff;"
                  onclick="confirm('Désactiver ce badge ?') && deleteBadge(<?= $b['id'] ?>)">
            Désactiver
          </button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>

<!-- Modal: Add Badge -->
<div class="modal" id="modal-add-badge" aria-hidden="true">
  <div class="modal-overlay" onclick="this.closest('.modal').classList.remove('open')"></div>
  <div class="modal-content" style="max-width:500px;">
    <div class="modal-header">
      <h2>Nouveau badge</h2>
      <button class="modal-close" onclick="this.closest('.modal').classList.remove('open')" aria-label="Fermer"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form id="form-add-badge" onsubmit="return createBadge(event)">
      <div class="modal-body">
        <div class="form-group">
          <label for="badge-nom">Nom du badge</label>
          <input type="text" id="badge-nom" name="nom" class="form-input" required />
        </div>
        <div class="form-group">
          <label for="badge-description">Description</label>
          <textarea id="badge-description" name="description" class="form-input" rows="2"></textarea>
        </div>
        <div class="form-row-2">
          <div class="form-group">
            <label for="badge-categorie">Catégorie</label>
            <input type="text" id="badge-categorie" name="categorie" class="form-input" placeholder="ex: Entraide, Compétence" />
          </div>
          <div class="form-group">
            <label for="badge-points">Points requis</label>
            <input type="number" id="badge-points" name="points_requis" class="form-input" value="0" min="0" />
          </div>
        </div>
        <div class="form-group">
          <label for="badge-icone">Icône (emoji)</label>
          <input type="text" id="badge-icone" name="icone" class="form-input" placeholder="ex: 🏅" maxlength="10" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="this.closest('.modal').classList.remove('open')">Annuler</button>
        <button type="submit" class="btn-publish">Créer le badge</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Assign Badge -->
<div class="modal" id="modal-assign-badge" aria-hidden="true">
  <div class="modal-overlay" onclick="this.closest('.modal').classList.remove('open')"></div>
  <div class="modal-content" style="max-width:500px;">
    <div class="modal-header">
      <h2>Attribuer <span id="assign-badge-name"></span></h2>
      <button class="modal-close" onclick="this.closest('.modal').classList.remove('open')" aria-label="Fermer"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form id="form-assign-badge" onsubmit="return assignBadge(event)">
      <div class="modal-body">
        <input type="hidden" name="badge_id" id="assign-badge-id" />
        <div class="form-group">
          <label for="assign-user">Utilisateur</label>
          <select id="assign-user" name="user_id" class="form-input" required>
            <option value="">Sélectionner un utilisateur...</option>
            <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>"><?= h($u['prenom'] . ' ' . $u['nom']) ?> (<?= h($u['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="this.closest('.modal').classList.remove('open')">Annuler</button>
        <button type="submit" class="btn-publish">Attribuer</button>
      </div>
    </form>
  </div>
</div>

<script>
const API = '../backend/api/badges.php';
const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' };

async function createBadge(e) {
  e.preventDefault();
  const form = e.target;
  const data = Object.fromEntries(new FormData(form));
  try {
    const resp = await fetch(API, { method: 'POST', headers, body: JSON.stringify(data) });
    const result = await resp.json();
    if (result.success) {
      showToast('Badge créé !', 'success');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
  return false;
}

function openAssign(badgeId, badgeName) {
  document.getElementById('assign-badge-id').value = badgeId;
  document.getElementById('assign-badge-name').textContent = badgeName;
  document.getElementById('modal-assign-badge').classList.add('open');
}

async function assignBadge(e) {
  e.preventDefault();
  const form = e.target;
  const data = Object.fromEntries(new FormData(form));
  try {
    const resp = await fetch(API + '?action=assign', { method: 'POST', headers, body: JSON.stringify(data) });
    const result = await resp.json();
    if (result.success) {
      showToast('Badge attribué !', 'success');
      document.getElementById('modal-assign-badge').classList.remove('open');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
  return false;
}

async function deleteBadge(badgeId) {
  try {
    const resp = await fetch(API + '?id=' + badgeId, { method: 'DELETE', headers });
    const result = await resp.json();
    if (result.success) {
      showToast('Badge désactivé', 'success');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(result.error || 'Erreur', 'error');
    }
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
}

document.querySelectorAll('.modal-close, .modal-overlay').forEach(el => {
  el.addEventListener('click', function() {
    this.closest('.modal')?.classList.remove('open');
  });
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
