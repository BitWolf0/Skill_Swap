<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
requireRole([ROLE_FORMATEUR, ROLE_ADMIN]);

$pageTitle = 'ISMO-SkillSwap — Catalogue';
$currentPage = 'catalogue';
$basePath = '..';

$db = Database::getInstance();

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT cc.*, (SELECT COUNT(*) FROM competences_stagiaire WHERE competence_id = cc.id) as declared_count FROM competences_catalogue cc WHERE cc.est_active = 1";
$params = [];

if ($category) {
    $sql .= ' AND cc.categorie = ?';
    $params[] = $category;
}
if ($search) {
    $sql .= ' AND (cc.nom LIKE ? OR cc.description LIKE ?)';
    $t = "%$search%";
    $params[] = $t;
    $params[] = $t;
}
$sql .= ' ORDER BY cc.categorie, cc.nom';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$skills = $stmt->fetchAll();

$categories = $db->query("SELECT DISTINCT categorie FROM competences_catalogue WHERE est_active = 1 AND categorie IS NOT NULL AND categorie != '' ORDER BY categorie")->fetchAll();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_formateur.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Catalogue des Compétences</h1>
      </div>
    </div>

    <form method="get" class="search-form" style="margin-bottom:1rem">
      <input type="search" name="search" class="form-input" placeholder="Rechercher..." value="<?= h($search) ?>" style="max-width:300px;display:inline-block" />
      <select name="category" class="form-input" style="max-width:200px;display:inline-block">
        <option value="">Toutes catégories</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= h($c['categorie']) ?>" <?= $category === $c['categorie'] ? 'selected' : '' ?>><?= h($c['categorie']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-sm btn-primary">Filtrer</button>
    </form>

    <table class="data-table">
      <thead><tr><th>Compétence</th><th>Catégorie</th><th>Déclarations</th><th>Statut</th></tr></thead>
      <tbody>
        <?php foreach ($skills as $s): ?>
        <tr>
          <td><?= h($s['nom']) ?></td>
          <td><?= h($s['categorie'] ?? 'Non catégorisé') ?></td>
          <td><?= (int)$s['declared_count'] ?></td>
          <td><span class="badge badge-active">Actif</span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>
<style>
.data-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.data-table th { background: var(--gray-50); padding: 12px 16px; text-align: left; font-weight: 600; font-size: 0.85rem; color: var(--gray-600); border-bottom: 1px solid var(--gray-200); }
.data-table td { padding: 12px 16px; border-bottom: 1px solid var(--gray-100); font-size: 0.9rem; }
.data-table tbody tr:hover { background: var(--gray-50); }
.search-form { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
</style>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
