<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$user['id'];
$db = Database::getInstance();

$pageTitle = 'ISMO-SkillSwap — Mon Profil';
$currentPage = 'profile';
$basePath = '..';

$viewId = (int)($_GET['id'] ?? $userId);
$profileUser = ($viewId === $userId) ? $user : getUserById($viewId);
if (!$profileUser) redirect('tableau_de_bord.php');
$isOwnProfile = $viewId === $userId;

$skills = $db->prepare("
    SELECT cc.nom, cc.categorie, cs.niveau_estime, cs.statut_validation
    FROM competences_stagiaire cs
    JOIN competences_catalogue cc ON cs.competence_id = cc.id
    WHERE cs.utilisateur_id = ? AND cs.statut_validation = 'Validé'
    ORDER BY cc.nom
");
$skills->execute([$viewId]);
$skills = $skills->fetchAll();

$badges = $db->prepare("
    SELECT b.nom, b.icone, b.description, bs.obtenu_le
    FROM badges_stagiaire bs
    JOIN badges b ON bs.badge_id = b.id
    WHERE bs.utilisateur_id = ?
    ORDER BY bs.obtenu_le DESC
");
$badges->execute([$viewId]);
$badges = $badges->fetchAll();

$helps = $db->prepare("SELECT COUNT(*) FROM demandes_aide WHERE mentor_id = ? AND statut = 'Résolu'");
$helps->execute([$viewId]);
$helpsCount = (int)$helps->fetchColumn();

$level = getUserLevel((int)$profileUser['points_gamification']);

$rawPhoto = $profileUser['photo'] ?? '';
$profilePhoto = $rawPhoto;
if ($rawPhoto && !str_starts_with($rawPhoto, 'http') && !str_starts_with($rawPhoto, '/assets/')) {
    $profilePhoto = $basePath . $rawPhoto;
}
$hasPhoto = $rawPhoto && $rawPhoto !== '/assets/images/default_avatar.svg';

$categories = [];
foreach ($skills as $s) {
    $cat = $s['categorie'] ?: 'Général';
    $categories[$cat][] = $s;
}

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">

    <!-- Profile Hero -->
    <div class="profile-hero" style="background:linear-gradient(135deg,var(--blue-900),var(--blue-700));border-radius:16px;padding:40px;margin-bottom:24px;position:relative;overflow:hidden;">
      <div style="position:absolute;top:-50%;right:-20%;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none;"></div>
      <div style="position:absolute;bottom:-30%;left:-10%;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,0.03);pointer-events:none;"></div>
      <div style="display:flex;align-items:center;gap:32px;flex-wrap:wrap;position:relative;z-index:1;">
        <div style="position:relative;flex-shrink:0;">
          <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:4px solid rgba(255,255,255,0.25);box-shadow:0 8px 32px rgba(0,0,0,0.2);background:var(--blue-600);display:flex;align-items:center;justify-content:center;">
            <?php if ($hasPhoto): ?>
              <img src="<?= h($profilePhoto) ?>" alt="Photo de profil" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;" />
            <?php else: ?>
              <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <?php endif; ?>
          </div>
          <?php if ($isOwnProfile): ?>
          <label for="hero-photo-upload" style="position:absolute;bottom:0;right:0;width:32px;height:32px;border-radius:50%;background:var(--accent-color);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;border:3px solid var(--blue-800);box-shadow:0 2px 8px rgba(0,0,0,0.3);transition:transform 0.2s;" title="Changer la photo">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <input type="file" id="hero-photo-upload" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;" />
          </label>
          <?php endif; ?>
        </div>
        <div style="flex:1;min-width:200px;">
          <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <h1 style="color:#fff;margin:0;font-size:1.75rem;font-weight:700;"><?= h($profileUser['prenom'] . ' ' . $profileUser['nom']) ?></h1>
            <span style="background:rgba(255,255,255,0.15);color:#fff;padding:4px 14px;border-radius:20px;font-size:0.8rem;font-weight:500;"><?= roleLabel($profileUser['role']) ?></span>
            <?php if ($profileUser['filiere']): ?><span style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.8);padding:4px 14px;border-radius:20px;font-size:0.8rem;"><?= h($profileUser['filiere']) ?></span><?php endif; ?>
          </div>
          <?php if ($profileUser['bio']): ?><p style="color:rgba(255,255,255,0.75);margin:12px 0 0;font-size:0.95rem;line-height:1.5;max-width:600px;"><?= h($profileUser['bio']) ?></p><?php endif; ?>
          <div style="display:flex;align-items:center;gap:16px;margin-top:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,0.1);padding:6px 16px 6px 12px;border-radius:20px;">
              <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0;"><?= $level['level'] ?></div>
              <div>
                <div style="color:#fff;font-size:0.8rem;font-weight:600;line-height:1.2;"><?= h($level['name']) ?></div>
                <div style="color:rgba(255,255,255,0.6);font-size:0.7rem;line-height:1.2;"><?= $level['points'] ?> pts</div>
              </div>
            </div>
            <?php if ($profileUser['role'] !== 'administrateur'): ?>
            <span style="display:flex;align-items:center;gap:6px;color:#fff;font-size:0.9rem;">
              <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= $profileUser['disponible'] ? '#22c55e' : '#6b7280' ?>;"></span>
              <?= $profileUser['disponible'] ? 'Disponible' : 'Non disponible' ?>
            </span>
            <?php endif; ?>
          </div>
          <?php if ($level['progress'] < 100): ?>
          <div style="margin-top:16px;max-width:400px;">
            <div style="display:flex;justify-content:space-between;font-size:0.75rem;color:rgba(255,255,255,0.6);margin-bottom:4px;">
              <span>Niveau <?= $level['level'] ?></span>
              <span><?= $level['points'] - $level['current_min'] ?> / <?= $level['next_min'] - $level['current_min'] ?> pts</span>
            </div>
            <div style="height:6px;border-radius:3px;background:rgba(255,255,255,0.15);overflow:hidden;">
              <div style="height:100%;border-radius:3px;background:linear-gradient(90deg,#f59e0b,#fbbf24);width:<?= round($level['progress']) ?>%;transition:width 0.6s ease;"></div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem;">
      <div style="background:#fff;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 2px rgba(0,0,0,0.05);text-align:center;">
        <div style="font-size:1.5rem;font-weight:700;color:#0f172a;"><?= (int)$profileUser['points_gamification'] ?></div>
        <div style="font-size:0.82rem;color:#64748b;margin-top:0.25rem;">Points</div>
      </div>
      <div style="background:#fff;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 2px rgba(0,0,0,0.05);text-align:center;">
        <div style="font-size:1.5rem;font-weight:700;color:#0f172a;"><?= $helpsCount ?></div>
        <div style="font-size:0.82rem;color:#64748b;margin-top:0.25rem;">Aides fournies</div>
      </div>
      <div style="background:#fff;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 2px rgba(0,0,0,0.05);text-align:center;">
        <div style="font-size:1.5rem;font-weight:700;color:#0f172a;"><?= count($badges) ?></div>
        <div style="font-size:0.82rem;color:#64748b;margin-top:0.25rem;">Badges</div>
      </div>
      <div style="background:#fff;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 2px rgba(0,0,0,0.05);text-align:center;">
        <div style="font-size:1.5rem;font-weight:700;color:#0f172a;"><?= count($skills) ?></div>
        <div style="font-size:0.82rem;color:#64748b;margin-top:0.25rem;">Compétences</div>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="main-grid" style="display:grid;grid-template-columns:1fr;gap:1.5rem;align-items:start;margin-top:1.5rem;">

      <!-- LEFT COLUMN -->
      <div class="left-col" style="display:flex;flex-direction:column;gap:1.5rem;">

        <!-- Informations Card -->
        <div style="background:#fff;padding:1.5rem;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
          <h3 style="font-size:0.9rem;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:0.5rem;margin-bottom:1.25rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Informations
          </h3>
          <form id="profile-form">
            <div class="form-row" style="display:grid;grid-template-columns:1fr;gap:1rem;margin-bottom:1rem;">
              <div style="display:flex;flex-direction:column;gap:0.375rem;">
                <label for="edit-prenom" style="font-size:0.82rem;font-weight:500;color:#475569;">Prénom</label>
                <input type="text" id="edit-prenom" name="prenom" value="<?= h($profileUser['prenom']) ?>" style="width:100%;height:40px;padding:0 0.75rem;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;color:#1e293b;box-sizing:border-box;" />
              </div>
              <div style="display:flex;flex-direction:column;gap:0.375rem;">
                <label for="edit-nom" style="font-size:0.82rem;font-weight:500;color:#475569;">Nom</label>
                <input type="text" id="edit-nom" name="nom" value="<?= h($profileUser['nom']) ?>" style="width:100%;height:40px;padding:0 0.75rem;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;color:#1e293b;box-sizing:border-box;" />
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.375rem;margin-bottom:1rem;">
              <label for="edit-filiere" style="font-size:0.82rem;font-weight:500;color:#475569;">Filière</label>
              <input type="text" id="edit-filiere" name="filiere" value="<?= h($profileUser['filiere'] ?? '') ?>" style="width:100%;height:40px;padding:0 0.75rem;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;color:#1e293b;box-sizing:border-box;" />
            </div>
            <div style="display:flex;flex-direction:column;gap:0.375rem;margin-bottom:1rem;">
              <label for="edit-bio" style="font-size:0.82rem;font-weight:500;color:#475569;">Bio</label>
              <textarea id="edit-bio" name="bio" rows="4" placeholder="Parlez de vous, vos compétences, vos centres d'intérêt..." style="width:100%;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;color:#1e293b;padding:0.75rem;box-sizing:border-box;resize:vertical;font-family:inherit;"><?= h($profileUser['bio'] ?? '') ?></textarea>
            </div>
            <!-- Action Footer -->
            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1px solid #e2e8f0;margin-top:1.5rem;flex-wrap:wrap;gap:0.75rem;">
              <?php if ($profileUser['role'] !== 'administrateur'): ?>
              <label style="display:flex;align-items:center;gap:0.625rem;cursor:pointer;user-select:none;">
                <input type="checkbox" id="edit-disponible" name="disponible" value="1" <?= $profileUser['disponible'] ? 'checked' : '' ?> style="width:16px;height:16px;border-radius:4px;accent-color:#4f46e5;" />
                <span style="font-size:0.82rem;color:#475569;">Disponible pour aider</span>
              </label>
              <?php endif; ?>
              <button type="submit" style="height:40px;padding:0 1.25rem;background:#4f46e5;color:#fff;font-size:0.82rem;font-weight:500;border:none;border-radius:8px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                Enregistrer
              </button>
            </div>
          </form>
        </div>



      </div>

      <!-- RIGHT COLUMN -->
      <div style="display:flex;flex-direction:column;gap:1.5rem;">

        <!-- Compétences validées -->
        <div style="background:#fff;padding:1.25rem;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
          <h3 style="font-size:0.9rem;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L15.09 8.26H22L17.55 12.25L19.64 18.5L12 14.01L4.36 18.5L6.45 12.25L2 8.26H8.91L12 2Z"/></svg>
            Compétences validées
          </h3>
          <?php if (empty($skills)): ?>
            <p style="font-size:0.82rem;color:#94a3b8;">Aucune compétence validée pour le moment.</p>
          <?php else: ?>
            <?php foreach ($categories as $cat => $items): ?>
            <div style="margin-bottom:0.75rem;">
              <p style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;"><?= h($cat) ?></p>
              <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                <?php foreach ($items as $s): ?>
                <span style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.375rem 0.75rem;border-radius:8px;font-size:0.82rem;font-weight:500;background:#f8fafc;border:1px solid #e2e8f0;color:#334155;">
                  <?= h($s['nom']) ?>
                  <span style="font-size:0.75rem;color:#94a3b8;font-weight:400;"><?= h($s['niveau_estime']) ?></span>
                </span>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Badges -->
        <div style="background:#fff;padding:1.25rem;border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
          <h3 style="font-size:0.9rem;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            Badges
          </h3>
          <?php if (empty($badges)): ?>
            <p style="font-size:0.82rem;color:#94a3b8;">Aucun badge obtenu pour le moment.</p>
          <?php else: ?>
          <div class="badges-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.75rem;">
            <?php foreach ($badges as $b): ?>
            <div style="display:flex;flex-direction:column;align-items:center;gap:0.375rem;padding:1rem;border-radius:12px;background:#f8fafc;border:1px solid #f1f5f9;text-align:center;">
              <?php if ($b['icone']): ?><span style="font-size:1.5rem;"><?= h($b['icone']) ?></span><?php else: ?><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg><?php endif; ?>
              <span style="font-size:0.82rem;font-weight:600;color:#334155;line-height:1.25;"><?= h($b['nom']) ?></span>
              <span style="font-size:0.7rem;color:#94a3b8;"><?= date('d/m/Y', strtotime($b['obtenu_le'])) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>

      </div>

    </div>

    <style>
      @media (min-width:640px) { .stats-grid { grid-template-columns:repeat(4,1fr) !important; } }
      @media (min-width:1024px) { .main-grid { grid-template-columns:2fr 1fr !important; } }
      @media (min-width:640px) { .form-row { grid-template-columns:repeat(2,1fr) !important; } }
    </style>

  </section>
</main>

<script>
document.getElementById('profile-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = Object.fromEntries(new FormData(this));
  data.disponible = this.querySelector('#edit-disponible').checked ? 1 : 0;
  try {
    const resp = await fetch('../backend/api/users.php?id=<?= $userId ?>', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: JSON.stringify(data)
    });
    const text = await resp.text();
    let result;
    try { result = JSON.parse(text); } catch(e) { result = { error: 'Réponse invalide: ' + text }; }
    if (!result.success && !result.error) result.error = 'Réponse inattendue: ' + text;
    showToast(result.success ? 'Profil mis à jour !' : result.error, result.success ? 'success' : 'error');
    if (result.success) setTimeout(() => location.reload(), 1000);
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
});

document.getElementById('photo-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const resp = await fetch('../backend/api/upload.php', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: formData
    });
    const result = await resp.json();
    showToast(result.success ? 'Photo mise à jour !' : (result.error || 'Erreur'), result.success ? 'success' : 'error');
    if (result.success) setTimeout(() => location.reload(), 1000);
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
});

document.getElementById('hero-photo-upload')?.addEventListener('change', async function() {
  if (this.files.length === 0) return;
  const formData = new FormData();
  formData.append('photo', this.files[0]);
  try {
    const resp = await fetch('../backend/api/upload.php', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: formData
    });
    const result = await resp.json();
    showToast(result.success ? 'Photo mise à jour !' : (result.error || 'Erreur'), result.success ? 'success' : 'error');
    if (result.success) setTimeout(() => location.reload(), 1000);
  } catch (err) {
    showToast('Erreur de connexion', 'error');
  }
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
