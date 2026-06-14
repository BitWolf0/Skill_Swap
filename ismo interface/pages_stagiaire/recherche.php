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

$hasFilters = $query || $skillId || $level || $filiere;

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
    $sql .= " ORDER BY u.points_gamification DESC LIMIT 20";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
}

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_stagiaire.php'; ?>
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
            <option value="Expert"<?= $level === 'Expert' ? ' selected' : '' ?>>Expert</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="filiere">Filière</label>
          <input type="text" name="filiere" id="filiere" class="form-input" placeholder="Ex: Informatique" value="<?= h($filiere) ?>" />
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
          <p><?= h($s['categorie'] ?? 'Générale') ?></p>
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
          <p><?= roleLabel($u['role']) ?> • <span class="icon-trophy" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg></span> <?= (int)$u['points_gamification'] ?> pts • <span class="icon-books" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M8 7h8"/><path d="M8 11h6"/></svg></span> <?= (int)$u['nb_competences'] ?> compétence(s)</p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
