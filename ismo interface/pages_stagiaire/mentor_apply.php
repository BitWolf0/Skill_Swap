<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$user['id'];

$pageTitle = 'ISMO-SkillSwap — Devenir Mentor';
$currentPage = 'mentor_apply';
$basePath = '..';

$db = Database::getInstance();
$isMentor = estMentor($userId);

$stmt = $db->prepare("SELECT * FROM mentor_applications WHERE utilisateur_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$userId]);
$application = $stmt->fetch();

$skills = $db->prepare("
    SELECT cc.nom, cs.niveau_estime, cs.statut_validation
    FROM competences_stagiaire cs
    JOIN competences_catalogue cc ON cs.competence_id = cc.id
    WHERE cs.utilisateur_id = ? AND cs.statut_validation = 'Validé'
    ORDER BY cc.nom
");
$skills->execute([$userId]);
$skills = $skills->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_stagiaire.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main mentor-apply-page">

    <!-- Hero Header -->
    <div class="mentor-hero">
      <div class="mentor-hero-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="mentor-hero-text">
        <h2>Devenir Mentor</h2>
        <p>Partagez vos compétences et aidez les autres stagiaires à progresser</p>
      </div>
    </div>

    <!-- Status Alerts -->
    <?php if ($isMentor): ?>
    <div class="mentor-alert success">
      <div class="mentor-alert-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>
      <div class="mentor-alert-content">
        <strong>Félicitations !</strong>
        <p>Vous êtes mentor. Vous pouvez répondre aux demandes d'aide dans le <a href="marketplace.php" style="color:inherit;font-weight:600;">marketplace</a>.</p>
      </div>
    </div>
    <?php elseif ($application && $application['statut'] === 'En attente'): ?>
    <div class="mentor-alert info">
      <div class="mentor-alert-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
      </div>
      <div class="mentor-alert-content">
        <strong>Candidature en cours d'examen</strong>
        <p>Votre candidature est en cours d'évaluation par un formateur ou administrateur. Vous serez notifié dès qu'une décision sera prise.</p>
      </div>
    </div>
    <?php elseif ($application && $application['statut'] === 'Refusé'): ?>
    <div class="mentor-alert error">
      <div class="mentor-alert-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
      </div>
      <div class="mentor-alert-content">
        <strong>Candidature refusée</strong>
        <p>Vous pouvez soumettre une nouvelle candidature ci-dessous en renforçant votre lettre de motivation.</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- Main Content Grid -->
    <?php if (!$isMentor && (!$application || $application['statut'] === 'Refusé')): ?>
    <div class="mentor-apply-grid">

      <!-- Form Column -->
      <div class="mentor-card">
        <h3 class="mentor-card-title">Formulaire de candidature</h3>
        <p class="mentor-card-sub">Remplissez les champs ci-dessous pour postuler en tant que mentor.</p>
        <hr class="mentor-card-divider">

        <form class="mentor-apply-form" id="mentor-apply-form">
          <div class="form-group">
            <label>
              <svg class="label-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
              Mes compétences validées
            </label>
            <?php if (empty($skills)): ?>
            <p class="text-muted">Aucune compétence validée pour le moment.</p>
            <?php else: ?>
            <div class="skills-tags">
              <?php foreach ($skills as $s): ?>
              <span class="tag tag-blue"><?= h($s['nom']) ?> (<?= h($s['niveau_estime']) ?>)</span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p class="form-hint">Seules les compétences validées par un formateur sont affichées.</p>
          </div>

          <div class="form-group">
            <label for="motivation">
              <svg class="label-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
              </svg>
              Lettre de motivation
            </label>
            <textarea id="motivation" name="motivation" class="form-input" rows="5" placeholder="Pourquoi voulez-vous devenir mentor ? Qu'avez-vous à apporter à la communauté ?" required></textarea>
            <p id="motivation-count" class="form-char-count">0 / 1000</p>
          </div>

          <div class="form-group">
            <label for="experience">
              <svg class="label-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
              </svg>
              Résumé de votre expérience
            </label>
            <textarea id="experience" name="experience" class="form-input" rows="4" placeholder="Décrivez votre parcours, vos expériences professionnelles ou académiques pertinentes... (optionnel)"></textarea>
          </div>

          <button type="submit" class="btn-publish">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
            Envoyer ma candidature
          </button>
        </form>
      </div>

      <!-- Benefits Sidebar -->
      <aside class="mentor-benefits">
        <h3>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l2.9 6.1 6.7.6-5 4.4 1.5 6.5-6.1-3.6-6.1 3.6 1.5-6.5-5-4.4 6.7-.6L12 2z"/>
          </svg>
          Avantages Mentor
        </h3>
        <ul class="mentor-benefits-list">
          <li>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Gagnez des points de réputation et débloquez des badges exclusifs
          </li>
          <li>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Renforcez vos compétences en enseignant aux autres
          </li>
          <li>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Valorisez votre profil auprès des formateurs et entreprises
          </li>
          <li>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Faites partie d'une communauté d'entraide active
          </li>
        </ul>
        <div class="mentor-graphic">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
          </svg>
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
      </aside>

    </div>
    <?php endif; ?>
  </section>
</main>

<script>
document.querySelector('#motivation')?.addEventListener('input', function() {
  const count = this.value.length;
  document.getElementById('motivation-count').textContent = count + ' / 1000';
  if (count > 1000) this.value = this.value.slice(0, 1000);
});

document.getElementById('mentor-apply-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = this.querySelector('.btn-publish');
  btn.disabled = true;
  btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span> Envoi en cours...';
  const data = { motivation: this.querySelector('#motivation').value, experience: this.querySelector('#experience').value };
  if (!data.motivation) { showToast('Veuillez écrire une lettre de motivation', 'error'); btn.disabled = false; btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Envoyer ma candidature'; return; }
  try {
    const resp = await fetch('../backend/api/mentor.php?action=postuler', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrfToken() ?>' },
      body: JSON.stringify(data)
    });
    const result = await resp.json();
    if (result.success) {
      showToast('Candidature envoyée avec succès !', 'success');
      setTimeout(() => location.reload(), 1200);
    } else {
      showToast(result.error || 'Erreur lors de l\'envoi', 'error');
      btn.disabled = false;
      btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Envoyer ma candidature';
    }
  } catch (err) {
    showToast('Erreur de connexion', 'error');
    btn.disabled = false;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Envoyer ma candidature';
  }
});
</script>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
