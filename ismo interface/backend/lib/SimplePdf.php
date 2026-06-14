<?php

class SimplePdf {
    private float $x;
    private float $y;
    private float $lMargin = 20;
    private float $rMargin = 20;
    private float $tMargin = 20;
    private float $bMargin = 20;
    private int $currentPage = 0;
    private array $pages = [];
    private float $fontSize = 10;
    private string $fontStyle = '';
    private array $textColor = [0, 0, 0];
    private array $drawColor = [0, 0, 0];
    private array $fillColor = [220, 220, 220];
    private bool $autoPageBreak = true;
    private float $lineWidth = 0.2;
    private string $fontFamily = 'Helvetica';

    private const FW = 210; // Page width in mm
    private const FH = 297; // Page height in mm
    
    // 1 mm = 72 / 25.4 points
    private const MM_TO_PT = 2.83464567; 

    public function __construct() {
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
        $this->lineWidth = 0.2;
    }

    public function AddPage(): void {
        $this->currentPage++;
        $this->pages[$this->currentPage] = [];
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
    }

    public function SetFont(string $style = '', float $size = 10): void {
        $this->fontStyle = strtoupper($style);
        $this->fontSize = $size;
    }

    public function SetTextColor(int $r, int $g, int $b): void {
        $this->textColor = [$r, $g, $b];
    }

    public function SetDrawColor(int $r, int $g, int $b): void {
        $this->drawColor = [$r, $g, $b];
    }

    public function SetFillColor(int $r, int $g, int $b): void {
        $this->fillColor = [$r, $g, $b];
    }

    public function SetXY(float $x, float $y): void {
        $this->x = $x;
        $this->y = $y;
    }

    public function SetX(float $x): void {
        $this->x = $x;
    }

    public function GetX(): float { return $this->x; }
    public function GetY(): float { return $this->y; }
    public function GetPageWidth(): float { return self::FW; }
    public function GetPageHeight(): float { return self::FH; }
    public function GetLeftMargin(): float { return $this->lMargin; }

    public function Ln(float $h = 5): void {
        $this->x = $this->lMargin;
        $this->y += $h;
    }

    private function getFontName(): string {
        $s = $this->fontStyle;
        if ($s === 'B' || $s === 'BOLD') return 'Helvetica-Bold';
        if ($s === 'I' || $s === 'ITALIC') return 'Helvetica-Oblique';
        if ($s === 'BI' || $s === 'IB' || $s === 'BOLDITALIC') return 'Helvetica-BoldOblique';
        return 'Helvetica';
    }

    public function Cell(float $w, float $h, string $text, int $border = 0, int $ln = 0, string $align = 'L', bool $fill = false): void {
        if ($this->autoPageBreak && $this->y + $h > self::FH - $this->bMargin) {
            $this->AddPage();
        }
        $this->pages[$this->currentPage][] = [
            'type' => 'cell',
            'x' => $this->x, 'y' => $this->y,
            'w' => $w, 'h' => $h,
            'text' => $text, 'border' => $border,
            'align' => $align, 'fill' => $fill,
            'font' => $this->getFontName(),
            'fontSize' => $this->fontSize,
            'textColor' => $this->textColor,
            'drawColor' => $this->drawColor,
            'fillColor' => $this->fillColor,
        ];
        if ($ln) {
            $this->x = $this->lMargin;
            $this->y += $h;
        } else {
            $this->x += $w;
        }
    }

    public function Rect(float $x, float $y, float $w, float $h, string $style = 'D'): void {
        if ($this->autoPageBreak && $y + $h > self::FH - $this->bMargin) return;
        $this->pages[$this->currentPage][] = [
            'type' => 'rect',
            'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h,
            'style' => $style,
            'drawColor' => $this->drawColor,
            'fillColor' => $this->fillColor,
        ];
    }

    public function Line(float $x1, float $y1, float $x2, float $y2): void {
        if ($this->autoPageBreak && max($y1, $y2) > self::FH - $this->bMargin) return;
        $this->pages[$this->currentPage][] = [
            'type' => 'line',
            'x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2,
            'drawColor' => $this->drawColor,
            'lineWidth' => $this->lineWidth ?? 0.2,
        ];
    }

    public function SetLineWidth(float $w): void {
        $this->lineWidth = $w;
    }

    public function CheckPageBreak(float $h): bool {
        if ($this->y + $h > self::FH - $this->bMargin) {
            if ($this->autoPageBreak) {
                $this->AddPage();
                return true;
            }
            return false;
        }
        return true;
    }

    private function fixEncoding(string $text): string {
        $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        if ($converted !== false && $converted !== '') {
            $text = $converted;
        } elseif (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        }
        $out = '';
        for ($i = 0, $len = strlen($text); $i < $len; $i++) {
            $c = $text[$i];
            $o = ord($c);
            if ($o === 40 || $o === 41 || $o === 92) {
                $out .= '\\' . $c;
            } elseif ($o < 32 && $o !== 10 && $o !== 13) {
                continue;
            } else {
                $out .= $c;
            }
        }
        return $out;
    }

    private function textWidth(string $text, string $font, float $size): float {
        static $cw = [
            'Helvetica' => [
                ' '=>278,'!'=>278,'"'=>355,'#'=>469,'$'=>556,'%'=>889,'&'=>667,'\''=>222,
                '('=>333,')'=>333,'*'=>389,'+'=>584,','=>278,'-'=>333,'.'=>278,'/'=>278,
                '0'=>556,'1'=>556,'2'=>556,'3'=>556,'4'=>556,'5'=>556,'6'=>556,'7'=>556,
                '8'=>556,'9'=>556,':'=>278,';'=>278,'<'=>584,'='=>584,'>'=>584,'?'=>556,
                '@'=>1015,'A'=>667,'B'=>667,'C'=>722,'D'=>722,'E'=>667,'F'=>611,'G'=>778,
                'H'=>722,'I'=>278,'J'=>500,'K'=>667,'L'=>556,'M'=>833,'N'=>722,'O'=>778,
                'P'=>667,'Q'=>778,'R'=>722,'S'=>667,'T'=>611,'U'=>722,'V'=>667,'W'=>944,
                'X'=>667,'Y'=>667,'Z'=>611,'['=>333,'\\'=>278,']'=>333,'^'=>469,'_'=>556,
                '`'=>333,'a'=>556,'b'=>556,'c'=>500,'d'=>556,'e'=>556,'f'=>278,'g'=>556,
                'h'=>556,'i'=>222,'j'=>222,'k'=>500,'l'=>222,'m'=>833,'n'=>556,'o'=>556,
                'p'=>556,'q'=>556,'r'=>333,'s'=>500,'t'=>278,'u'=>556,'v'=>500,'w'=>722,
                'x'=>500,'y'=>500,'z'=>500,'{'=>334,'|'=>260,'}'=>334,'~'=>584,
            ],
            'Helvetica-Bold' => [
                ' '=>278,'!'=>333,'"'=>474,'#'=>556,'$'=>556,'%'=>889,'&'=>722,'\''=>238,
                '('=>333,')'=>333,'*'=>389,'+'=>584,','=>278,'-'=>333,'.'=>278,'/'=>278,
                '0'=>556,'1'=>556,'2'=>556,'3'=>556,'4'=>556,'5'=>556,'6'=>556,'7'=>556,
                '8'=>556,'9'=>556,':'=>333,';'=>333,'<'=>584,'='=>584,'>'=>584,'?'=>611,
                '@'=>975,'A'=>722,'B'=>722,'C'=>722,'D'=>778,'E'=>722,'F'=>667,'G'=>778,
                'H'=>833,'I'=>389,'J'=>500,'K'=>722,'L'=>611,'M'=>889,'N'=>833,'O'=>778,
                'P'=>667,'Q'=>778,'R'=>722,'S'=>667,'T'=>611,'U'=>722,'V'=>667,'W'=>944,
                'X'=>667,'Y'=>667,'Z'=>611,'['=>333,'\\'=>278,']'=>333,'^'=>556,'_'=>556,
                '`'=>333,'a'=>611,'b'=>611,'c'=>556,'d'=>611,'e'=>611,'f'=>333,'g'=>611,
                'h'=>611,'i'=>278,'j'=>278,'k'=>556,'l'=>278,'m'=>889,'n'=>611,'o'=>611,
                'p'=>611,'q'=>611,'r'=>389,'s'=>556,'t'=>333,'u'=>611,'v'=>556,'w'=>778,
                'x'=>556,'y'=>556,'z'=>500,'{'=>389,'|'=>280,'}'=>389,'~'=>584,
            ],
        ];
        $scale = $size / 1000;
        $w = 0;
        $chars = $cw[$font] ?? $cw['Helvetica'];
        for ($i = 0, $len = strlen($text); $i < $len; $i++) {
            $c = $text[$i];
            $o = ord($c);
            if ($o >= 128 && $o <= 255) {
                $w += 556;
            } else {
                $w += $chars[$c] ?? 556;
            }
        }
        return $w * $scale;
    }

    private function renderContent(int $pageNum, array $fontObjNums): string {
        $ops = $this->pages[$pageNum] ?? [];
        $buf = '';
        $k = self::MM_TO_PT;

        foreach ($ops as $op) {
            if ($op['type'] === 'cell') {
                $f = $op['font'];
                $fontNum = $fontObjNums[$f];
                $x = $op['x'] * $k;
                $y = (self::FH - $op['y'] - $op['h']) * $k;
                $pw = $op['w'] * $k;
                $ph = $op['h'] * $k;

                if ($op['fill'] || $op['border']) {
                    if ($op['fill']) {
                        $buf .= sprintf("%.3f %.3f %.3f rg\n", $op['fillColor'][0]/255, $op['fillColor'][1]/255, $op['fillColor'][2]/255);
                    }
                    if ($op['border']) {
                        $buf .= sprintf("%.3f %.3f %.3f RG\n", $op['drawColor'][0]/255, $op['drawColor'][1]/255, $op['drawColor'][2]/255);
                    }
                    $buf .= sprintf("%.3f %.3f %.3f %.3f re\n", $x, $y, $pw, $ph);
                    if ($op['fill'] && $op['border']) $buf .= "B\n";
                    elseif ($op['border']) $buf .= "S\n";
                    else $buf .= "f\n";
                }

                $text = $this->fixEncoding($op['text']);
                if ($text !== '') {
                    $tw = $this->textWidth($text, $f, $op['fontSize']) * $k;
                    $tx = $x + (2 * $k);
                    if ($op['align'] === 'C') $tx = $x + ($pw - $tw) / 2;
                    elseif ($op['align'] === 'R') $tx = $x + $pw - $tw - (2 * $k);
                    $ty = $y + ($ph / 2) - ($op['fontSize'] * 0.3);
                    $buf .= sprintf("BT /F%d %.3f Tf %.3f %.3f %.3f rg %.3f %.3f Td (%s) Tj ET\n",
                        $fontNum, $op['fontSize'],
                        $op['textColor'][0]/255, $op['textColor'][1]/255, $op['textColor'][2]/255,
                        $tx, $ty, $text);
                }
            } elseif ($op['type'] === 'rect') {
                $x = $op['x'] * $k;
                $y = (self::FH - $op['y'] - $op['h']) * $k;
                $pw = $op['w'] * $k;
                $ph = $op['h'] * $k;
                $s = $op['style'];
                if ($s === 'F' || $s === 'DF' || $s === 'FD') {
                    $buf .= sprintf("%.3f %.3f %.3f rg\n", $op['fillColor'][0]/255, $op['fillColor'][1]/255, $op['fillColor'][2]/255);
                }
                if ($s === 'D' || $s === 'DF' || $s === 'FD') {
                    $buf .= sprintf("%.3f %.3f %.3f RG\n", $op['drawColor'][0]/255, $op['drawColor'][1]/255, $op['drawColor'][2]/255);
                }
                $buf .= sprintf("%.3f %.3f %.3f %.3f re\n", $x, $y, $pw, $ph);
                if ($s === 'F') $buf .= "f\n";
                elseif ($s === 'D') $buf .= "S\n";
                else $buf .= "B\n";
            } elseif ($op['type'] === 'line') {
                $x1 = $op['x1'] * $k;
                $y1 = (self::FH - $op['y1']) * $k;
                $x2 = $op['x2'] * $k;
                $y2 = (self::FH - $op['y2']) * $k;
                $buf .= sprintf("%.3f w\n", $op['lineWidth'] * $k);
                $buf .= sprintf("%.3f %.3f %.3f RG\n", $op['drawColor'][0]/255, $op['drawColor'][1]/255, $op['drawColor'][2]/255);
                $buf .= sprintf("%.3f %.3f m %.3f %.3f l S\n", $x1, $y1, $x2, $y2);
            }
        }

        return $buf;
    }

    private function usedFonts(int $pageNum): array {
        $ops = $this->pages[$pageNum] ?? [];
        $fonts = [];
        foreach ($ops as $op) {
            if ($op['type'] === 'cell') $fonts[$op['font']] = true;
        }
        return $fonts;
    }

    public function Render(): string {
        $pdf = "%PDF-1.4\n";
        $allFonts = ['Helvetica', 'Helvetica-Bold', 'Helvetica-Oblique', 'Helvetica-BoldOblique'];

        $fontObjNums = [];
        $objNum = 1;

        // Register static object IDs for standard fonts mapping to custom font numbers /F1 - /F4
        $fontIdx = 1;
        foreach ($allFonts as $fn) {
            $fontObjNums[$fn] = $fontIdx;
            $pdf .= sprintf("%d 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /%s /Encoding /WinAnsiEncoding >>\nendobj\n",
                $objNum, $fn);
            $objNum++;
            $fontIdx++;
        }

        $pageObjStart = $objNum;
        $contentObjStart = $pageObjStart + $this->currentPage;
        $pagesObjNum = $contentObjStart + $this->currentPage;

        $pageRefs = [];
        for ($p = 1; $p <= $this->currentPage; $p++) {
            $content = $this->renderContent($p, $fontObjNums);
            $contentLen = strlen($content);
            $contentObjNum = $contentObjStart + $p - 1;
            $pageObjNum = $pageObjStart + $p - 1;
            $pageRefs[] = $pageObjNum;

            $fs = $this->usedFonts($p);
            $fontResources = '';
            foreach ($fs as $fn => $true) {
                $fontResources .= sprintf('/F%d %d 0 R ', $fontObjNums[$fn], $fontObjNums[$fn]);
            }

            $pageH = self::FH * self::MM_TO_PT;
            $pageW = self::FW * self::MM_TO_PT;

            $pdf .= sprintf("%d 0 obj\n<< /Type /Page /Parent %d 0 R /MediaBox [0 0 %.2f %.2f] /Contents %d 0 R /Resources << /Font << %s >> >> >>\nendobj\n",
                $pageObjNum, $pagesObjNum, $pageW, $pageH, $contentObjNum, $fontResources);

            $pdf .= sprintf("%d 0 obj\n<< /Length %d >>\nstream\n%s\nendstream\nendobj\n",
                $contentObjNum, $contentLen, $content);
        }
        
        $pdf .= sprintf("%d 0 obj\n<< /Type /Pages /Kids [%s] /Count %d >>\nendobj\n",
            $pagesObjNum, implode(' 0 R ', $pageRefs) . ' 0 R', $this->currentPage);

        $catalogObjNum = $pagesObjNum + 1;
        $pdf .= sprintf("%d 0 obj\n<< /Type /Catalog /Pages %d 0 R >>\nendobj\n",
            $catalogObjNum, $pagesObjNum);

        $objectsCount = $catalogObjNum;

        $startxref = strlen($pdf);
        $offsets = [];
        preg_match_all('/^(\d+) 0 obj/m', $pdf, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $offsets[(int)$m[1]] = strpos($pdf, $m[0]);
        }

        $pdf .= "xref\n";
        $pdf .= sprintf("0 %d\n", $objectsCount + 1);
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $objectsCount; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n";
        $pdf .= sprintf("<< /Size %d /Root %d 0 R >>\n", $objectsCount + 1, $catalogObjNum);
        $pdf .= "startxref\n";
        $pdf .= "$startxref\n";
        $pdf .= "%%EOF\n";

        return $pdf;
    }

    public function Output(string $filename = 'doc.pdf'): never {
        $pdf = $this->Render();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }
}