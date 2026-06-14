<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();

$pageTitle = 'ISMO-SkillSwap — Recherche';
$currentPage = 'recherche';
$basePath = '..';

$query = $_GET['q'] ?? '';
$skillId = $_GET['skill_id'] ?? '';
$level = $_GET['level'] ?? '';
$filiere = $_GET['filiere'] ?? '';
$available = $_GET['available'] ?? '';

$hasFilters = $query || $skillId || $level || $filiere || $available;

// Get all skills for dropdown
$allSkills = $db->prepare("SELECT id, nom FROM competences_catalogue ORDER BY nom ASC");
$allSkills->execute();
$allSkills = $allSkills->fetchAll();

$skills = [];
if ($query) {
    $stmt = $db->prepare("SELECT * FROM competences_catalogue WHERE nom LIKE ? OR categorie LIKE ? ORDER BY nom LIMIT 20");
    $term = "%$query%";
    $stmt->execute([$term, $term]);
    $skills = $stmt->fetchAll();
}

$users = [];
if ($hasFilters) {
    $sql = "SELECT DISTINCT u.id, u.prenom, u.nom, u.role, u.points_gamification, u.bio, u.photo FROM utilisateurs u WHERE u.est_actif = 1";
    $params = [];

    if ($query) {
        $sql .= " AND (u.prenom LIKE ? OR u.nom LIKE ? OR u.bio LIKE ?)";
        $term = "%$query%";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }
    if ($skillId) {
        $sql .= " AND EXISTS (SELECT 1 FROM competences_stagiaire cs WHERE cs.utilisateur_id = u.id AND cs.competence_id = ?)";
        $params[] = (int)$skillId;
    }
    if ($level) {
        $sql .= " AND EXISTS (SELECT 1 FROM competences_stagiaire cs WHERE cs.utilisateur_id = u.id AND cs.niveau_estime = ?)";
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

    <form class="search-form" method="get" action="">
      <div class="search-input-wrap">
        <input type="search" name="q" class="form-input" placeholder="Rechercher une compétence ou une personne..." value="<?= h($query) ?>" />
        <button type="submit" class="btn-publish">Rechercher</button>
      </div>
      <div class="filter-row" style="margin-top:12px;display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;width:100%;">
        <div class="filter-group">
          <label for="skill_id">Compétence</label>
          <select name="skill_id" id="skill_id" class="form-input">
            <option value="">Toutes les compétences</option>
            <?php foreach ($allSkills as $s): ?>
            <option value="<?= $s['id'] ?>"<?= $skillId == $s['id'] ? ' selected' : '' ?>><?= h($s['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-group">
          <label for="level">Niveau</label>
          <select name="level" id="level" class="form-input">
            <option value="">Tous les niveaux</option>
            <option value="Débutant"<?= $level === 'Débutant' ? ' selected' : '' ?>>Débutant</option>
            <option value="Intermédiaire"<?= $level === 'Intermédiaire' ? ' selected' : '' ?>>Intermédiaire</option>
            <option value="Avancé"<?= $level === 'Avancé' ? ' selected' : '' ?>>Avancé</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="filiere">Filière</label>
          <input type="text" name="filiere" id="filiere" class="form-input" placeholder="Ex: Informatique" value="<?= h($filiere) ?>" />
        </div>
        <div class="filter-group" style="justify-content:flex-end;">
          <label class="filter-toggle" style="display:flex;align-items:center;gap:8px;margin-top:22px;font-size:0.85rem;cursor:pointer;">
            <input type="checkbox" name="available" value="1"<?= $available === '1' ? ' checked' : '' ?> />
            Disponible uniquement
          </label>
        </div>
      </div>
    </form>

    <?php if ($hasFilters): ?>
      <?php if ($query): ?>
      <h2>Compétences trouvées (<?= count($skills) ?>)</h2>
      <?php if (empty($skills)): ?><p class="text-muted">Aucune compétence trouvée.</p>
      <?php else: ?>
      <div class="results-list">
        <?php foreach ($skills as $s): ?>
        <div class="result-item">
          <h3><?= h($s['nom']) ?></h3>
          <p><?= h($s['categorie']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php endif; ?>

      <h2>Personnes trouvées (<?= count($users) ?>)</h2>
      <?php if (empty($users)): ?><p class="text-muted">Aucune personne trouvée.</p>
      <?php else: ?>
      <div class="results-list">
        <?php foreach ($users as $u): ?>
        <div class="result-item">
          <h3><a href="profile.php?id=<?= $u['id'] ?>"><?= h($u['prenom'] . ' ' . $u['nom']) ?></a></h3>
          <p><?= roleLabel($u['role']) ?> • <span class="icon-star" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span> <?= (int)$u['points_gamification'] ?></p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
