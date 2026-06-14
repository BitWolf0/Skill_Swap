<?php
require_once __DIR__ . '/config.php';
requireAuth();
requireCsrf();
$user = getCurrentUser();
$currentUserId = (int)$user['id'];
$currentRole = $user['role'];
$targetUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $currentUserId;
if ($targetUserId !== $currentUserId && !in_array($currentRole, ['administrateur', 'formateur'], true)) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Accès refusé.';
    exit;
}
require_once __DIR__ . '/lib/SimplePdf.php';
$targetUser = getUserById($targetUserId);
if (!$targetUser) {
    header('HTTP/1.1 404 Not Found');
    echo 'Utilisateur introuvable.';
    exit;
}
$db = Database::getInstance();
$fullName = h($targetUser['prenom'] . ' ' . $targetUser['nom']);
$skillsStmt = $db->prepare("
    SELECT cs.*, cc.nom AS skill_name, cc.categorie AS skill_category
    FROM competences_stagiaire cs
    JOIN competences_catalogue cc ON cs.competence_id = cc.id
    WHERE cs.utilisateur_id = ? AND cs.statut_validation = 'Validé'
    ORDER BY cc.categorie ASC, cc.nom ASC
");
$skillsStmt->execute([$targetUserId]);
$skills = $skillsStmt->fetchAll();
$badgesStmt = $db->prepare("
    SELECT b.*, bs.obtenu_le
    FROM badges_stagiaire bs
    JOIN badges b ON bs.badge_id = b.id
    WHERE bs.utilisateur_id = ?
    ORDER BY bs.obtenu_le DESC
");
$badgesStmt->execute([$targetUserId]);
$badges = $badgesStmt->fetchAll();
$helpsStmt = $db->prepare("
    SELECT COUNT(*) FROM demandes_aide
    WHERE mentor_id = ? AND statut = 'Résolu'
");
$helpsStmt->execute([$targetUserId]);
$helpsCount = (int)$helpsStmt->fetchColumn();
$levelInfo = getUserLevel((int)$targetUser['points_gamification']);

// ─── LIGHT MODE PALETTE ─────────────────────────────────────────────────────
$blue      = [37,  99,  235];   // #2563EB primary
$dark      = [15,  23,  42];    // text
$midGray   = [100, 116, 139];   // muted text
$lightGray = [248, 249, 252];   // card / page bg
$borderGray= [226, 232, 240];   // borders
$white     = [255, 255, 255];
$rowAlt    = [248, 250, 252];   // alternating row
$amber     = [217, 119, 6];     // stat numbers
$red       = [220, 38, 38];
$blueDark  = [29, 78, 216];

$pdf = new SimplePdf();
$pdf->AddPage();
$pageW = $pdf->GetPageWidth() - $pdf->GetLeftMargin() - 20;
$lm    = $pdf->GetLeftMargin();

// ════════════════════════════════════════════════════════════════════════════
// HERO — blue bar
// ════════════════════════════════════════════════════════════════════════════
$heroH = 36;
$pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
$pdf->Rect($lm, $pdf->GetY(), $pageW, $heroH, 'F');

// ── Logo (replicating topbar SVG: blue bg, white arc + dot) ──
$logoX = $lm + 5;
$logoY = $pdf->GetY() + 6;
$logoSize = 16;

$pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
$pdf->Rect($logoX, $logoY, $logoSize, $logoSize, 'F');

$cx = $logoX + $logoSize / 2;
$cy = $logoY + $logoSize / 2;

// SVG arc: left → top → right → bottom (counter-clockwise, right-side C)
// In SVG viewBox 32×32, arc R=8px → 4mm in 16mm box
$arcR = 4;
$pdf->SetDrawColor($white[0], $white[1], $white[2]);
$pdf->SetLineWidth(1.0);
$steps = 30;
$startDeg = 180;
$endDeg   = 450;
$prev = null;
for ($i = 0; $i <= $steps; $i++) {
    $angle = deg2rad($startDeg + ($endDeg - $startDeg) * $i / $steps);
    $px = $cx + $arcR * cos($angle);
    $py = $cy + $arcR * sin($angle);
    if ($prev !== null) {
        $pdf->Line($prev[0], $prev[1], $px, $py);
    }
    $prev = [$px, $py];
}

// Centre dot (SVG: r=3px on 32px viewBox → 1.5mm on 16mm box)
$dotR = 1.5;
$pdf->SetFillColor($white[0], $white[1], $white[2]);
$pdf->Rect($cx - $dotR, $cy - $dotR, $dotR * 2, $dotR * 2, 'F');

// ── Brand text ──
$pdf->SetXY($logoX + $logoSize + 4, $logoY + 1);
$pdf->SetTextColor($white[0], $white[1], $white[2]);
$pdf->SetFont('', 13);
$pdf->Cell(60, 7, 'ISMO-SkillSwap', 0, 0, 'L');

$pdf->SetXY($logoX + $logoSize + 4, $logoY + 8);
$pdf->SetFont('', 7);
$pdf->Cell($pageW - $logoSize - 9, 5, "Plateforme d'entraide et de valorisation des competences", 0, 1, 'L');

// ── Title right-aligned ──
$pdf->SetXY($lm, $logoY + 1);
$pdf->SetFont('B', 16);
$pdf->Cell($pageW, 7, 'PASSEPORT DE COMPETENCES', 0, 0, 'R');

$pdf->SetXY($lm, $logoY + $heroH - 4);
$pdf->Ln(6);

// ════════════════════════════════════════════════════════════════════════════
// IDENTITY TABLE
// ════════════════════════════════════════════════════════════════════════════
$col1W = $pageW * 0.22;
$col2W = $pageW - $col1W;

$pdf->SetDrawColor($borderGray[0], $borderGray[1], $borderGray[2]);
$pdf->SetLineWidth(0.3);

$pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
$pdf->SetTextColor($white[0], $white[1], $white[2]);
$pdf->SetFont('B', 8);
$pdf->Cell($col1W, 7, '  Champ', 1, 0, 'L', true);
$pdf->Cell($col2W, 7, '  Valeur', 1, 1, 'L', true);

$rows = [
    ['NOM',    $fullName],
    ['EMAIL',  h($targetUser['email'])],
    ['ROLE',   roleLabel($targetUser['role'])],
    ['NIVEAU', 'Niveau ' . $levelInfo['level'] . ' — ' . $levelInfo['name']],
];
foreach ($rows as $i => [$label, $val]) {
    $fillRow = $i % 2 === 0;
    $pdf->SetFillColor($fillRow ? $rowAlt[0] : $white[0], $fillRow ? $rowAlt[1] : $white[1], $fillRow ? $rowAlt[2] : $white[2]);
    $pdf->SetTextColor($midGray[0], $midGray[1], $midGray[2]);
    $pdf->SetFont('B', 8);
    $pdf->Cell($col1W, 7, '  ' . $label, 1, 0, 'L', true);
    $pdf->SetFont('', 9);
    $pdf->SetTextColor(
        $i === 3 ? $blue[0] : $dark[0],
        $i === 3 ? $blue[1] : $dark[1],
        $i === 3 ? $blue[2] : $dark[2]
    );
    $pdf->Cell($col2W, 7, '  ' . $val, 1, 1, 'L', true);
}
$pdf->Ln(4);
$pdf->SetLineWidth(0.2);

// ════════════════════════════════════════════════════════════════════════════
// STATS ROW
// ════════════════════════════════════════════════════════════════════════════
$statsData = [
    ['Points',   (int)$targetUser['points_gamification']],
    ['Competences', count($skills)],
    ['Badges',   count($badges)],
    ['Aides',    $helpsCount],
];

$pdf->Ln(2);
$tileW  = $pageW / 4;
$tileH  = 22;
$tileY  = $pdf->GetY();

foreach ($statsData as $idx => [$label, $val]) {
    $tx = $lm + $idx * $tileW;
    $gap = 2;
    $pdf->SetFillColor($white[0], $white[1], $white[2]);
    $pdf->Rect($tx + $gap / 2, $tileY, $tileW - $gap, $tileH, 'F');
    $pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
    $pdf->Rect($tx + $gap / 2, $tileY, $tileW - $gap, 1.5, 'F');
    $pdf->SetXY($tx, $tileY + 2);
    $pdf->SetFont('B', 16);
    $pdf->SetTextColor($amber[0], $amber[1], $amber[2]);
    $pdf->Cell($tileW, 10, (string)$val, 0, 0, 'C');
    $pdf->SetXY($tx, $tileY + 13);
    $pdf->SetFont('', 6);
    $pdf->SetTextColor($midGray[0], $midGray[1], $midGray[2]);
    $pdf->Cell($tileW, 5, $label, 0, 0, 'C');
}
$pdf->SetXY($lm, $tileY + $tileH);
$pdf->Ln(6);

// ════════════════════════════════════════════════════════════════════════════
// SECTION HEADER HELPER
// ════════════════════════════════════════════════════════════════════════════
function sectionHeader($pdf, $lm, $pageW, $text, $blue, $white) {
    $pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
    $pdf->Rect($lm, $pdf->GetY(), $pageW, 10, 'F');
    $pdf->SetFont('B', 10);
    $pdf->SetTextColor($white[0], $white[1], $white[2]);
    $pdf->Cell($pageW, 10, '  ' . $text, 0, 1, 'L');
    $pdf->Ln(3);
}

// ════════════════════════════════════════════════════════════════════════════
// COMPETENCES TABLE
// ════════════════════════════════════════════════════════════════════════════
sectionHeader($pdf, $lm, $pageW,
    'Competences validees (' . count($skills) . ')',
    $blue, $white);

if (empty($skills)) {
    $pdf->SetFont('', 10);
    $pdf->SetTextColor($midGray[0], $midGray[1], $midGray[2]);
    $pdf->Cell($pageW, 8, 'Aucune competence validee pour le moment.', 0, 1, 'L');
} else {
    $headers = ['Competence', 'Categorie', 'Niveau', 'Validee le'];
    $hW      = [$pageW * 0.35, $pageW * 0.28, $pageW * 0.18, $pageW * 0.19];

    $pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
    $pdf->SetTextColor($white[0], $white[1], $white[2]);
    $pdf->SetDrawColor($borderGray[0], $borderGray[1], $borderGray[2]);
    $pdf->SetFont('B', 8);
    foreach ($headers as $i => $h) {
        $pdf->Cell($hW[$i], 7, $h, 1, 0, 'C', true);
    }
    $pdf->Ln();

    $rowCount = 0;
    foreach ($skills as $sk) {
        $levelLabel = match ($sk['niveau_estime']) {
            'Débutant'      => 'Debutant',
            'Intermédiaire' => 'Intermediaire',
            'Avancé'        => 'Avance',
            'Expert'        => 'Expert',
            default         => $sk['niveau_estime'],
        };
        $validatedDate = !empty($sk['date_validation'])
            ? date('d/m/Y', strtotime($sk['date_validation']))
            : date('d/m/Y', strtotime($sk['date_declaration']));

        $fillRow = $rowCount % 2 === 0;
        if ($fillRow) {
            $pdf->SetFillColor($rowAlt[0], $rowAlt[1], $rowAlt[2]);
        } else {
            $pdf->SetFillColor($white[0], $white[1], $white[2]);
        }
        $pdf->SetDrawColor($borderGray[0], $borderGray[1], $borderGray[2]);
        $pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
        $pdf->SetFont('', 8);
        $pdf->Cell($hW[0], 7, ' ' . h($sk['skill_name']), 1, 0, 'L', $fillRow);
        $pdf->Cell($hW[1], 7, ' ' . h($sk['skill_category']), 1, 0, 'L', $fillRow);

        $c = $sk['niveau_estime'] ?? '';
        $levelColor = match (true) {
            str_contains($c, 'Debut') || str_contains($c, 'Début') => [160, 160, 160],
            str_contains($c, 'Inter') || str_contains($c, 'Inter') => $blue,
            str_contains($c, 'Avance') || str_contains($c, 'Avancé') => [217, 119, 6],
            str_contains($c, 'Expert') || str_contains($c, 'expert') => [220, 38, 38],
            default => $dark,
        };
        $pdf->SetFont('B', 8);
        $pdf->SetTextColor($levelColor[0], $levelColor[1], $levelColor[2]);
        $pdf->Cell($hW[2], 7, $levelLabel, 1, 0, 'C', $fillRow);
        $pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
        $pdf->SetFont('', 8);
        $pdf->Cell($hW[3], 7, $validatedDate, 1, 1, 'L', $fillRow);
        $rowCount++;
    }
}

$pdf->Ln(6);

// ════════════════════════════════════════════════════════════════════════════
// BADGES TABLE
// ════════════════════════════════════════════════════════════════════════════
sectionHeader($pdf, $lm, $pageW,
    'Badges obtenus (' . count($badges) . ')',
    $blue, $white);

if (empty($badges)) {
    $pdf->SetFont('', 10);
    $pdf->SetTextColor($midGray[0], $midGray[1], $midGray[2]);
    $pdf->Cell($pageW, 8, 'Aucun badge obtenu pour le moment.', 0, 1, 'L');
} else {
    $bHeaders = ['Badge', 'Description', 'Points', 'Obtenu le'];
    $bW       = [$pageW * 0.28, $pageW * 0.40, $pageW * 0.13, $pageW * 0.19];

    $pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
    $pdf->SetTextColor($white[0], $white[1], $white[2]);
    $pdf->SetDrawColor($borderGray[0], $borderGray[1], $borderGray[2]);
    $pdf->SetFont('B', 8);
    foreach ($bHeaders as $i => $h) {
        $pdf->Cell($bW[$i], 7, $h, 1, 0, 'C', true);
    }
    $pdf->Ln();

    $rowCount = 0;
    foreach ($badges as $b) {
        $fillRow = $rowCount % 2 === 0;
        if ($fillRow) {
            $pdf->SetFillColor($rowAlt[0], $rowAlt[1], $rowAlt[2]);
        } else {
            $pdf->SetFillColor($white[0], $white[1], $white[2]);
        }
        $pdf->SetDrawColor($borderGray[0], $borderGray[1], $borderGray[2]);
        $pdf->SetTextColor($dark[0], $dark[1], $dark[2]);

        $pdf->SetFont('B', 8);
        $pdf->Cell($bW[0], 7, ' ' . h($b['nom']), 1, 0, 'L', $fillRow);
        $pdf->SetFont('', 7);
        $pdf->Cell($bW[1], 7, ' ' . h(mb_substr($b['description'] ?? '-', 0, 60)), 1, 0, 'L', $fillRow);
        $pdf->SetFont('B', 8);
        $pdf->SetTextColor($amber[0], $amber[1], $amber[2]);
        $pdf->Cell($bW[2], 7, (string)(int)$b['points_requis'], 1, 0, 'C', $fillRow);
        $pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
        $pdf->SetFont('', 8);
        $pdf->Cell($bW[3], 7, date('d/m/Y', strtotime($b['obtenu_le'])), 1, 1, 'L', $fillRow);
        $rowCount++;
    }
}

$pdf->Ln(6);

// ════════════════════════════════════════════════════════════════════════════
// PROGRESSION
// ════════════════════════════════════════════════════════════════════════════
sectionHeader($pdf, $lm, $pageW,
    'Progression', $blue, $white);

$progress = $levelInfo['progress'];
$barW     = $pageW;
$barH     = 12;

if ($levelInfo['next_min'] > $levelInfo['current_min']) {
    $pdf->SetFont('', 9);
    $pdf->SetTextColor($dark[0], $dark[1], $dark[2]);
    $pdf->Cell($barW, 6,
        'Niveau ' . $levelInfo['level'] . ' — ' . $levelInfo['name']
        . '   (' . $levelInfo['points'] . ' / ' . $levelInfo['next_min'] . ' pts)',
        0, 1, 'L');
    $pdf->Ln(1);

    $barY = $pdf->GetY();
    $pdf->SetFillColor($borderGray[0], $borderGray[1], $borderGray[2]);
    $pdf->Rect($lm, $barY, $barW, $barH, 'F');

    $fillW = max(1, $barW * $progress / 100);
    $pdf->SetFillColor($blue[0], $blue[1], $blue[2]);
    $pdf->Rect($lm, $barY, $fillW, $barH, 'F');

    $pdf->SetXY($lm, $barY);
    $pdf->SetFont('B', 8);
    $pdf->SetTextColor($white[0], $white[1], $white[2]);
    $pdf->Cell($barW, $barH, round($progress) . '% — vers le niveau ' . ($levelInfo['level'] + 1), 0, 1, 'C');

    $pdf->Ln(2);
    $pdf->SetFont('', 7);
    $pdf->SetTextColor($midGray[0], $midGray[1], $midGray[2]);
    $pdf->Cell($barW, 4, 'Progression: ' . round($progress) . '%', 0, 1, 'R');
} else {
    $pdf->SetFont('B', 11);
    $pdf->SetTextColor($amber[0], $amber[1], $amber[2]);
    $pdf->Cell($barW, 8, 'Niveau maximum atteint — ' . $levelInfo['name'] . ' !', 0, 1, 'C');
}

$pdf->Ln(10);

// ════════════════════════════════════════════════════════════════════════════
// FOOTER
// ════════════════════════════════════════════════════════════════════════════
$pdf->SetDrawColor($borderGray[0], $borderGray[1], $borderGray[2]);
$pdf->Line($lm, $pdf->GetY(), $lm + $pageW, $pdf->GetY());

$pdf->Ln(3);
$pdf->SetFont('', 7);
$pdf->SetTextColor($midGray[0], $midGray[1], $midGray[2]);
$pdf->Cell($pageW, 4,
    'Document genere le ' . date('d/m/Y \a H:i') . '  |  ISMO-SkillSwap',
    0, 1, 'C');
$pdf->Cell($pageW, 4,
    'Ce document est un recapitulatif officiel des competences et realisations.',
    0, 1, 'C');

// ════════════════════════════════════════════════════════════════════════════
// OUTPUT
// ════════════════════════════════════════════════════════════════════════════
$filename = 'passeport_competences_' . $targetUserId . '.pdf';
$pdf->Output($filename);
