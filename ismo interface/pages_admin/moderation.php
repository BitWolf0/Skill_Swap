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

    <?php if ($total === 0): ?>
    <div class="card" style="padding:2rem;text-align:center;">
      <p style="font-size:1.1rem;color:var(--text-muted);">Aucune publication à modérer.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive" style="overflow-x:auto;">
      <table class="table" style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="border-bottom:2px solid var(--border-color);">
            <th style="padding:10px 12px;text-align:left;font-weight:600;">ID</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;">Auteur</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;">Titre</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;">Compétence</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;">Statut</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;">Propositions</th>
            <th style="padding:10px 12px;text-align:left;font-weight:600;">Date</th>
            <th style="padding:10px 12px;text-align:center;font-weight:600;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($demandes as $d): ?>
          <tr style="border-bottom:1px solid var(--border-color);">
            <td style="padding:10px 12px;">#<?= $d['id'] ?></td>
            <td style="padding:10px 12px;"><?= h($d['prenom'] . ' ' . $d['nom']) ?></td>
            <td style="padding:10px 12px;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= h($d['titre']) ?>">
              <?= h(mb_substr($d['titre'], 0, 60)) ?>
            </td>
            <td style="padding:10px 12px;"><?= h($d['competence_nom'] ?? '-') ?></td>
            <td style="padding:10px 12px;">
              <span class="tag <?= $d['statut'] === 'Résolu' ? 'tag-green' : ($d['statut'] === 'En cours' ? 'tag-orange' : 'tag-blue') ?>">
                <?= h($d['statut']) ?>
              </span>
            </td>
            <td style="padding:10px 12px;text-align:center;"><?= $d['nb_propositions'] ?></td>
            <td style="padding:10px 12px;white-space:nowrap;font-size:0.85rem;"><?= date('d/m/Y H:i', strtotime($d['creee_le'])) ?></td>
            <td style="padding:10px 12px;text-align:center;">
              <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer définitivement cette demande ?');">
                <input type="hidden" name="delete_id" value="<?= $d['id'] ?>" />
                <input type="hidden" name="type" value="demande" />
                <button type="submit" class="btn-sm btn-danger" style="background:#e74c3c;color:#fff;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:0.85rem;">
                  Supprimer
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <?php if (in_array($_SESSION['user_role'], ['administrateur', 'formateur'])): ?>
    <div style="margin-top:40px;">
      <div class="page-head" style="margin-bottom:8px;">
        <div class="page-title-wrap">
          <h2 class="page-title" style="font-size:1.25rem;">Candidatures Mentor</h2>
          <p class="page-sub"><?= count($mentorApps) ?> candidature(s) en attente</p>
        </div>
      </div>

      <?php if (empty($mentorApps)): ?>
      <div class="card" style="padding:2rem;text-align:center;">
        <p style="font-size:1.1rem;color:var(--text-muted);">Aucune candidature en attente.</p>
      </div>
      <?php else: ?>
      <div class="table-responsive" style="overflow-x:auto;">
        <table class="table" style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:2px solid var(--border-color);">
              <th style="padding:10px 12px;text-align:left;font-weight:600;">Candidat</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;">Email</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;">Comp&eacute;tences</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;">Motivation</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;">Date</th>
              <th style="padding:10px 12px;text-align:center;font-weight:600;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($mentorApps as $app): ?>
            <tr style="border-bottom:1px solid var(--border-color);">
              <td style="padding:10px 12px;font-weight:600;"><?= h($app['prenom'] . ' ' . $app['nom']) ?></td>
              <td style="padding:10px 12px;font-size:0.85rem;"><?= h($app['email']) ?></td>
              <td style="padding:10px 12px;text-align:center;">
                <span class="tag tag-blue"><?= (int)$app['competences_validees'] ?></span>
              </td>
              <td style="padding:10px 12px;max-width:250px;font-size:0.85rem;color:var(--gray-600);">
                <?= h(mb_substr($app['motivation'] ?? '', 0, 100)) ?>
                <?php if ($app['experience']): ?>
                <br><em style="font-size:0.8rem;">Exp: <?= h(mb_substr($app['experience'], 0, 80)) ?></em>
                <?php endif; ?>
              </td>
              <td style="padding:10px 12px;white-space:nowrap;font-size:0.85rem;"><?= date('d/m/Y', strtotime($app['date_soumission'])) ?></td>
              <td style="padding:10px 12px;text-align:center;white-space:nowrap;">
                <form method="post" style="display:inline;" onsubmit="return confirm('Approuver cette candidature ? Le stagiaire deviendra mentor.');">
                  <?= csrfField() ?>
                  <input type="hidden" name="application_id" value="<?= $app['id'] ?? $app['application_id'] ?>" />
                  <input type="hidden" name="mentor_action" value="Approuvé" />
                  <button type="submit" class="btn-sm" style="background:#22c55e;color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600;">
                    Approuver
                  </button>
                </form>
                <form method="post" style="display:inline;margin-left:6px;" onsubmit="return confirm('Refuser cette candidature ?');">
                  <?= csrfField() ?>
                  <input type="hidden" name="application_id" value="<?= $app['id'] ?? $app['application_id'] ?>" />
                  <input type="hidden" name="mentor_action" value="Refusé" />
                  <button type="submit" class="btn-sm" style="background:#e74c3c;color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600;">
                    Refuser
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../backend/includes/footer.php'; ?>
