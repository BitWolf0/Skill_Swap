<?php
/**
 * ISMO-SkillSwap v4 — Login Page
 */
require_once __DIR__ . '/../backend/config.php';

// Platform stats (v4 — try-catch so missing tables don't crash)
try {
    $stmt = $db->query("SELECT COUNT(*) FROM utilisateurs");
    $totalUsers = (int) $stmt->fetchColumn();
} catch (Exception $e) { $totalUsers = 0; }

try {
    $stmt = $db->query("SELECT COUNT(*) FROM demandes_aide WHERE statut = 'Résolu'");
    $totalHelps = (int) $stmt->fetchColumn();
} catch (Exception $e) { $totalHelps = 0; }

try {
    $stmt = $db->query("SELECT COALESCE(ROUND(COUNT(CASE WHEN statut='Résolu' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0)), 0) FROM demandes_aide");
    $resolutionRate = (int) $stmt->fetchColumn();
} catch (Exception $e) { $resolutionRate = 0; }

try {
    $stmt = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE derniere_connexion >= NOW() - INTERVAL 30 MINUTE");
    $onlineCount = (int) $stmt->fetchColumn();
} catch (Exception $e) { $onlineCount = 0; }

// Redirect if already logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    if ($user) {
        $redirect = match ($user['role']) {
            'stagiaire'      => 'dashboard.php',
            'mentor'         => '../pages_mentor/dashboard.php',
            'formateur'      => '../formateur_pages/tableau_de_bord.php',
            'administrateur' => '../pages_admin/tableau_de_bord.php',
            default          => 'dashboard.php',
        };
        header("Location: $redirect");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap — Connexion</title>
  <meta name="description" content="Connectez-vous à ISMO-SkillSwap, la plateforme d'entraide académique par les pairs." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/tailwind.css" />
  <link rel="stylesheet" href="../assets/css/login.css" />
</head>
<body>
  <canvas id="particles-canvas"></canvas>
  <div class="page-wrapper">
    <!-- LEFT HERO -->
    <section class="hero" aria-label="Présentation de ISMO-SkillSwap">
      <div class="brand">
        <div class="brand-icon" aria-hidden="true">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="10" fill="rgba(255,255,255,0.2)" />
            <path d="M8 16C8 11.582 11.582 8 16 8s8 3.582 8 8-3.582 8-8 8" stroke="#fff" stroke-width="2.5" stroke-linecap="round" />
            <circle cx="16" cy="16" r="3" fill="#fff" />
          </svg>
        </div>
        <span class="brand-name">ISMO<span>-SkillSwap</span></span>
      </div>
      <div class="hero-headline">
        <h1>L'entraide<br /><span class="gradient-text">réinventée.</span></h1>
        <p class="hero-sub">La plateforme qui transforme chaque étudiant en expert et chaque question en opportunité d'apprentissage.</p>
      </div>
      <ul class="features" aria-label="Fonctionnalités principales">
        <li class="feature-card">
          <div class="feature-icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
          <div class="feature-text"><strong>Peer-to-Peer Help</strong><span>Posez vos questions, obtenez des réponses d'autres étudiants en temps réel.</span></div>
        </li>
        <li class="feature-card">
          <div class="feature-icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg></div>
          <div class="feature-text"><strong>Badges &amp; Skills</strong><span>Gagnez des badges reconnus et valorisez vos compétences acquises.</span></div>
        </li>
        <li class="feature-card">
          <div class="feature-icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="12" y1="6" x2="16" y2="6"/><line x1="12" y1="10" x2="16" y2="10"/></svg></div>
          <div class="feature-text"><strong>Digital Passport</strong><span>Un portfolio vivant qui trace votre parcours et impressionne les recruteurs.</span></div>
        </li>
      </ul>
      <div class="stats-card" aria-label="Statistiques de la plateforme">
        <div class="stats-card-glow" aria-hidden="true"></div>
        <p class="stats-label">Rejoignez la communauté</p>
        <div class="stats-grid">
          <div class="stat"><span class="stat-value" id="stat-users" data-target="<?= $totalUsers ?>">0</span><span class="stat-desc">Utilisateurs</span></div>
          <div class="stat-divider" aria-hidden="true"></div>
          <div class="stat"><span class="stat-value" id="stat-helps" data-target="<?= $totalHelps ?>">0</span><span class="stat-desc">Aides données</span></div>
          <div class="stat-divider" aria-hidden="true"></div>
          <div class="stat"><span class="stat-value" id="stat-rate" data-target="<?= $resolutionRate ?>">0<small>%</small></span><span class="stat-desc">Taux de résolution</span></div>
        </div>
        <div class="online-badge" aria-label="Utilisateurs en ligne"><span class="pulse-dot" aria-hidden="true"></span><span><?= $onlineCount ?> étudiant<?= $onlineCount !== 1 ? 's' : '' ?> actif<?= $onlineCount !== 1 ? 's' : '' ?> maintenant</span></div>
      </div>
    </section>

    <!-- RIGHT AUTH -->
    <section class="auth-section" aria-label="Authentification">
      <div class="auth-card">
        <div class="card-accent" aria-hidden="true"></div>
        <div class="tabs" role="tablist" aria-label="Mode d'authentification">
          <button class="tab active" id="tab-login" role="tab" aria-selected="true" aria-controls="panel-login" onclick="switchTab('login')">Connexion</button>
          <button class="tab" id="tab-signup" role="tab" aria-selected="false" aria-controls="panel-signup" onclick="switchTab('signup')">Inscription</button>
          <span class="tab-indicator" aria-hidden="true"></span>
        </div>

        <!-- LOGIN PANEL -->
        <div class="form-panel active" id="panel-login" role="tabpanel" aria-labelledby="tab-login">
          <div class="form-header">
            <h2>Bon retour</h2>
            <p>Connectez-vous pour continuer à apprendre et à aider.</p>
          </div>
          <?php if (isset($_GET['expired'])): ?>
            <div style="background:#FEF3C7;border:1px solid #F59E0B;color:#92400E;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;text-align:center;" role="alert">Session expirée. Veuillez vous reconnecter.</div>
          <?php endif; ?>
          <form id="login-form" method="post" action="../backend/auth/login.php" novalidate>
            <div class="input-group">
              <label for="login-email">Adresse e-mail</label>
              <div class="input-wrapper">
                <span class="input-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                <input type="email" id="login-email" name="email" placeholder="votre@email.com" autocomplete="email" required />
              </div>
              <span class="field-error" id="login-email-error" role="alert"></span>
            </div>
            <div class="input-group">
              <label for="login-password">Mot de passe</label>
              <div class="input-wrapper">
                <span class="input-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                <input type="password" id="login-password" name="password" placeholder="••••••••" autocomplete="current-password" required />
                <button type="button" class="toggle-pw" id="toggle-login-pw" aria-label="Afficher le mot de passe" onclick="togglePassword('login-password', this)"><svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
              </div>
              <span class="field-error" id="login-password-error" role="alert"></span>
            </div>
            <div class="form-row">
              <label class="checkbox-label" for="remember-me">
                <input type="checkbox" id="remember-me" name="remember" />
                <span class="checkbox-custom" aria-hidden="true"></span>
                Se souvenir de moi
              </label>
              <a href="#password-reset" class="forgot-link">Mot de passe oublié ?</a>
            </div>
            <button type="submit" class="btn-primary" id="btn-login">
              <span class="btn-text">Se connecter</span>
              <span class="btn-spinner" aria-hidden="true"></span>
            </button>
          </form>
        </div>

        <!-- SIGNUP PANEL -->
        <div class="form-panel" id="panel-signup" role="tabpanel" aria-labelledby="tab-signup" hidden>
          <div class="form-header">
            <h2>Créer un compte</h2>
            <p>Rejoignez des centaines d'étudiants qui s'entraident chaque jour.</p>
          </div>
          <form id="signup-form" method="post" action="../backend/auth/register.php" novalidate>
            <div class="name-row">
              <div class="input-group">
                <label for="signup-prenom">Prénom</label>
                <div class="input-wrapper">
                  <span class="input-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                  <input type="text" id="signup-prenom" name="prenom" placeholder="Prénom" autocomplete="given-name" required />
                </div>
              </div>
              <div class="input-group">
                <label for="signup-nom">Nom</label>
                <div class="input-wrapper">
                  <input type="text" id="signup-nom" name="nom" placeholder="Nom" autocomplete="family-name" required />
                </div>
              </div>
            </div>
            <div class="input-group">
              <label for="signup-email">Adresse e-mail</label>
              <div class="input-wrapper">
                <span class="input-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                <input type="email" id="signup-email" name="email" placeholder="votre@email.com" autocomplete="email" required />
              </div>
              <span class="field-error" id="signup-email-error" role="alert"></span>
            </div>
            <div class="input-group">
              <label for="signup-filiere">Filière</label>
              <div class="input-wrapper">
                <span class="input-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>
                <input type="text" id="signup-filiere" name="filiere" placeholder="Ex: DEV, RES, GL" />
              </div>
            </div>
            <div class="input-group">
              <label for="signup-password">Mot de passe</label>
              <div class="input-wrapper">
                <span class="input-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                <input type="password" id="signup-password" name="password" placeholder="Minimum 8 caractères" autocomplete="new-password" required />
                <button type="button" class="toggle-pw" id="toggle-signup-pw" aria-label="Afficher le mot de passe" onclick="togglePassword('signup-password', this)"><svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
              </div>
              <div class="strength-bar" aria-label="Force du mot de passe">
                <div class="strength-track"><div class="strength-fill" id="strength-fill"></div></div>
                <span class="strength-label" id="strength-label"></span>
              </div>
            </div>
            <div class="input-group">
              <label for="signup-role">Je suis…</label>
              <div class="input-wrapper select-wrapper">
                <span class="input-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>
                <select id="signup-role" name="role">
                  <option value="" disabled selected>Choisir un rôle</option>
                  <option value="stagiaire">Stagiaire</option>
                  <option value="formateur">Formateur</option>
                </select>
                <span class="select-chevron" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
              </div>
            </div>
            <label class="checkbox-label terms-label" for="terms">
              <input type="checkbox" id="terms" name="terms" required />
              <span class="checkbox-custom" aria-hidden="true"></span>
              J'accepte les <a href="termes.php">Conditions d'utilisation</a> et la <a href="confidentialite.php">Politique de confidentialité</a>
            </label>
            <button type="submit" class="btn-primary" id="btn-signup">
              <span class="btn-text">Créer mon compte</span>
              <span class="btn-spinner" aria-hidden="true"></span>
            </button>
          </form>
        </div>
      </div>
    </section>
  </div>
  <div class="toast-container" id="toast-container" aria-live="polite" aria-atomic="true"></div>
  <script src="../assets/js/login.js"></script>
</body>
</html>
