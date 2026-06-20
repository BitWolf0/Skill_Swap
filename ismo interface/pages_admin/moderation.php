<?php
require_once __DIR__ . '/../backend/config.php';
requireAuth();
if (!in_array($_SESSION['user_role'], ['administrateur', 'formateur'])) {
    redirect('../pages_stagiaire/dashboard.php');
}

$db = Database::getInstance();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $type = $_POST['type'] ?? 'demande';

    if ($type === 'demande') {
        $stmt = $db->prepare('SELECT id FROM demandes_aide WHERE id = ?');
        $stmt->execute([$deleteId]);
        if ($stmt->fetch()) {
            $db->prepare('DELETE FROM propositions_aide WHERE demande_id = ?')->execute([$deleteId]);
            $db->prepare('DELETE FROM demandes_aide WHERE id = ?')->execute([$deleteId]);
            $msg = 'Demande supprimée définitivement';
        }
    }

    $_SESSION['flash'] = ['message' => $msg ?? 'Action effectuée', 'type' => 'success'];
    redirect('moderation.php');
}

$pageTitle = 'ISMO-SkillSwap — Modération';
$currentPage = 'moderation';
$basePath = '..';

// Flash message
$flashMsg = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ─── Mentor applications ─────────────────────────────────
$mentorApps = $db->prepare("
    SELECT ma.*, u.nom, u.prenom, u.email, u.filiere, u.points_gamification,
           (SELECT COUNT(*) FROM competences_stagiaire WHERE utilisateur_id = ma.utilisateur_id AND statut_validation = 'Validé') as competences_validees
    FROM mentor_applications ma
    JOIN utilisateurs u ON ma.utilisateur_id = u.id
    WHERE ma.statut = 'En attente'
    ORDER BY ma.date_soumission DESC
");
$mentorApps->execute();
$mentorApps = $mentorApps->fetchAll();

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mentor_action'])) {
    if (!verifyCsrf($_POST['_token'] ?? '')) {
        $_SESSION['flash'] = ['message' => 'Token CSRF invalide', 'type' => 'error'];
        redirect('moderation.php');
    }
    $appId = (int)$_POST['application_id'];
    $action = $_POST['mentor_action'];
    if (!in_array($action, ['Approuvé', 'Refusé'])) {
        $_SESSION['flash'] = ['message' => 'Action invalide', 'type' => 'error'];
        redirect('moderation.php');
    }
    $stmt = $db->prepare("SELECT utilisateur_id FROM mentor_applications WHERE id = ?");
    $stmt->execute([$appId]);
    $app = $stmt->fetch();
    if ($app) {
        $db->prepare("UPDATE mentor_applications SET statut = ?, date_traitement = NOW(), traite_par_id = ? WHERE id = ?")
           ->execute([$action, $_SESSION['user_id'], $appId]);
        if ($action === 'Approuvé') {
            $db->prepare("UPDATE utilisateurs SET role = 'mentor' WHERE id = ?")
               ->execute([$app['utilisateur_id']]);
            // Assigner automatiquement le badge Mentor
            $badgeCheck = $db->prepare("SELECT id FROM badges_stagiaire WHERE utilisateur_id = ? AND badge_id = 4");
            $badgeCheck->execute([$app['utilisateur_id']]);
            if (!$badgeCheck->fetch()) {
                $db->prepare("INSERT INTO badges_stagiaire (utilisateur_id, badge_id, attribue_par, motif) VALUES (?, 4, 'système', 'Mentor approuvé')")
                   ->execute([$app['utilisateur_id']]);
            }
        }
        $_SESSION['flash'] = ['message' => 'Candidature ' . ($action === 'Approuvé' ? 'approuvée' : 'refusée'), 'type' => 'success'];
    }
    redirect('moderation.php');
}

// List all help requests
$demandes = $db->query("
    SELECT d.id, d.titre, d.statut, d.creee_le, d.description,
           u.prenom, u.nom, u.role AS auteur_role,
           c.nom AS competence_nom,
           (SELECT COUNT(*) FROM propositions_aide WHERE demande_id = d.id) AS nb_propositions
    FROM demandes_aide d
    JOIN utilisateurs u ON d.auteur_id = u.id
    LEFT JOIN competences_catalogue c ON d.competence_id = c.id
    ORDER BY d.creee_le DESC
    LIMIT 100
")->fetchAll();

$total = count($demandes);

include __DIR__ . '/../backend/includes/header.php';
?>
<?php include __DIR__ . '/../backend/includes/sidebar_admin.php'; ?>
<?php include __DIR__ . '/../backend/includes/topbar.php'; ?>
<main class="content-area" id="main-content">
  <section class="content-main">
    <div class="page-head">
      <div class="page-title-wrap">
        <h1 class="page-title">Modération</h1>
        <p class="page-sub"><?= $total ?> publication(s) sur la plateforme</p>
      </div>
    </div>

    <?php if ($flashMsg): ?>
    <div class="mod-flash mod-flash-<?= $flashMsg['type'] ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <?php if ($flashMsg['type'] === 'success'): ?>
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        <?php else: ?>
        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        <?php endif; ?>
      </svg>
      <span><?= h($flashMsg['message']) ?></span>
    </div>
    <?php endif; ?>

    <?php if ($total === 0): ?>
    <div class="mod-empty">
      <p>Aucune publication à modérer.</p>
    </div>
    <?php else: ?>
    <div class="mod-table-wrap">
      <div class="mod-table-scroll">
      <table class="mod-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Auteur</th>
            <th>Titre</th>
            <th>Compétence</th>
            <th>Statut</th>
            <th class="center">Propositions</th>
            <th>Date</th>
            <th class="center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($demandes as $d): ?>
          <tr>
            <td class="id-cell">#<?= $d['id'] ?></td>
            <td class="author-cell"><?= h($d['prenom'] . ' ' . $d['nom']) ?></td>
            <td class="title-cell" title="<?= h($d['titre']) ?>"><?= h(mb_substr($d['titre'], 0, 60)) ?></td>
            <td><?= h($d['competence_nom'] ?? '-') ?></td>
            <td>
              <span class="tag <?= $d['statut'] === 'Résolu' ? 'tag-green' : ($d['statut'] === 'En cours' ? 'tag-orange' : 'tag-blue') ?>">
                <?= h($d['statut']) ?>
              </span>
            </td>
            <td class="proposals-cell"><?= $d['nb_propositions'] ?></td>
            <td class="date-cell"><?= date('d/m/Y H:i', strtotime($d['creee_le'])) ?></td>
            <td class="action-cell">
              <form method="post" class="mod-form-inline" onsubmit="return confirm('Supprimer définitivement cette demande ?');">
                <input type="hidden" name="delete_id" value="<?= $d['id'] ?>" />
                <input type="hidden" name="type" value="demande" />
                <button type="submit" class="mod-btn mod-btn-delete">Supprimer</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
    <?php endif; ?>

    <?php if (in_array($_SESSION['user_role'], ['administrateur', 'formateur'])): ?>
    <div class="mod-mentor-section">
      <div class="page-head">
        <div class="page-title-wrap">
          <h2 class="page-title">Candidatures Mentor</h2>
          <p class="page-sub"><?= count($mentorApps) ?> candidature(s) en attente</p>
        </div>
      </div>

      <?php if (empty($mentorApps)): ?>
      <div class="mod-empty">
        <p>Aucune candidature en attente.</p>
      </div>
      <?php else: ?>
      <div class="mod-table-wrap">
        <div class="mod-table-scroll">
        <table class="mod-table">
          <thead>
            <tr>
              <th>Candidat</th>
              <th>Email</th>
              <th>Comp&eacute;tences</th>
              <th>Motivation</th>
              <th>Date</th>
              <th class="center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($mentorApps as $app): ?>
            <tr>
              <td class="author-cell"><?= h($app['prenom'] . ' ' . $app['nom']) ?></td>
              <td class="date-cell"><?= h($app['email']) ?></td>
              <td class="proposals-cell"><span class="tag tag-blue"><?= (int)$app['competences_validees'] ?></span></td>
              <td class="motivation-cell">
                <?= h(mb_substr($app['motivation'] ?? '', 0, 100)) ?>
                <?php if ($app['experience']): ?>
                <br><em>Exp: <?= h(mb_substr($app['experience'], 0, 80)) ?></em>
                <?php endif; ?>
              </td>
              <td class="date-cell"><?= date('d/m/Y', strtotime($app['date_soumission'])) ?></td>
              <td class="action-cell">
                <form method="post" class="mod-form-inline" onsubmit="return confirm('Approuver cette candidature ? Le stagiaire deviendra mentor.');">
                  <?= csrfField() ?>
                  <input type="hidden" name="application_id" value="<?= $app['id'] ?? $app['application_id'] ?>" />
                  <input type="hidden" name="mentor_action" value="Approuvé" />
                  <button type="submit" class="mod-btn mod-btn-approve">Approuver</button>
                </form>
                <form method="post" class="mod-form-inline mod-form-offset" onsubmit="return confirm('Refuser cette candidature ?');">
                  <?= csrfField() ?>
                  <input type="hidden" name="application_id" value="<?= $app['id'] ?? $app['application_id'] ?>" />
                  <input type="hidden" name="mentor_action" value="Refusé" />
                  <button type="submit" class="mod-btn mod-btn-reject">Refuser</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
