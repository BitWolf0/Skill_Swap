<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();

$pageTitle = 'ISMO-SkillSwap — Recherche';
$currentPage = 'recherche';
$basePath = '..';

$db = Database::getInstance();

$query = $_GET['q'] ?? '';
$skillId = $_GET['skill_id'] ?? '';
$level = $_GET['level'] ?? '';
$filiere = $_GET['filiere'] ?? '';
$available = $_GET['available'] ?? '';

$hasFilters = $query || $skillId || $level || $filiere || $available;

$allSkills = $db->prepare("SELECT id, nom FROM competences_catalogue WHERE est_active = 1 ORDER BY nom");
$allSkills->execute();
$allSkills = $allSkills->fetchAll();

$skills = [];
if ($query) {
    $stmt = $db->prepare("SELECT * FROM competences_catalogue WHERE est_active = 1 AND (nom LIKE ? OR categorie LIKE ? OR description LIKE ?) ORDER BY nom LIMIT 20");
    $term = "%$query%";
    $stmt->execute([$term, $term, $term]);
    $skills = $stmt->fetchAll();
}

$users = [];
if ($hasFilters) {
    $sql = "SELECT DISTINCT u.*, (SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = u.id AND statut_validation = 'Validé') as nb_competences FROM utilisateurs u WHERE 1=1";
    $params = [];

    if ($query) {
        $sql .= " AND (u.prenom LIKE ? OR u.nom LIKE ?)";
        $term = "%$query%";
        $params[] = $term;
        $params[] = $term;
    }
    if ($skillId) {
        $sql .= " AND EXISTS (SELECT 1 FROM competences_stagiaire cs WHERE cs.utilisateur_id = u.id AND cs.competence_id = ? AND cs.statut_validation = 'Validé')";
        $params[] = (int)$skillId;
    }
    if ($level) {
        $sql .= " AND EXISTS (SELECT 1 FROM competences_stagiaire cs WHERE cs.utilisateur_id = u.id AND cs.niveau_estime = ? AND cs.statut_validation = 'Validé')";
        $params[] = $level;
    }
    if ($filiere) {
        $sql .= " AND u.filiere LIKE ?";
        $params[] = "%$filiere%";
    }
    if ($available === '1') {
        $sql .= " AND u.disponible = 1";
    }
    $sql .= " ORDER BY u.points_gamification DESC LIMIT 20";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
}

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_mentor.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Recherche</h1>
        <p class="page-sub">Trouvez des compétences et des personnes</p>
      </div>
    </div>

    <form class="search-panel" method="get" action="">
      <div class="search-bar">
        <input type="search" name="q" placeholder="Rechercher une compétence ou une personne..." value="<?= h($query) ?>" />
        <button type="submit" class="search-btn">Rechercher</button>
        <span class="search-icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
      </div>
      <div class="filter-row">
        <div class="filter-group">
          <label for="skill_id">Compétence</label>
          <select name="skill_id" id="skill_id">
            <option value="">Toutes les compétences</option>
            <?php foreach ($allSkills as $s): ?>
            <option value="<?= $s['id'] ?>"<?= $skillId == $s['id'] ? ' selected' : '' ?>><?= h($s['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-group">
          <label for="level">Niveau</label>
          <select name="level" id="level">
            <option value="">Tous les niveaux</option>
            <option value="Débutant"<?= $level === 'Débutant' ? ' selected' : '' ?>>Débutant</option>
            <option value="Intermédiaire"<?= $level === 'Intermédiaire' ? ' selected' : '' ?>>Intermédiaire</option>
            <option value="Avancé"<?= $level === 'Avancé' ? ' selected' : '' ?>>Avancé</option>
            <option value="Expert"<?= $level === 'Expert' ? ' selected' : '' ?>>Expert</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="filiere">Filière</label>
          <input type="text" name="filiere" id="filiere" placeholder="Ex: Informatique" value="<?= h($filiere) ?>" />
        </div>
        <div class="filter-group">
          <label class="filter-toggle">
            <input type="checkbox" name="available" value="1"<?= $available === '1' ? ' checked' : '' ?> />
            Disponible uniquement
          </label>
        </div>
      </div>
    </form>

    <?php if ($hasFilters): ?>
      <div class="results-info">
        <p><?= count($users) ?> personne(s) trouvée(s)<?= $query ? ' pour «' . h($query) . '»' : '' ?></p>
      </div>

      <?php if ($query && !empty($skills)): ?>
      <h2 class="section-title">Compétences</h2>
      <div class="results-list">
        <?php foreach ($skills as $s): ?>
        <a class="result-item" href="?skill_id=<?= $s['id'] ?>&level=<?= h($level) ?>&filiere=<?= h($filiere) ?>&available=<?= h($available) ?>">
          <div class="result-icon-skill">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
          </div>
          <div class="result-body">
            <span class="result-name"><?= h($s['nom']) ?></span>
            <span class="result-meta"><?= h($s['categorie'] ?? 'Générale') ?></span>
          </div>
          <span class="result-badge skill-result">Compétence</span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <h2 class="section-title">Personnes</h2>
      <?php if (empty($users)): ?><p class="text-muted">Aucune personne trouvée.</p>
      <?php else: ?>
      <div class="results-list">
        <?php foreach ($users as $u): ?>
        <a class="result-item" href="profile.php?id=<?= $u['id'] ?>">
          <div class="result-avatar"><?= mb_strtoupper(mb_substr($u['prenom'], 0, 1)) . mb_strtoupper(mb_substr($u['nom'], 0, 1)) ?></div>
          <div class="result-body">
            <span class="result-name"><?= h($u['prenom'] . ' ' . $u['nom']) ?></span>
            <span class="result-meta">
              <span class="result-stat">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                <?= (int)$u['points_gamification'] ?> pts
              </span>
              <span class="result-stat">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M8 7h8"/><path d="M8 11h6"/></svg>
                <?= (int)$u['nb_competences'] ?> comp.
              </span>
            </span>
          </div>
          <span class="result-badge <?= strtolower(roleLabel($u['role'])) === 'mentor' ? 'mentor-result' : 'trainee-result' ?>"><?= roleLabel($u['role']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
