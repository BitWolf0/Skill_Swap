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

<main class="content-area !grid-cols-1" id="main-content" style="grid-template-columns:1fr !important;">
  <section class="content-main" style="grid-column:1/-1;">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="page-title" style="margin:0;">Gestion des Comptes</h1>
        <p class="page-sub" style="margin-top:4px;"><?= count($users) ?> utilisateur(s) · Gérez les comptes de la plateforme</p>
      </div>
      <div class="flex items-center gap-2 text-sm text-gray-400">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
        <span>Dernière synchro il y a 1 min</span>
      </div>
    </div>

    <!-- Filters -->
    <form method="get" style="display:flex;align-items:center;gap:12px;background:#fff;padding:16px;border-radius:16px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.05);margin-bottom:24px;width:100%;">
      <div style="position:relative;flex:1;">
        <div style="position:absolute;inset:0;right:auto;left:12px;display:flex;align-items:center;pointer-events:none;color:#94a3b8;">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input type="text" name="search" placeholder="Rechercher un utilisateur..." value="<?= h($search) ?>"
          style="width:100%;height:44px;padding:0 16px 0 40px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;color:#334155;outline:none;transition:all 0.15s ease;box-sizing:border-box;" />
      </div>
      <select name="role" style="width:180px;height:44px;padding:0 12px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;color:#334155;outline:none;transition:all 0.15s ease;cursor:pointer;flex-shrink:0;box-sizing:border-box;">
        <option value="">Tous les rôles</option>
        <option value="stagiaire" <?= $roleFilter === 'stagiaire' ? 'selected' : '' ?>>Stagiaire</option>
        <option value="mentor" <?= $roleFilter === 'mentor' ? 'selected' : '' ?>>Mentor</option>
        <option value="formateur" <?= $roleFilter === 'formateur' ? 'selected' : '' ?>>Formateur</option>
        <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>
      <button type="submit" style="height:44px;padding:0 24px;background:#9333ea;color:#fff;font-weight:500;font-size:0.82rem;border-radius:8px;border:none;cursor:pointer;flex-shrink:0;white-space:nowrap;transition:background 0.15s ease;display:flex;align-items:center;justify-content:center;box-sizing:border-box;">Filtrer</button>
      <?php if ($search || $roleFilter): ?>
      <a href="gestion_comptes.php" style="height:44px;padding:0 16px;border-radius:8px;border:1px solid #e2e8f0;font-size:0.82rem;font-weight:500;color:#64748b;background:#fff;display:inline-flex;align-items:center;justify-content:center;white-space:nowrap;text-decoration:none;flex-shrink:0;transition:background 0.15s ease;box-sizing:border-box;">Réinitialiser</a>
      <?php endif; ?>
    </form>

    <?php if (!empty($pending)): ?>
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:16px;padding:20px;margin-bottom:24px;width:100%;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div>
          <h2 style="margin:0;font-size:1.05rem;font-weight:700;color:#991b1b;">Comptes en attente de validation</h2>
          <p style="margin:4px 0 0;font-size:0.85rem;color:#b91c1c;"><?= count($pending) ?> inscription(s) à valider</p>
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:1px solid #fecaca;">
              <th style="padding:8px 12px;text-align:left;font-size:0.75rem;font-weight:600;color:#991b1b;text-transform:uppercase;">Nom</th>
              <th style="padding:8px 12px;text-align:left;font-size:0.75rem;font-weight:600;color:#991b1b;text-transform:uppercase;">Email</th>
              <th style="padding:8px 12px;text-align:left;font-size:0.75rem;font-weight:600;color:#991b1b;text-transform:uppercase;">Rôle</th>
              <th style="padding:8px 12px;text-align:left;font-size:0.75rem;font-weight:600;color:#991b1b;text-transform:uppercase;">Date</th>
              <th style="padding:8px 12px;text-align:center;font-size:0.75rem;font-weight:600;color:#991b1b;text-transform:uppercase;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pending as $p): ?>
            <tr style="border-bottom:1px solid #fee2e2;">
              <td style="padding:8px 12px;font-weight:600;font-size:0.87rem;color:#1e293b;"><?= h($p['prenom'] . ' ' . $p['nom']) ?></td>
              <td style="padding:8px 12px;font-size:0.82rem;color:#475569;"><?= h($p['email']) ?></td>
              <td style="padding:8px 12px;font-size:0.82rem;color:#475569;"><?= roleLabel($p['role']) ?></td>
              <td style="padding:8px 12px;font-size:0.82rem;color:#475569;"><?= date('d/m/Y', strtotime($p['date_inscription'])) ?></td>
              <td style="padding:8px 12px;text-align:center;">
                <button class="toggle-status" style="background:#16a34a;color:#fff;border:none;padding:6px 16px;border-radius:6px;cursor:pointer;font-size:0.82rem;font-weight:600;"
                  data-id="<?= $p['id'] ?>" data-current="0">
                  Valider
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
      <div style="overflow-x:auto;">
        <table class="data-table" style="margin-bottom:0;">
          <thead>
            <tr>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Nom</th>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Email</th>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Rôle</th>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Statut</th>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Points</th>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Inscrit le</th>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Connexion</th>
              <th style="padding:0.85rem 1rem;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr class="transition" style="transition:background 0.15s ease;">
              <td style="padding:0.75rem 1rem;">
                <div class="flex items-center gap-3">
                  <div class="flex-shrink-0 flex items-center justify-center" style="width:34px;height:34px;border-radius:999px;background:var(--blue-100);color:var(--blue-600);font-size:0.75rem;font-weight:700;">
                    <?= mb_substr(h($u['prenom']), 0, 1) ?><?= mb_substr(h($u['nom']), 0, 1) ?>
                  </div>
                  <div>
                    <div style="font-weight:600;font-size:0.87rem;color:var(--gray-800);"><?= h($u['prenom'] . ' ' . $u['nom']) ?></div>
                  </div>
                </div>
              </td>
              <td style="padding:0.75rem 1rem;font-size:0.82rem;color:var(--gray-500);"><?= h($u['email']) ?></td>
              <td style="padding:0.75rem 1rem;">
                <?php
                  $roleBadge = match($u['role']) {
                    'administrateur' => ['bg-purple-100', '#6B21A8'],
                    'formateur'      => ['bg-orange-100', '#EA580C'],
                    default          => ['bg-blue-50', 'var(--blue-600)'],
                  };
                ?>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background:<?= $roleBadge[0] ?>;color:<?= $roleBadge[1] ?>;">
                  <?= roleLabel($u['role']) ?>
                </span>
              </td>
              <td style="padding:0.75rem 1rem;">
                <div class="flex items-center gap-1.5" style="min-height:24px;">
                  <?php if ($u['est_actif']): ?>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background:var(--green-100);color:var(--green-600);line-height:1.4;">Actif</span>
                  <?php else: ?>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background:var(--red-50);color:var(--red-500);line-height:1.4;">Suspendu</span>
                  <?php endif; ?>
                  <?php if ($u['disponible']): ?>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="background:var(--blue-50);color:var(--blue-600);line-height:1.4;margin-left:2px;">Dispo</span>
                  <?php endif; ?>
                </div>
              </td>
              <td style="padding:0.75rem 1rem;font-size:0.82rem;font-weight:600;color:var(--gray-700);"><?= (int)$u['points_gamification'] ?> pts</td>
              <td style="padding:0.75rem 1rem;font-size:0.82rem;color:var(--gray-500);"><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
              <td style="padding:0.75rem 1rem;font-size:0.82rem;color:var(--gray-500);">
                <?= $u['derniere_connexion'] ? date('d/m/Y H:i', strtotime($u['derniere_connexion'])) : '<span class="text-gray-400">Jamais</span>' ?>
              </td>
              <td style="padding:0.75rem 1rem;text-align:center;">
                <button class="toggle-status inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border transition" style="border-color:var(--gray-200);background:var(--white);<?= $u['est_actif'] ? 'color:var(--red-500);' : 'color:var(--green-600);' ?>"
                  data-id="<?= $u['id'] ?>" data-current="<?= $u['est_actif'] ?>"
                  onmouseover="this.style.background='<?= $u['est_actif'] ? 'var(--red-50)' : 'var(--green-100)' ?>'"
                  onmouseout="this.style.background='var(--white)'">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <?php if ($u['est_actif']): ?>
                      <path d="M10 15l-5-5m0 0l5-5m-5 5h12"/><path d="M20 4v16"/>
                    <?php else: ?>
                      <path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>
                    <?php endif; ?>
                  </svg>
                  <?= $u['est_actif'] ? 'Suspendre' : 'Activer' ?>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
            <tr><td colspan="8" class="text-center py-8 text-gray-400">Aucun utilisateur trouvé</td></tr>
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
</script>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
