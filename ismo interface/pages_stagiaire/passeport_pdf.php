<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
$user = getCurrentUser();
$userId = (int)$user['id'];

$pageTitle = 'ISMO-SkillSwap — Passeport de Compétences';
$currentPage = 'passeport_pdf';
$basePath = '..';

$db = Database::getInstance();

$skills = $db->prepare("
    SELECT cs.*, cc.nom AS skill_name, cc.categorie AS skill_category,
           CONCAT(v.prenom, ' ', v.nom) AS validated_by_name
    FROM competences_stagiaire cs
    JOIN competences_catalogue cc ON cs.competence_id = cc.id
    LEFT JOIN utilisateurs v ON cs.validateur_id = v.id
    WHERE cs.utilisateur_id = ? AND cs.statut_validation = 'Validé'
    ORDER BY cs.date_declaration DESC
");
$skills->execute([$userId]);
$skills = $skills->fetchAll();

$badges = $db->prepare("
    SELECT b.*, bs.obtenu_le
    FROM badges_stagiaire bs
    JOIN badges b ON bs.badge_id = b.id
    WHERE bs.utilisateur_id = ?
    ORDER BY bs.obtenu_le DESC
");
$badges->execute([$userId]);
$badges = $badges->fetchAll();

$stmt = $db->prepare("SELECT COUNT(*) FROM demandes_aide WHERE mentor_id = ? AND statut = 'Résolu'");
$stmt->execute([$userId]);
$helps = (int)$stmt->fetchColumn();

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_stagiaire.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>

<main class="content-area passeport-page" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Mon Passeport de Compétences</h1>
        <p class="page-sub">Récapitulatif de vos compétences et réalisations</p>
      </div>
      <a href="../backend/generate_pdf.php?user_id=<?= $userId ?>" class="btn-publish" target="_blank">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Télécharger PDF
      </a>
    </div>

    <div class="passeport-container">
      <div class="passeport-header">
        <h2><?= h($user['prenom'] . ' ' . $user['nom']) ?></h2>
        <p><?= h($user['email']) ?> | Rôle: <?= roleLabel($user['role']) ?></p>
        <p>Points: <?= (int)$user['points_gamification'] ?> | Aides données: <?= $helps ?></p>
      </div>

      <h3>Compétences</h3>
      <?php if (empty($skills)): ?>
      <p class="empty-text">Aucune compétence déclarée.</p>
      <?php else: ?>
      <div class="passeport-table-wrap">
        <table class="passeport-table">
          <thead>
            <tr>
              <th>Compétence</th>
              <th>Catégorie</th>
              <th>Niveau</th>
              <th>Statut</th>
              <th>Validé par</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($skills as $sk): ?>
            <tr>
              <td class="cell-name"><?= h($sk['skill_name']) ?></td>
              <td><?= h($sk['skill_category']) ?></td>
              <td><?= h($sk['niveau_estime']) ?></td>
              <td>
                <?php if ($sk['statut_validation'] === 'Validé'): ?>
                  <span class="statut-badge valide">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Validé
                  </span>
                <?php elseif ($sk['statut_validation'] === 'Refusé'): ?>
                  <span class="statut-badge refuse">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    Refusé
                  </span>
                <?php else: ?>
                  <span class="statut-badge attente">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    En attente
                  </span>
                <?php endif; ?>
              </td>
              <td class="cell-validator"><?= h($sk['validated_by_name'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

      <h3>Badges obtenus</h3>
      <?php if (empty($badges)): ?>
      <p class="empty-text">Aucun badge obtenu.</p>
      <?php else: ?>
      <div class="badges-list">
        <?php foreach ($badges as $b): ?>
        <div class="badge-item">
          <span class="badge-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
          </span>
          <div>
            <strong><?= h($b['nom']) ?></strong>
            <small><?= date('d/m/Y', strtotime($b['obtenu_le'])) ?></small>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
