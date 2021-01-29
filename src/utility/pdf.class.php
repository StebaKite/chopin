<?php

require_once 'fpdf.php';
require_once 'utility.component.interface.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';

class Pdf extends FPDF implements UtilityComponentInterface {

    public $title;
    public $title1;
    public $title2;
    public $creator;
    public $logo;
    private static $_instance = null;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        parent::__construct();

        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    function initialize($orientation = 'P', $unit = 'mm', $size = 'A4') {
        // Some checks
        $this->_dochecks();
        // Initialization of properties
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = array();
        $this->PageSizes = array();
        $this->state = 0;
        $this->fonts = array();
        $this->FontFiles = array();
        $this->diffs = array();
        $this->images = array();
        $this->links = array();
        $this->InHeader = false;
        $this->InFooter = false;
        $this->lasth = 0;
        $this->FontFamily = '';
        $this->FontStyle = '';
        $this->FontSizePt = 12;
        $this->underline = false;
        $this->DrawColor = '0 G';
        $this->FillColor = '0 g';
        $this->TextColor = '0 g';
        $this->ColorFlag = false;
        $this->ws = 0;

        // Font path
        if (defined('FPDF_FONTPATH')) {
            $this->fontpath = FPDF_FONTPATH;
            if (substr($this->fontpath, -1) != '/' && substr($this->fontpath, -1) != '\\')
                $this->fontpath .= '/';
        }
        elseif (is_dir(dirname(__FILE__) . '/font'))
            $this->fontpath = dirname(__FILE__) . '/font/';
        else
            $this->fontpath = '';

        // Core fonts
        $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
        // Scale factor
        if ($unit == 'pt')
            $this->k = 1;
        elseif ($unit == 'mm')
            $this->k = 72 / 25.4;
        elseif ($unit == 'cm')
            $this->k = 72 / 2.54;
        elseif ($unit == 'in')
            $this->k = 72;
        else
            $this->Error('Incorrect unit: ' . $unit);

        // Page sizes
        $this->StdPageSizes = array('a3' => array(841.89, 1190.55), 'a4' => array(595.28, 841.89), 'a5' => array(420.94, 595.28),
            'letter' => array(612, 792), 'legal' => array(612, 1008));
        $size = $this->_getpagesize($size);
        $this->DefPageSize = $size;
        $this->CurPageSize = $size;
        // Page orientation
        $orientation = strtolower($orientation);

        if ($orientation == 'p' || $orientation == 'portrait') {
            $this->DefOrientation = 'P';
            $this->w = $size[0];
            $this->h = $size[1];
        } elseif ($orientation == 'l' || $orientation == 'landscape') {
            $this->DefOrientation = 'L';
            $this->w = $size[1];
            $this->h = $size[0];
        } else
            $this->Error('Incorrect orientation: ' . $orientation);

        $this->CurOrientation = $this->DefOrientation;
        $this->wPt = $this->w * $this->k;
        $this->hPt = $this->h * $this->k;
        // Page margins (1 cm)
        $margin = 28.35 / $this->k;
        $this->SetMargins($margin, $margin);
        // Interior cell margin (1 mm)
        $this->cMargin = $margin / 10;
        // Line width (0.2 mm)
        $this->LineWidth = .567 / $this->k;
        // Automatic page break
        $this->SetAutoPageBreak(true, 2 * $margin);
        // Default display mode
        $this->SetDisplayMode('default');
        // Enable compression
        $this->SetCompression(true);
        // Set default PDF version number
        $this->PDFVersion = '1.3';
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::PDF]))
            $_SESSION[self::PDF] = serialize(new Pdf());
        return unserialize($_SESSION[self::PDF]);
    }

    public function Header() {
        if (defined(self::EURO)) {}
        else {
            define(self::EURO, chr(128));        
        }

        $this->Image($this->getLogo(), 5, 5, 20);

        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', $this->getTitle()), 0, 0, 'C');
        $this->Ln();

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', $this->getTitle1()), 0, 0, 'C');
        $this->Ln();

        if ($this->getTitle2() != self::EMPTYSTRING) {
            $this->SetFont('Arial', 'I', 12);
            $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', $this->getTitle2()), 0, 0, 'C');
            $this->Ln();
        }

        $this->Ln(10);
    }

    public function Footer() {

        $this->SetY(-15);            // Position at 1.5 cm from bottom
        $this->SetFont('Arial', 'I', 8);         // Arial italic 8
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C'); // Page number

        $this->SetY(-10);            // Position at 1 cm from bottom
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Generato dal database di Nexus8 il " . date("d/m/Y")), 0, 1, 'C');
    }

    // Simple table
    public function BasicTable($header, $data) {
        foreach ($data as $row) {
            foreach ($row as $col)
                $this->Cell(190, 6, $col, 1);
            $this->Ln();
        }
    }

    // Better table
    public function ImprovedTable($header, $data) {

        // Column widths
        $w = array(40, 35, 40, 45);
        // Header
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        $this->Ln();
        // Data
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR');
            $this->Cell($w[1], 6, $row[1], 'LR');
            $this->Cell($w[2], 6, number_format(floatval($row[2])), 'LR', 0, 'R');
            $this->Cell($w[3], 6, number_format(floatval($row[3])), 'LR', 0, 'R');
            $this->Ln();
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }

    /**     * ***********************************************************
     * MultiCell con bullet (array)
     *
     * E' richiesta una array con le seguenti colonne:
     *
     * 		Bullet	-> Stringa o Numero
     * 		Margine -> Numero, spazio fra il bullet e il testo
     * 		Indent	-> Numero, spazio dalla posizione corrente
     * 		Spacer	-> Numero, chiama Cell(x), spacer=x
     * 		Text	-> Array, elementi da inserire nell'elenco
     *
     * *************************************************************
     */
    public function MultiCellBulletList($w, $h, $blt_array, $border = 0, $align = 'J', $fill = 0) {

        if (is_array($blt_array)) {

            $bak_x = $this->x;

            for ($i = 0; $i < sizeof($blt_array['text']); $i++) {

                // Prendo il bullet incluso il margine
                $blt_width = $this->GetStringWidth($blt_array['bullet'] . $blt_array['margin']) + $this->cMargin * 2;
                $this->SetX($bak_x);

                // indentazione
                if ($blt_array['indent'] > 0)
                    $this->Cell($blt_array['indent']);

                // output del bullet
                $this->Cell($blt_width, $h, $blt_array['bullet'] . $blt_array['margin'], 0, '', $fill);

                // output del testo
                $this->MultiCell($w - $blt_width, $h, $blt_array['text'][$i], $border, $align, $fill);

                // Inserisco lo spacer fra gli elementi se non è l'ultima linea
                if ($i != sizeof($blt_array['text']) - 1)
                    $this->Ln($blt_array['spacer']);

                // Incremento il bullet se è un numero
                if (is_numeric($blt_array['bullet']))
                    $blt_array['bullet'] ++;

                // ripristino x
                $this->x = $bak_x;
            }
        }
    }

    public function makeMastrinoContoTable($header, $data) {

        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B', 10);

        // Header
        $w = array(20, 80, 30, 30, 30);
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->SetFont('', '', 8);

        // Data
        $totaleDare = 0;
        $totaleAvere = 0;
        $saldo = 0;

        $fill = false;
        foreach ($data as $row) {

            if ($row['ind_dareavere'] == 'D') {
                $totaleDare = $totaleDare + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $impDare = $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $euroAvere = "";
                $impAvere = "";
            } elseif ($row['ind_dareavere'] == 'A') {
                $totaleAvere = $totaleAvere + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $impDare = "";
                $impAvere = $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                $euroDare = "";
            }

            if (trim($row[Conto::TIP_CONTO]) == self::CONTO_IN_DARE) {
                $saldo = $totaleDare - $totaleAvere;
            } elseif (trim($row[Conto::TIP_CONTO]) == self::CONTO_IN_AVERE) {
                $saldo = $totaleAvere - $totaleDare;
            }

            $this->SetFont('', '', 10);
            $fill = !$fill;

            $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', date("d/m/Y", strtotime($row[Registrazione::DAT_REGISTRAZIONE]))), 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, iconv('UTF-8', 'windows-1252', trim($row[Registrazione::DES_REGISTRAZIONE])), 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, number_format(floatval($impDare), 2, ',', '.'), 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, number_format(floatval($impAvere), 2, ',', '.'), 'LR', 0, 'R', $fill);

            if ($saldo < 0) {
                $this->SetTextColor(255, 0, 0);
                $this->SetFont('', 'B', 10);
            }

            $this->Cell($w[4], 6, number_format(floatval($saldo), 2, ',', '.'), 'LR', 0, 'R', $fill);
            $this->Ln();

            $this->SetFont('');
            $this->SetTextColor(0);
        }

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);

        $this->SetFont('', 'B', 10);
        $this->Cell($w[0], 6, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 6, 'Totale ' . EURO, 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 6, number_format(floatval($totaleDare), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 6, number_format(floatval($totaleAvere), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[4], 6, number_format(floatval($saldo), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function BilancioTable($data, $invSegno) {

        // Column widths
        $w = array(150, 25);

        $desconto_break = "";
        $ind_visibilita_sottoconti_break = "";
        $totaleConto = 0;

        $sottoconti = array();

        // Data
        foreach ($data as $row) {

            $totaleSottoconto = trim($row['tot_conto']);

            $importo = number_format((floatval($totaleSottoconto * $invSegno)), 2, ',', '.');

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totconto = number_format((floatval($totaleConto * $invSegno)), 2, ',', '.');

                    $this->Ln();
                    $this->SetFont('', 'B', 12);
                    $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $desconto_break), '', 0, 'L');
                    $this->Cell($w[1], 6, $totconto, '', 0, 'R');

                    if ($ind_visibilita_sottoconti_break == 'S') {

                        foreach ($sottoconti as $sottoconto) {

                            $this->Ln();
                            $this->SetFont('', '', 11);
                            $this->Cell($w[0], 6, str_repeat(' ', 7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']), '', 0, 'L');
                            $this->Cell($w[1], 6, $sottoconto['importo'] . str_repeat(' ', 35), '', 0, 'R');
                        }
                    }

                    $this->Ln();
                    $i = 0;
                    $totaleConto = 0;
                    $sottoconti = array();
                }

                array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));

                $desconto_break = trim($row['des_conto']);
                $ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
            } else {
                array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));
            }
            $totaleConto += $totaleSottoconto;
        }

        /**
         * Ultimo totale di fine ciclo
         */
        $totconto = number_format((floatval($totaleConto * $invSegno)), 2, ',', '.');

        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $desconto_break), '', 0, 'L');
        $this->Cell($w[1], 6, $totconto, '', 0, 'R');

        if ($ind_visibilita_sottoconti_break == 'S') {

            foreach ($sottoconti as $sottoconto) {

                $this->Ln();
                $this->SetFont('', '', 11);
                $this->Cell($w[0], 6, str_repeat(' ', 7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']), '', 0, 'L');
                $this->Cell($w[1], 6, $sottoconto['importo'] . str_repeat(' ', 35), '', 0, 'R');
            }
        }
    }

    public function BilancioTableRicavi($data, $invSegno) {

        // Column widths
        $w = array(150, 25);

        $desconto_break = "";
        $ind_visibilita_sottoconti_break = "";
        $totaleConto = 0;

        $sottoconti = array();

        // Data
        foreach ($data as $row) {

            $totaleSottoconto = trim($row['tot_conto']);

            $importo = number_format((floatval($totaleSottoconto * $invSegno)), 2, ',', '.');

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totconto = number_format((floatval($totaleConto * $invSegno)), 2, ',', '.');

                    $this->Ln();
                    $this->SetFont('', 'B', 12);
                    $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $desconto_break), '', 0, 'L');
                    $this->Cell($w[1], 6, $totconto, '', 0, 'R');

                    if ($ind_visibilita_sottoconti_break == 'S') {

                        foreach ($sottoconti as $sottoconto) {

                            $this->Ln();
                            $this->SetFont('', '', 11);
                            $this->Cell($w[0], 6, str_repeat(' ', 7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']), '', 0, 'L');
                            $this->Cell($w[1], 6, $sottoconto['importo'] . str_repeat(' ', 35), '', 0, 'R');
                        }
                    }

                    $this->Ln();
                    $i = 0;
                    $totaleConto = 0;
                    $sottoconti = array();
                }

                array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));

                $desconto_break = trim($row['des_conto']);
                $ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
            } else {
                array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));
            }
            $totaleConto += $totaleSottoconto;
        }

        /**
         * Ultimo totale di fine ciclo
         */
        $totconto = number_format(abs(floatval($totaleConto)), 2, ',', '.');

        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $desconto_break), '', 0, 'L');
        $this->Cell($w[1], 6, $totconto, '', 0, 'R');

        if ($ind_visibilita_sottoconti_break == 'S') {

            foreach ($sottoconti as $sottoconto) {

                $this->Ln();
                $this->SetFont('', '', 11);
                $this->Cell($w[0], 6, str_repeat(' ', 7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']), '', 0, 'L');
                $this->Cell($w[1], 6, $sottoconto['importo'] . str_repeat(' ', 35), '', 0, 'R');
            }
        }
    }

    public function BilancioEsercizioTable($data) {

        // Column widths
        $w = array(150, 25);

        $desconto_break = "";
        $ind_visibilita_sottoconti_break = "";
        $totaleConto = 0;

        $sottoconti = array();

        // Data
        foreach ($data as $row) {

            $totaleSottoconto = trim($row['tot_conto']);

            $importo = number_format(abs(floatval($totaleSottoconto)), 2, ',', '.');

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totconto = number_format(abs(floatval($totaleConto)), 2, ',', '.');

                    if ($totconto > 0) {
                        $this->Ln();
                        $this->SetFont('', 'B', 12);
                        $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $desconto_break), '', 0, 'L');
                        $this->Cell($w[1], 6, $totconto, '', 0, 'R');
                    }

                    if ($ind_visibilita_sottoconti_break == 'S') {

                        foreach ($sottoconti as $sottoconto) {

                            if ($sottoconto['importo'] > 0) {
                                $this->Ln();
                                $this->SetFont('', '', 11);
                                $this->Cell($w[0], 6, str_repeat(' ', 7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']), '', 0, 'L');
                                $this->Cell($w[1], 6, $sottoconto['importo'] . str_repeat(' ', 35), '', 0, 'R');
                            }
                        }
                    }
                    $this->Ln();
                    $i = 0;
                    $totaleConto = 0;
                    $sottoconti = array();
                }

                array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));

                $desconto_break = trim($row['des_conto']);
                $ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
            } else {
                array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));
            }
            $totaleConto += $totaleSottoconto;
        }

        /**
         * Ultimo totale di fine ciclo
         */
        $totconto = number_format(abs(floatval($totaleConto)), 2, ',', '.');

        if ($totconto > 0) {
            $this->Ln();
            $this->SetFont('', 'B', 12);
            $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $desconto_break), '', 0, 'L');
            $this->Cell($w[1], 6, $totconto, '', 0, 'R');
        }

        if ($ind_visibilita_sottoconti_break == 'S') {

            foreach ($sottoconti as $sottoconto) {

                if ($sottoconto['importo'] > 0) {
                    $this->Ln();
                    $this->SetFont('', '', 11);
                    $this->Cell($w[0], 6, '       ' . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']), '', 0, 'L');
                    $this->Cell($w[1], 6, $sottoconto['importo'] . str_repeat(' ', 35), '', 0, 'R');
                }
            }
        }
    }

    public function BilancioCostiTable($totaleRicavi, $totaleCosti) {

        // Column widths
        $w = array(100, 50, 25);

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->SetTextColor(51, 153, 255);

        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, '______________________________________', '', 0, 'L');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Totale Ricavi', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(abs(floatval($totaleRicavi)), 2, ',', '.'), '', 0, 'R');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Totale Costi', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(abs(floatval($totaleCosti)), 2, ',', '.'), '', 0, 'R');

        $utile = $totaleRicavi - $totaleCosti;

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Utile del periodo', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(floatval($utile), 2, ',', '.'), '', 0, 'R');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, '______________________________________', '', 0, 'L');

        $this->SetTextColor(0);
    }

    public function BilancioRicaviTable($totaleRicavi, $totaleCosti) {

        // Column widths
        $w = array(100, 50, 25);

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->SetTextColor(51, 153, 255);

        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'RIEPILOGO________________________', '', 0, 'L');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Totale Ricavi', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(abs(floatval($totaleRicavi)), 2, ',', '.'), '', 0, 'R');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Totale Costi', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(abs(floatval($totaleCosti)), 2, ',', '.'), '', 0, 'R');

        $perdita = $totaleRicavi - $totaleCosti;

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Perdita del periodo', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(floatval($perdita), 2, ',', '.'), '', 0, 'R');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, '________________________________', '', 0, 'L');

        $this->SetTextColor(0);
    }

    public function TotaleCostiTable($totaleCosti) {

        // Column widths
        $w = array(100, 50, 25);

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->SetTextColor(51, 153, 255);

        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, '______________________________________', '', 0, 'L');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Totale Costi', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(floatval($totaleCosti), 2, ',', '.'), '', 0, 'R');

        $this->SetTextColor(0);
    }

    public function TotaleRicaviTable($totaleRicavi) {

        // Column widths
        $w = array(100, 50, 25);

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->SetTextColor(51, 153, 255);

        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, '______________________________________', '', 0, 'L');

        $this->Ln();
        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, 'Totale Ricavi', '', 0, 'L');
        $this->Cell($w[2], 8, number_format(floatval($totaleRicavi), 2, ',', '.'), '', 0, 'R');

        $this->SetTextColor(0);
    }

    public function TotaleAttivoTable($totaleAttivo) {

        // Column widths
        $w = array(100, 50, 25);

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->SetTextColor(51, 153, 255);

        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, iconv('UTF-8', 'windows-1252', 'Totale Attività'), '', 0, 'L');
        $this->Cell($w[2], 8, number_format(abs(floatval($totaleAttivo)), 2, ',', '.'), '', 0, 'R');

        $this->SetTextColor(0);
    }

    public function TotalePassivoTable($totalePassivo) {

        // Column widths
        $w = array(100, 50, 25);

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('', 'B', 12);
        $this->SetTextColor(51, 153, 255);

        $this->Cell($w[0], 8, '', '', 0, 'L');
        $this->Cell($w[1], 8, iconv('UTF-8', 'windows-1252', 'Totale Passività'), '', 0, 'L');
        $this->Cell($w[2], 8, number_format(abs(floatval($totalePassivo)), 2, ',', '.'), '', 0, 'R');

        $this->SetTextColor(0);
    }

    /**
     * Questo metodo crea una tabella PDF per il riepilogo negozi
     */
    public function riepilogoNegoziTable($header, $data, $invSegno) {

        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(150, 30, 30, 30, 30);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->SetFont('', '', 8);

        $numReg = 0;
        $totaleCosti = 0;
        $desconto_break = "";

        $totaleConto_Bre = 0;
        $totaleConto_Tre = 0;
        $totaleConto_Vil = 0;

        $totale_Bre = 0;
        $totale_Tre = 0;
        $totale_Vil = 0;

        foreach ($data as $row) {

            $totaleConto = trim($row['tot_conto']);
            $totaleCosti += $totaleConto;

            if (trim($row['cod_negozio']) == self::BREMBATE)
                $totale_Bre += $totaleConto;
            if (trim($row['cod_negozio']) == self::TREZZO)
                $totale_Tre += $totaleConto;
            if (trim($row['cod_negozio']) == self::VILLA)
                $totale_Vil += $totaleConto;

            $numReg ++;

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    $totBre = ($totaleConto_Bre != 0) ? number_format((floatval($totaleConto_Bre * $invSegno)), 2, ',', '.') : "---";
                    $totTre = ($totaleConto_Tre != 0) ? number_format((floatval($totaleConto_Tre * $invSegno)), 2, ',', '.') : "---";
                    $totVil = ($totaleConto_Vil != 0) ? number_format((floatval($totaleConto_Vil * $invSegno)), 2, ',', '.') : "---";

                    $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
                    $tot = ($totale != 0) ? number_format((floatval($totale * $invSegno)), 2, ',', '.') : "---";

                    $this->SetFont('', '', 10);
                    $fill = !$fill;

                    $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', trim($desconto_break)), 'LR', 0, 'L', $fill);
                    $this->Cell($w[1], 6, $totBre, 'LR', 0, 'R', $fill);
                    $this->Cell($w[2], 6, $totTre, 'LR', 0, 'R', $fill);
                    $this->Cell($w[3], 6, $totVil, 'LR', 0, 'R', $fill);

                    $this->SetFont('', 'B', 10);
                    $this->Cell($w[4], 6, $tot, 'LR', 0, 'R', $fill);
                    $this->Ln();

                    $totaleConto_Bre = 0;
                    $totaleConto_Tre = 0;
                    $totaleConto_Vil = 0;
                }

                $desconto_break = trim($row['des_conto']);
            }

            if (trim($row['cod_negozio']) == self::BREMBATE)
                $totaleConto_Bre += $totaleConto;
            if (trim($row['cod_negozio']) == self::TREZZO)
                $totaleConto_Tre += $totaleConto;
            if (trim($row['cod_negozio']) == self::VILLA)
                $totaleConto_Vil += $totaleConto;
        }

        $totBre = ($totaleConto_Bre != 0) ? number_format((floatval($totaleConto_Bre * $invSegno)), 2, ',', '.') : "---";
        $totTre = ($totaleConto_Tre != 0) ? number_format((floatval($totaleConto_Tre * $invSegno)), 2, ',', '.') : "---";
        $totVil = ($totaleConto_Vil != 0) ? number_format((floatval($totaleConto_Vil * $invSegno)), 2, ',', '.') : "---";

        $totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
        $tot = ($totale != 0) ? number_format((floatval($totale * $invSegno)), 2, ',', '.') : "---";

        $this->SetFont('', '', 10);
        $fill = !$fill;

        $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', trim($desconto_break)), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 6, $totBre, 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 6, $totTre, 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 6, $totVil, 'LR', 0, 'R', $fill);
        $this->SetFont('', 'B', 10);
        $this->Cell($w[4], 6, $tot, 'LR', 0, 'R', $fill);
        $this->Ln();

        /**
         * Totale complessivo di colonna
         */
        $totBre = ($totale_Bre != 0) ? number_format((floatval($totale_Bre * $invSegno)), 2, ',', '.') : "---";
        $totTre = ($totale_Tre != 0) ? number_format((floatval($totale_Tre * $invSegno)), 2, ',', '.') : "---";
        $totVil = ($totale_Vil != 0) ? number_format((floatval($totale_Vil * $invSegno)), 2, ',', '.') : "---";

        $totale = $totale_Bre + $totale_Tre + $totale_Vil;
        $tot = ($totale != 0) ? number_format((floatval($totale * $invSegno)), 2, ',', '.') : "---";

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $fill = !$fill;

        $this->SetFont('', 'B', 10);
        $this->Cell($w[0], 6, "TOTALE", 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 6, $totBre, 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 6, $totTre, 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 6, $totVil, 'LR', 0, 'R', $fill);
        $this->Cell($w[4], 6, $tot, 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    /**
     * Questo metodo crea una tabella PDF del margine di contribuzione per il riepilogo negozi
     */
    public function riepilogoNegoziMctTable($header, $datiMCT) {

        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(100, 30, 30, 30, 30);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Fatturato")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(abs(floatval($datiMCT["totaleRicaviBRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(abs(floatval($datiMCT["totaleRicaviTRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleRicaviVIL"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleRicavi"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Acquisti")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabiliBRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabiliTRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabiliVIL"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabili"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Margine assoluto")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(floatval($datiMCT["margineTotaleBRE"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(floatval($datiMCT["margineTotaleTRE"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["margineTotaleVIL"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["margineTotale"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Margine percentuale")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(floatval($datiMCT["marginePercentualeBRE"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(floatval($datiMCT["marginePercentualeTRE"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["marginePercentualeVIL"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["marginePercentuale"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Ricarico percentuale")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(floatval($datiMCT["ricaricoPercentualeBRE"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(floatval($datiMCT["ricaricoPercentualeTRE"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["ricaricoPercentualeVIL"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["ricaricoPercentuale"]), 2, ',', '.') . " %", 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    /**
     * Questo metodo crea una tabella PDF del Break Even Point per il riepilogo negozi
     */
    public function riepilogoNegoziBepTable($header, $datiMCT) {

        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(100, 30, 30, 30, 30);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Fatturato")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(abs(floatval($datiMCT["totaleRicaviBRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(abs(floatval($datiMCT["totaleRicaviTRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleRicaviVIL"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleRicavi"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Costi fissi")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(abs(floatval($datiMCT["totaleCostiFissiBRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(abs(floatval($datiMCT["totaleCostiFissiTRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleCostiFissiVIL"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleCostiFissi"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Acquisti")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabiliBRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabiliTRE"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabiliVIL"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($datiMCT["totaleCostiVariabili"])), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Incidenza acquisti sul fatturato")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(floatval($datiMCT["incidenzaCostiVariabiliSulFatturatoBRE"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(floatval($datiMCT["incidenzaCostiVariabiliSulFatturatoTRE"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["incidenzaCostiVariabiliSulFatturatoVIL"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["incidenzaCostiVariabiliSulFatturato"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("BEP totale")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(floatval($datiMCT["bepBRE"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(floatval($datiMCT["bepTRE"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["bepVIL"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($datiMCT["bep"]), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function riepilogoNegoziTotaliTable($header, $riepilogo) {

        $fill = "";
        
        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(150, 30, 30, 30, 30);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);

        $fill = !$fill;
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Totale ricavi")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(abs(floatval($riepilogo->getTotaleRicaviBrembate())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(abs(floatval($riepilogo->getTotaleRicaviTrezzo())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($riepilogo->getTotaleRicaviVilla())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->SetFont('', 'B', 10);
        $this->Cell($w[4], 8, number_format(abs(floatval($riepilogo->getTotaleRicavi())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->SetFont('', '', 10);
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim("Totale costi")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(abs(floatval($riepilogo->getTotaleCostiBrembate())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(abs(floatval($riepilogo->getTotaleCostiTrezzo())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(abs(floatval($riepilogo->getTotaleCostiVilla())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->SetFont('', 'B', 10);
        $this->Cell($w[4], 8, number_format(abs(floatval($riepilogo->getTotaleCosti())), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->SetFont('', 'B', 10);
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim($header[0] . " del periodo")), 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 8, number_format(floatval($riepilogo->getUtileBrembate()), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 8, number_format(floatval($riepilogo->getUtileTrezzo()), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 8, number_format(floatval($riepilogo->getUtileVilla()), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Cell($w[4], 8, number_format(floatval($riepilogo->getTotaleUtile()), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function progressiviNegozioTable($header, $vociNegozio, $invSegno) {

        $fill = "";
        
        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(70, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 20);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);

        $desconto_break = "";
        $totaliMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);    // dodici mesi
        $totaliComplessiviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); // dodici mesi
//        $classe = array('', '', '', '', '', '', '', '', '', '', '', '');  // dodici mesi

        foreach ($vociNegozio as $row) {

            $totconto = $row['tot_conto'];

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    /**
                     * A rottura creo le colonne accumulate e inizializzo l'array
                     */
                    $totale_conto = 0;

                    for ($i = 1; $i < 13; $i++) {

                        if ($totaliMesi[$i] == 0)
                            $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
                        else {
                            if (($totaliMesi[$i] * $invSegno) < 0) {
                                $this->SetFont('', 'B', 10);
                                $this->SetTextColor(255, 0, 0);
                            }
                            $this->Cell($w[$i], 8, number_format((floatval($totaliMesi[$i] * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
                            $this->SetTextColor(0);
                            $this->SetFont('', '', 10);
                        }
                        $totale_conto = $totale_conto + $totaliMesi[$i];
                    }

                    $this->SetFont('', 'B', 10);
                    $this->Cell($w[13], 8, number_format((floatval($totale_conto * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
                    $this->SetFont('', '', 10);
                    $this->Ln();

                    for ($i = 1; $i < 13; $i++) {
                        $totaliMesi[$i] = 0;
                    }

                    $fill = !$fill;
                    $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim($row['des_conto'])), 'LR', 0, 'L', $fill);

                    $totaliMesi[$row['mm_registrazione']] += $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                } else {
                    $fill = !$fill;
                    $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim($row['des_conto'])), 'LR', 0, 'L', $fill);

                    $totaliMesi[$row['mm_registrazione']] += $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                }
                $desconto_break = trim($row['des_conto']);
            } else {
                if ($totaliMesi[$row['mm_registrazione']] > 0) {
                    $totaliMesi[$row['mm_registrazione']] += $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                }
            }
        }

        /**
         * Ultima riga
         */
        $totale_conto = 0;

        for ($i = 1; $i < 13; $i++) {

            if ($totaliMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else {
                if (($totaliMesi[$i] * $invSegno) < 0) {
                    $this->SetFont('', 'B', 10);
                    $this->SetTextColor(255, 0, 0);
                }
                $this->Cell($w[$i], 8, number_format((floatval($totaliMesi[$i] * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0);
                $this->SetFont('', '', 10);
            }

            $totale_conto = $totale_conto + $totaliMesi[$i];
        }

        $this->SetFont('', 'B', 10);
        $this->Cell($w[13], 8, number_format((floatval($totale_conto * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, "TOTALE", 'LR', 0, 'L', $fill);

        /**
         * Totali mensili finali
         */
        for ($i = 1; $i < 13; $i++) {

            if ($totaliComplessiviMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else
                $this->Cell($w[$i], 8, number_format((floatval($totaliComplessiviMesi[$i] * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);

            $totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
        }
        $this->Cell($w[13], 8, number_format((floatval($totale_anno * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function progressiviNegozioConfrontatoTable($header, $vociNegozio, $invSegno) {

        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(70, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 20);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);

        $desconto_break = "";
        $totaliMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);    // dodici mesi
        $totaliComplessiviMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); // dodici mesi
        $classe = array('', '', '', '', '', '', '', '', '', '', '', '');  // dodici mesi

        foreach ($vociNegozio as $row) {

            $totconto = $row['tot_conto'];

            if (trim($row['des_conto']) != $desconto_break) {

                if ($desconto_break != "") {

                    /**
                     * A rottura creo le colonne accumulate e inizializzo l'array
                     */
                    $totale_conto = 0;

                    for ($i = 1; $i < 13; $i++) {

                        if ($totaliMesi[$i] == 0)
                            $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
                        else {
                            if (($totaliMesi[$i] * $invSegno) < 0) {
                                $this->SetFont('', 'B', 10);
                                $this->SetTextColor(255, 0, 0);
                            }
                            $this->Cell($w[$i], 8, number_format((floatval($totaliMesi[$i] * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
                            $this->SetTextColor(0);
                            $this->SetFont('', '', 10);
                        }
                        $totale_conto = $totale_conto + $totaliMesi[$i];
                    }

                    $this->SetFont('', 'B', 10);
                    $this->Cell($w[13], 8, number_format((floatval($totale_conto * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
                    $this->SetFont('', '', 10);
                    $this->Ln();

                    for ($i = 1; $i < 13; $i++) {
                        $totaliMesi[$i] = 0;
                    }

                    $fill = !$fill;
                    $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim($row['des_conto'])), 'LR', 0, 'L', $fill);

                    $totaliMesi[$row['mm_registrazione']] += $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                } else {
                    $fill = !$fill;
                    $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', trim($row['des_conto'])), 'LR', 0, 'L', $fill);

                    $totaliMesi[$row['mm_registrazione']] += $totconto;
                    $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
                }
                $desconto_break = trim($row['des_conto']);
            } else {
                $totaliMesi[$row['mm_registrazione']] += $totconto;
                $totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
            }
        }

        /**
         * Ultima riga
         */
        $totale_conto = 0;

        for ($i = 1; $i < 13; $i++) {

            if ($totaliMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else {
                if (($totaliMesi[$i] * $invSegno) < 0) {
                    $this->SetFont('', 'B', 10);
                    $this->SetTextColor(255, 0, 0);
                }
                $this->Cell($w[$i], 8, number_format((floatval($totaliMesi[$i] * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0);
                $this->SetFont('', '', 10);
            }

            $totale_conto = $totale_conto + $totaliMesi[$i];
        }

        $this->SetFont('', 'B', 10);
        $this->Cell($w[13], 8, number_format((floatval($totale_conto * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->Cell($w[0], 8, "TOTALE", 'LR', 0, 'L', $fill);

        /**
         * Totali mensili finali
         */
        for ($i = 1; $i < 13; $i++) {

            if ($totaliComplessiviMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else
                $this->Cell($w[$i], 8, number_format((floatval($totaliComplessiviMesi[$i] * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);

            $totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
        }
        $this->Cell($w[13], 8, number_format((floatval($totale_anno * $invSegno)), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function progressiviMctTable($header, $totaliAcquistiMesi, $totaliRicaviMesi) {

        $fill = "";
        
        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(70, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 20);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);

//        $margineContribuzione = "";
        $totaleRicavi = 0;
        $totaleAcquisti = 0;
        $totaliMctAssolutoMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $totaliMctPercentualeMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $totaliMctRicaricoMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $totaleMctAssoluto = 0;
        $totaleMctPercentuale = 0;
        $totaleMctRicarico = 0;
        $classe_MctAss = array('', '', '', '', '', '', '', '', '', '', '', '');     // dodici mesi
        $classe_MctPer = array('', '', '', '', '', '', '', '', '', '', '', '');     // dodici mesi
        $classe_MctRic = array('', '', '', '', '', '', '', '', '', '', '', '');     // dodici mesi
        $classe_tot_MctAss = "";
        $classe_tot_MctPer = "";
        $classe_tot_MctRic = "";

        /**
         * Faccio i totali di linea annuali per Acquisti e Ricavi
         */
        for ($i = 1; $i < 13; $i++) {
            $totaleRicavi = $totaleRicavi + $totaliRicaviMesi[$i];
            $totaleAcquisti = $totaleAcquisti + $totaliAcquistiMesi[$i];
        }

        /**
         * Calcolo gli MCT per ciascun mese
         */
        for ($i = 1; $i < 13; $i++) {

            $totaliMctAssolutoMesi[$i] = abs($totaliRicaviMesi[$i]) - $totaliAcquistiMesi[$i];
            if ($totaliMctAssolutoMesi[$i] < 0)
                $classe_MctAss[$i] = "ko";

            /**
             * Se il ricavo è zero non faccio la divisione
             */
            if ($totaliRicaviMesi[$i] != 0) {
                $totaliMctPercentualeMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100 ) / abs($totaliRicaviMesi[$i]);
            } else {
                $totaliMctPercentualeMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100 );
            }

            if ($totaliMctPercentualeMesi[$i] < 0)
                $classe_MctPer[$i] = "ko";

            /**
             * Se il totale acquisti è zero non faccio la divisione
             */
            if ($totaliAcquistiMesi[$i] != 0) {
                $totaliMctRicaricoMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100) / abs($totaliAcquistiMesi[$i]);
            } else {
                $totaliMctRicaricoMesi[$i] = ($totaliMctAssolutoMesi[$i] * 100);
            }

            if ($totaliMctRicaricoMesi[$i] < 0)
                $classe_MctRic[$i] = "ko";
        }

        /**
         * Faccio il totale di linea annuale per il margine assoluto
         */
        for ($i = 1; $i < 13; $i++) {
            $totaleMctAssoluto = $totaleMctAssoluto + $totaliMctAssolutoMesi[$i];
        }
        if ($totaleMctAssoluto < 0)
            $classe_tot_MctAss = "ko";

        /**
         * Calcolo i margini sui totali annuali
         */
        $totaleMctPercentuale = ($totaleMctAssoluto * 100 ) / abs($totaleRicavi);
        if ($totaleMctPercentuale < 0)
            $classe_tot_MctPer = "ko";

        $totaleMctRicarico = ($totaleMctAssoluto * 100) / abs($totaleAcquisti);
        if ($totaleMctRicarico < 0)
            $classe_tot_MctRic = "ko";

        /**
         * Genero le righe del documento
         */
        $fill = !$fill;
        $this->SetFont('', '', 10);
        $this->Cell($w[0], 8, "Fatturato", 'LR', 0, 'L', $fill);
        for ($i = 1; $i < 13; $i++) {
            if ($totaliRicaviMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else
                $this->Cell($w[$i], 8, number_format(abs(floatval($totaliRicaviMesi[$i])), 0, ',', '.'), 'LR', 0, 'R', $fill);
        }
        $this->SetFont('', 'B', 10);
        $this->Cell($w[$i], 8, number_format(abs(floatval($totaleRicavi)), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->SetFont('', '', 10);
        $this->Cell($w[0], 8, "Acquisti", 'LR', 0, 'L', $fill);
        for ($i = 1; $i < 13; $i++) {
            if ($totaliAcquistiMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else
                $this->Cell($w[$i], 8, number_format(abs(floatval($totaliAcquistiMesi[$i])), 0, ',', '.'), 'LR', 0, 'R', $fill);
        }
        $this->SetFont('', 'B', 10);
        $this->Cell($w[$i], 8, number_format(abs(floatval($totaleAcquisti)), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->SetFont('', '', 10);
        $this->SetTextColor(0);
        $this->Cell($w[0], 8, "Margine assoluto", 'LR', 0, 'L', $fill);
        for ($i = 1; $i < 13; $i++) {
            if ($totaliMctAssolutoMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else {
                if ($classe_MctAss[$i] == "ko") {
                    $this->SetFont('', 'B', 10);
                    $this->SetTextColor(255, 0, 0);
                }

                $this->Cell($w[$i], 8, number_format(floatval($totaliMctAssolutoMesi[$i]), 0, ',', '.'), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0);
                $this->SetFont('', '', 10);
            }
        }
        $this->SetFont('', 'B', 10);
        if ($classe_tot_MctAss == "ko") {
            $this->SetTextColor(255, 0, 0);
        }
        $this->Cell($w[$i], 8, number_format(floatval($totaleMctAssoluto), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);
        $this->Cell($w[0], 8, "Margine percentuale", 'LR', 0, 'L', $fill);
        for ($i = 1; $i < 13; $i++) {
            if ($totaliMctPercentualeMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else {
                if ($classe_MctPer[$i] == "ko") {
                    $this->SetFont('', 'B', 10);
                    $this->SetTextColor(255, 0, 0);
                }

                $this->Cell($w[$i], 8, number_format(floatval($totaliMctPercentualeMesi[$i]), 0, ',', '.'), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0);
                $this->SetFont('', '', 10);
            }
        }
        $this->SetFont('', 'B', 10);
        if ($classe_tot_MctPer == "ko") {
            $this->SetTextColor(255, 0, 0);
        }
        $this->Cell($w[$i], 8, number_format(floatval($totaleMctPercentuale), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $fill = !$fill;
        $this->SetFont('', '', 10);
        $this->SetTextColor(0);
        $this->Cell($w[0], 8, "Ricarico percentuale", 'LR', 0, 'L', $fill);
        for ($i = 1; $i < 13; $i++) {
            if ($totaliMctRicaricoMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else {
                if ($classe_MctRic[$i] == "ko") {
                    $this->SetFont('', 'B', 10);
                    $this->SetTextColor(255, 0, 0);
                }
                $this->Cell($w[$i], 8, number_format(floatval($totaliMctRicaricoMesi[$i]), 0, ',', '.'), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0);
                $this->SetFont('', '', 10);
            }
        }
        $this->SetFont('', 'B', 10);
        if ($classe_tot_MctRic == "ko") {
            $this->SetTextColor(255, 0, 0);
        }
        $this->Cell($w[$i], 8, number_format(floatval($totaleMctRicarico), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function progressiviUtilePerditaTable($header, $totaliAcquistiMesi, $totaliRicaviMesi) {

        $fill = "";
        
        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', '', 12);

        // Header
        $w = array(70, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 20);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);

//        $utilePerdita = "";
//        $totaleRicavi = 0;
//        $totaleAcquisti = 0;
        $totaleUtilePerdita = 0;
        $utilePerditaMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);     // dodici mesi
        $classe = array('', '', '', '', '', '', '', '', '', '', '', '');     // dodici mesi
        $progrUtilePerditaMesi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);        // dodici mesi
        $progrClasse = array('', '', '', '', '', '', '', '', '', '', '', '');         // dodici mesi

        /**
         * Calcolo l'utile o la perdita per ciascun mese
         */
        for ($i = 1; $i < 13; $i++) {
            $utilePerditaMesi[$i] = abs($totaliRicaviMesi[$i]) - $totaliAcquistiMesi[$i];
            if ($utilePerditaMesi[$i] < 0)
                $classe[$i] = "ko";
            $totaleUtilePerdita = $totaleUtilePerdita + $utilePerditaMesi[$i];

            for ($j = $i; $j < 13; $j++) {
                $progrUtilePerditaMesi[$j] += $utilePerditaMesi[$i];
                if ($progrUtilePerditaMesi[$j] < 0)
                    $progrClasse[$j] = "ko";
                else
                    $progrClasse[$j] = "";
            }
        }

        /**
         * Genero le righe del documento
         */
        $fill = !$fill;
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', "Diff. Ricavi - Costi MESE"), 'LR', 0, 'L', $fill);
        for ($i = 1; $i < 13; $i++) {
            if ($utilePerditaMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else {
                if ($classe[$i] == 'ko') {
                    $this->SetTextColor(255, 0, 0);
                    $this->SetFont('', 'B', 10);
                }

                $this->Cell($w[$i], 8, number_format(floatval($utilePerditaMesi[$i]), 0, ',', '.'), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0);
                $this->SetFont('', '', 10);
            }
        }
        $this->SetFont('', 'B', 10);

        if ($totaleUtilePerdita < 0) {
            $this->SetTextColor(255, 0, 0);
        }

        $this->Cell($w[$i], 8, number_format(floatval($totaleUtilePerdita), 0, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();

        // Seconda riga : progressivo Ricavi-Costi

        $fill = !$fill;
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);
        $this->Cell($w[0], 8, iconv('UTF-8', 'windows-1252', "Diff. Ricavi - Costi PROGRESSIVO"), 'LR', 0, 'L', $fill);
        for ($i = 1; $i < 13; $i++) {
            if ($progrUtilePerditaMesi[$i] == 0)
                $this->Cell($w[$i], 8, "---", 'LR', 0, 'R', $fill);
            else {
                if ($progrClasse[$i] == 'ko') {
                    $this->SetTextColor(255, 0, 0);
                    $this->SetFont('', 'B', 10);
                }

                $this->Cell($w[$i], 8, number_format(floatval($progrUtilePerditaMesi[$i]), 0, ',', '.'), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0);
                $this->SetFont('', '', 10);
            }
        }
        $this->SetFont('', 'B', 10);

        $this->Cell($w[$i], 8, "", 'LR', 0, 'R', $fill);
        $this->Ln();


        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function ScadenzeFornitoriTable($header, $data) {

        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B', 10);

        // Header
        $w = array(20, 80, 90, 25, 25, 25);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->SetFont('', '', 8);

        // Data

        $idfornitore_break = "";
        $datscadenza_break = "";
        $totale_fornitore = 0;
        $totale_scadenze = 0;

        $fill = false;
        foreach ($data as $row) {

            if (($idfornitore_break == "") && ($datscadenza_break == "")) {
                $idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
                $datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                $desfornitore = trim($row[Fornitore::DES_FORNITORE]);
                $datscadenza = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
            }

            if ((trim($row[ScadenzaFornitore::ID_FORNITORE]) != $idfornitore_break) | (trim($row[ScadenzaFornitore::DAT_SCADENZA]) != $datscadenza_break)) {

                $this->SetFont('', 'B', 12);
                $this->Cell($w[0], 10, '', 'LR', 0, 'L', $fill);
                $this->Cell($w[1], 10, '', 'LR', 0, 'L', $fill);
                $this->Cell($w[2], 10, 'Totale', 'LR', 0, 'R', $fill);
                $this->Cell($w[3], 10, '', 'LR', 0, 'L', $fill);
                $this->Cell($w[4], 10, '', 'LR', 0, 'C', $fill);
                $this->Cell($w[5], 10, EURO . number_format(floatval($totale_fornitore), 2, ',', '.'), 'LR', 0, 'R', $fill);
                $this->Ln();
                $fill = !$fill;

                $desfornitore = trim($row[Fornitore::DES_FORNITORE]);
                $datscadenza = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                $idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
                $datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);

                $totale_scadenze += $totale_fornitore;
                $totale_fornitore = 0;
            }

            if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_APERTA) {
                $stascadenza = self::SCADENZA_DA_PAGARE;
                $c1 = "0";
                $c2 = "0";
                $c3 = "0";
            }
            if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
                $stascadenza = self::SCADENZA_PAGATA;
                $c1 = "0";
                $c2 = "0";
                $c3 = "0";
            }
            if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
                $stascadenza = self::SCADENZA_POSTICIPATA;
                $c1 = "51";
                $c2 = "153";
                $c3 = "255";
            }

            $this->SetFont('', '', 10);
            $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $datscadenza), 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, iconv('UTF-8', 'windows-1252', $desfornitore), 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, iconv('UTF-8', 'windows-1252', $row[ScadenzaFornitore::NOTA_SCADENZA]), 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, iconv('UTF-8', 'windows-1252', $row[ScadenzaFornitore::TIP_ADDEBITO]), 'LR', 0, 'C', $fill);
            $this->SetFont('', '', 10);
            $this->SetTextColor($c1, $c2, $c3);
            $this->Cell($w[4], 6, iconv('UTF-8', 'windows-1252', $stascadenza), 'LR', 0, 'C', $fill);
            $this->Cell($w[5], 6, EURO . number_format(floatval($row[ScadenzaFornitore::IMP_IN_SCADENZA]), 2, ',', '.'), 'LR', 0, 'R', $fill);
            $this->SetFont('', 'B', 8);
            $this->SetTextColor(0);
            $this->Ln();
            $fill = !$fill;

            $desfornitore = "";
            $datscadenza = "";
            $totale_fornitore += trim($row[ScadenzaFornitore::IMP_IN_SCADENZA]);
        }

        $this->SetFont('', 'B', 12);
        $this->Cell($w[0], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[2], 10, 'Totale', 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[4], 10, '', 'LR', 0, 'C', $fill);
        $this->Cell($w[5], 10, EURO . number_format(floatval($totale_fornitore), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();
        $fill = !$fill;

        $totale_scadenze += $totale_fornitore;

        $this->SetFont('', 'B', 12);
        $this->Cell($w[0], 15, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 15, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[2], 15, 'Totale Scadenze', 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 15, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[4], 15, '', 'LR', 0, 'C', $fill);
        $this->Cell($w[5], 15, EURO . number_format(floatval($totale_scadenze), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();
        $fill = !$fill;

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    public function ScadenzeClientiTable($header, $data) {

        // Colors, line width and bold font
        $this->SetFillColor(28, 148, 196);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B', 10);

        // Header
        $w = array(20, 80, 90, 25, 25, 25);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->SetFont('', '', 8);

        // Data

        $idcliente_break = "";
        $datregistrazione_break = "";
        $totale_cliente = 0;
        $totale_scadenze = 0;

        $fill = false;

        foreach ($data as $row) {

            if (($idcliente_break == "") && ($datregistrazione_break == "")) {
                $idcliente_break = trim($row[ScadenzaCliente::ID_CLIENTE]);
                $datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
                $descliente = trim($row[Cliente::DES_CLIENTE]);
                $datregistrazione = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
            }

            if ((trim($row[ScadenzaCliente::ID_CLIENTE]) != $idcliente_break) |
                    (trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]) != $datregistrazione_break)) {

                $this->SetFont('', 'B', 12);
                $this->Cell($w[0], 10, '', 'LR', 0, 'L', $fill);
                $this->Cell($w[1], 10, '', 'LR', 0, 'L', $fill);
                $this->Cell($w[2], 10, 'Totale', 'LR', 0, 'R', $fill);
                $this->Cell($w[3], 10, '', 'LR', 0, 'L', $fill);
                $this->Cell($w[4], 10, '', 'LR', 0, 'C', $fill);
                $this->Cell($w[5], 10, EURO . number_format(floatval($totale_cliente), 2, ',', '.'), 'LR', 0, 'R', $fill);
                $this->Ln();
                $fill = !$fill;

                $descliente = trim($row[Cliente::DES_CLIENTE]);
                $datregistrazione = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
                $idcliente_break = trim($row[ScadenzaCliente::ID_CLIENTE]);
                $datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);

                $totale_scadenze += $totale_cliente;
                $totale_cliente = 0;
            }

            if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_APERTA) {
                $stascadenza = self::SCADENZA_DA_INCASSARE;
                $c1 = "0";
                $c2 = "0";
                $c3 = "0";
            }
            if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
                $stascadenza = self::SCADENZA_INCASSATA;
                $c1 = "0";
                $c2 = "0";
                $c3 = "0";
            }
            if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
                $stascadenza = self::SCADENZA_POSTICIPATA;
                $c1 = "51";
                $c2 = "153";
                $c3 = "255";
            }

            $this->SetFont('', '', 10);
            $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $datregistrazione), 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, iconv('UTF-8', 'windows-1252', $descliente), 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, iconv('UTF-8', 'windows-1252', $row[ScadenzaCliente::NOTA]), 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, iconv('UTF-8', 'windows-1252', $row[ScadenzaCliente::TIP_ADDEBITO]), 'LR', 0, 'C', $fill);
            $this->SetFont('', '', 10);
            $this->SetTextColor($c1, $c2, $c3);
            $this->Cell($w[4], 6, iconv('UTF-8', 'windows-1252', $stascadenza), 'LR', 0, 'C', $fill);
            $this->Cell($w[5], 6, EURO . number_format(floatval($row[ScadenzaCliente::IMP_REGISTRAZIONE]), 2, ',', '.'), 'LR', 0, 'R', $fill);
            $this->SetFont('', 'B', 8);
            $this->SetTextColor(0);
            $this->Ln();
            $fill = !$fill;

            $descliente = "";
            $datregistrazione = "";
            $totale_cliente += trim($row[ScadenzaCliente::IMP_REGISTRAZIONE]);
        }

        $this->SetFont('', 'B', 12);
        $this->Cell($w[0], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[2], 10, 'Totale', 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[4], 10, '', 'LR', 0, 'C', $fill);
        $this->Cell($w[5], 10, EURO . number_format(floatval($totale_cliente), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();
        $fill = !$fill;

        $totale_scadenze += $totale_cliente;

        $this->SetFont('', 'B', 12);
        $this->Cell($w[0], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[2], 10, 'Totale Incassi', 'LR', 0, 'R', $fill);
        $this->Cell($w[3], 10, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[4], 10, '', 'LR', 0, 'C', $fill);
        $this->Cell($w[5], 10, EURO . number_format(floatval($totale_scadenze), 2, ',', '.'), 'LR', 0, 'R', $fill);
        $this->Ln();
        $fill = !$fill;

        $this->Cell(array_sum($w), 0, '', 'T');
    }

    // Getters & Setters

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title, $isUTF8=false) {
        $this->title = $title;
    }

    public function getTitle1() {
        return $this->title1;
    }

    public function setTitle1($title1) {
        $this->title1 = $title1;
    }

    public function getTitle2() {
        return $this->title2;
    }

    public function setTitle2($title2) {
        $this->title2 = $title2;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function setCreator($creator, $isUTF8=false) {
        $this->creator = $creator;
    }

    public function getLogo() {
        return $this->logo;
    }

    public function setLogo($logo) {
        $this->logo = $logo;
    }

}