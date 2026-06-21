<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole(ROLE_ADMIN);

$pageTitle = 'ISMO-SkillSwap — Gestion des Comptes';
$currentPage = 'gestion_comptes';
$basePath = '..';

$db = Database::getInstance();

$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';

$sql = "SELECT id, prenom, nom, email, role, est_actif, disponible, points_gamification, date_inscription, derniere_connexion FROM utilisateurs WHERE 1=1";
$params = [];

if ($search) {
    $sql .= ' AND (prenom LIKE ? OR nom LIKE ? OR email LIKE ?)';
    $t = "%$search%";
    $params[] = $t;
    $params[] = $t;
    $params[] = $t;
}
if ($roleFilter) {
    $v4Role = $roleFilter === 'stagiaire' ? 'stagiaire' : ($roleFilter === 'formateur' ? 'formateur' : ($roleFilter === 'admin' ? 'administrateur' : ''));
    if ($v4Role) {
        $sql .= ' AND role = ?';
        $params[] = $v4Role;
    }
}
$sql .= ' ORDER BY date_inscription DESC LIMIT 50';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Pending (inactive) accounts requiring admin validation
$pending = $db->query("SELECT id, prenom, nom, email, role, filiere, date_inscription FROM utilisateurs WHERE est_actif = 0 ORDER BY date_inscription DESC LIMIT 20")->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">

    <!-- Page Header -->
    <div class="page-head gc-head">
      <div class="gc-head-left">
        <div class="gc-head-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4.354a4 4 0 110 5.292M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div>
          <h1 class="gc-head-title">Gestion des Comptes</h1>
          <p class="gc-head-sub"><strong><?= count($users) ?></strong> utilisateur(s) · Gérez les comptes de la plateforme</p>
        </div>
      </div>
      <div class="gc-head-right">
        <div class="gc-sync-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
          <span>Synchro il y a 1 min</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <form method="get" class="filter-bar">
      <div class="search-input-wrap">
        <div class="search-icon">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input type="text" name="search" placeholder="Rechercher un utilisateur..." value="<?= h($search) ?>" class="search-input" />
      </div>
      <select name="role" class="filter-select">
        <option value="">Tous les rôles</option>
        <option value="stagiaire" <?= $roleFilter === 'stagiaire' ? 'selected' : '' ?>>Stagiaire</option>
        <option value="mentor" <?= $roleFilter === 'mentor' ? 'selected' : '' ?>>Mentor</option>
        <option value="formateur" <?= $roleFilter === 'formateur' ? 'selected' : '' ?>>Formateur</option>
        <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>
      <button type="submit" class="filter-btn">Filtrer</button>
      <?php if ($search || $roleFilter): ?>
      <a href="gestion_comptes.php" class="filter-reset">Réinitialiser</a>
      <?php endif; ?>
    </form>

    <?php if (!empty($pending)): ?>
    <div class="pending-alert">
      <div class="pending-alert-header">
        <div>
          <h2 class="pending-alert-title">Comptes en attente de validation</h2>
          <p class="pending-alert-sub"><?= count($pending) ?> inscription(s) à valider</p>
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table class="pending-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Date</th>
              <th class="actions">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pending as $p): ?>
            <tr>
              <td class="name-cell"><?= h($p['prenom'] . ' ' . $p['nom']) ?></td>
              <td class="text-cell"><?= h($p['email']) ?></td>
              <td class="text-cell"><?= roleLabel($p['role']) ?></td>
              <td class="text-cell"><?= date('d/m/Y', strtotime($p['date_inscription'])) ?></td>
              <td class="action-cell">
                <button class="toggle-status pending-valider-btn" data-id="<?= $p['id'] ?>" data-current="0">Valider</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="gc-table-wrap">
      <div class="gc-table-scroll">
        <table class="gc-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Statut</th>
              <th>Points</th>
              <th>Inscrit le</th>
              <th>Connexion</th>
              <th class="center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:12px;">
                  <div class="user-avatar-sm">
                    <?= mb_substr(h($u['prenom']), 0, 1) ?><?= mb_substr(h($u['nom']), 0, 1) ?>
                  </div>
                  <div class="user-name"><?= h($u['prenom'] . ' ' . $u['nom']) ?></div>
                </div>
              </td>
              <td class="email-cell"><?= h($u['email']) ?></td>
              <td>
                <?php
                  $roleClass = match($u['role']) {
                    'administrateur' => 'role-badge--admin',
                    'formateur'      => 'role-badge--formateur',
                    default          => 'role-badge--stagiaire',
                  };
                ?>
                <span class="role-badge <?= $roleClass ?>"><?= roleLabel($u['role']) ?></span>
              </td>
              <td>
                <div style="display:flex;align-items:center;gap:6px;">
                  <?php if ($u['est_actif']): ?>
                    <span class="status-badge status-badge--active">Actif</span>
                  <?php else: ?>
                    <span class="status-badge status-badge--suspended">Suspendu</span>
                  <?php endif; ?>
                  <?php if ($u['disponible']): ?>
                    <span class="status-badge status-badge--dispo">Dispo</span>
                  <?php endif; ?>
                </div>
              </td>
              <td class="points-cell"><?= (int)$u['points_gamification'] ?> pts</td>
              <td class="date-cell"><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
              <td class="date-cell">
                <?= $u['derniere_connexion'] ? date('d/m/Y H:i', strtotime($u['derniere_connexion'])) : 'Jamais' ?>
              </td>
              <td style="text-align:center;">
                <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                <?php if ((int)$_SESSION['user_id'] !== (int)$u['id']): ?>
                <button class="toggle-status btn-toggle <?= $u['est_actif'] ? 'btn-toggle--suspend' : 'btn-toggle--activate' ?>"
                  data-id="<?= $u['id'] ?>" data-current="<?= $u['est_actif'] ?>">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <?php if ($u['est_actif']): ?>
                      <path d="M10 15l-5-5m0 0l5-5m-5 5h12"/><path d="M20 4v16"/>
                    <?php else: ?>
                      <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                    <?php endif; ?>
                  </svg>
                  <?= $u['est_actif'] ? 'Suspendre' : 'Activer' ?>
                </button>
                <?php endif; ?>
                <?php if ($u['role'] !== 'administrateur'): ?>
                <button class="btn-promote-admin" data-id="<?= $u['id'] ?>">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                  Admin
                </button>
                <?php elseif ((int)$_SESSION['user_id'] !== (int)$u['id']): ?>
                <button class="btn-demote-admin" data-id="<?= $u['id'] ?>">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                  Rétrograder
                </button>
                <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
            <tr><td colspan="8" class="empty-state">Aucun utilisateur trouvé</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>
</main>

<script>
document.querySelectorAll('.toggle-status').forEach(btn => {
  btn.addEventListener('click', async function() {
    const id = this.dataset.id;
    const active = this.dataset.current === '1';
    if (!confirm((active ? 'Suspendre' : 'Activer') + ' ce compte ?')) return;
    this.disabled = true;
    this.textContent = '...';
    try {
      const r = await fetch('../backend/api/users.php?id=' + id + '&action=toggle-status', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' }
      });
      const res = await r.json();
      if (res.success) { showToast(res.message, 'success'); setTimeout(() => location.reload(), 600); }
      else showToast(res.error || 'Erreur', 'error');
    } catch(e) { showToast('Erreur de connexion', 'error'); }
    this.disabled = false;
  });
});

document.querySelectorAll('.btn-promote-admin').forEach(btn => {
  btn.addEventListener('click', async function() {
    const id = this.dataset.id;
    if (!confirm('Promouvoir cet utilisateur au rôle Administrateur ?\n\nCette action est irréversible.')) return;
    this.disabled = true;
    this.textContent = '...';
    try {
      const r = await fetch('../backend/api/users.php?id=' + id + '&action=promote_admin', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' }
      });
      const res = await r.json();
      if (res.success) { showToast(res.message, 'success'); setTimeout(() => location.reload(), 600); }
      else showToast(res.error || 'Erreur', 'error');
    } catch(e) { showToast('Erreur de connexion', 'error'); }
    this.disabled = false;
  });
});

document.querySelectorAll('.btn-demote-admin').forEach(btn => {
  btn.addEventListener('click', async function() {
    const id = this.dataset.id;
    if (!confirm('Rétrograder cet administrateur au rôle Stagiaire ?\n\nCette action est irréversible.')) return;
    this.disabled = true;
    this.textContent = '...';
    try {
      const r = await fetch('../backend/api/users.php?id=' + id + '&action=demote_admin', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' }
      });
      const res = await r.json();
      if (res.success) { showToast(res.message, 'success'); setTimeout(() => location.reload(), 600); }
      else showToast(res.error || 'Erreur', 'error');
    } catch(e) { showToast('Erreur de connexion', 'error'); }
    this.disabled = false;
  });
});
</script>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
