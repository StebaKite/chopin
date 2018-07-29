<?php

require_once 'fatturaBase.class.php';
require_once 'fatture.business.interface.php';

class FatturaEntePubblico extends FatturaBase implements FattureBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {

        if (!isset($_SESSION[self::FATTURA_ENTE_PUBBLICO]))
            $_SESSION[self::FATTURA_ENTE_PUBBLICO] = serialize(new FatturaEntePubblico());
        return unserialize($_SESSION[self::FATTURA_ENTE_PUBBLICO]);
    }

    public function identificativiFatturaEntePubblico($giorno, $meserif, $anno, $numfat, $codneg) {

        $negozio = ($codneg == self::VILLA) ? self::NEGOZIO_VILLA : $negozio;
        $negozio = ($codneg == self::TREZZO) ? self::NEGOZIO_TREZZO : $negozio;
        $negozio = ($codneg == self::BREMBATE) ? self::NEGOZIO_BREMBATE : $negozio;

        /**
         * Eccezione:
         * - le fatture i Brembate hanno una B dopo il progressivo
         * - le fatture di Trezzo hanno una T dopo il progressivo
         */
        $nfat = str_pad($numfat, 2, "0", STR_PAD_LEFT);
        $nfat = ($codneg == self::BREMBATE) ? str_pad($numfat, 2, "0", STR_PAD_LEFT) . "B" : $nfat;
        $nfat = ($codneg == self::TREZZO) ? str_pad($numfat, 2, "0", STR_PAD_LEFT) . "T" : $nfat;

        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 94;
        $y2 = $y1 + 10;
        $mid = $y1 + (($y2 - $y1) / 2);
        $this->SetFillColor(189, 229, 244);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2 - $y1), 2.5, 'DF');
        $this->SetXY($r1 + 5, $y1 + 3);
        $this->SetFont("Arial", "B", 10);
        $this->Cell(10, 4, "REG. SEZ. 1" . str_repeat(" ", 48) . $negozio . "     Fattura N. :  " . $nfat . $fatneg . "/" . $anno . "   del  " . $giorno . " " . $meserif . " " . $anno, 0, 0, "");
    }

    public function aggiungiLineaLiberaEntePubblico($w, $linea, $r1, $y1) {

        $this->SetXY($r1, $y1);
        $this->SetFont("Arial", "", 12);

        $articolo = explode("\\", $linea["ARTICOLO"]);

        $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $articolo[0]), "");
        $this->Cell($w[1], 6, EURO, "", 0, 'R');
        $this->Cell($w[2], 6, number_format($linea["TOTALE"], 2, ',', '.'), "", 0, 'R');
        $this->Ln();

        for ($i = 1; $i < count($articolo); $i++) {
            $this->SetX($r1);
            $this->Cell($w[0], 6, iconv('UTF-8', 'windows-1252', $articolo[$i]), "", 0, "L");
            $this->Ln();
        }
    }

    public function totaliFatturaContributoEntePubblico($tot_dettagli, $tot_imponibile, $tot_iva, $aliq_iva) {

        $this->SetFont("Arial", "B", 10);
        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 260;
        $y2 = $y1 + 15;
        $this->SetFillColor(230, 230, 230);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2 - $y1), 2.5, 'DF');
        $this->Line($r1, $y1 + 6, $r2, $y1 + 6);
        $this->Line($r1 + 25, $y1, $r1 + 25, $y2);  // davanti all' IVA
        $this->Line($r1 + 50, $y1, $r1 + 50, $y2);  // davanti al totale
        $this->Line($r1 + 75, $y1, $r1 + 75, $y2);  // davanti all'IVA a Vs. carico
        $this->Line($r1 + 150, $y1, $r1 + 150, $y2);  // davanti all'IVA a Vs. carico

        $this->SetXY($r1 + 3, $y1);
        $this->Cell(20, 6, "IMPONIBILE", "", 0, "C");
        $this->SetX($r1 + 30);
        $this->Cell(20, 6, "IVA " . $aliq_iva . "%", "", 0, "C");
        $this->SetX($r1 + 50);
        $this->Cell(20, 6, "TOTALE", "", 0, "C");
        $this->SetX($r1 + 92);
        $this->Cell(40, 6, "IVA Vs. carico ex art. 17-ter, DPR n.633/72", "", 0, "C");
        $this->SetX($r1 + 159);
        $this->Cell(20, 6, "NETTO A PAGARE", "", 0, "C");

        $this->SetXY($r1 + 3, $y1 + 7);
        $this->Cell(20, 6, number_format($tot_imponibile, 2, ',', '.'), "", 0, "C");
        $this->SetXY($r1 + 30, $y1 + 7);
        $this->Cell(20, 6, number_format($tot_iva, 2, ',', '.'), "", 0, "C");
        $this->SetXY($r1 + 50, $y1 + 7);
        $this->Cell(20, 6, number_format($tot_dettagli, 2, ',', '.'), "", 0, "C");
        $this->SetX($r1 + 90);
        $this->Cell(40, 6, "-" . number_format($tot_iva, 2, ',', '.'), "", 0, "C");
        $this->SetXY($r1 + 159, $y1 + 7);
        $this->Cell(20, 6, EURO . " " . number_format($tot_imponibile, 2, ',', '.'), "", 0, "C");
    }

    public function totaliFatturaVenditaEntePubblico($tot_dettagli, $tot_imponibile, $tot_iva, $aliq_iva) {

        /**
         * Box riepilogo imponibili e aliquote IVA
         */
        $r1 = 10;
        $r2 = $r1 + 100;
        $y1 = 250;
        $y2 = $y1 + 25;
        $this->SetFillColor(230, 230, 230);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2 - $y1), 2.5, 'DF');
        $this->Line($r1, $y1 + 6, $r2, $y1 + 6);
        $this->Line($r1 + 15, $y1, $r1 + 15, $y2);  // davanti alla descrizione
        $this->Line($r1 + 50, $y1, $r1 + 50, $y2);  // davanti all'imponibile
        $this->Line($r1 + 75, $y1, $r1 + 75, $y2);  // davanti all'imposta

        $this->SetFont("Arial", "B", 10);

        $this->SetXY($r1 + 2, $y1);
        $this->Cell(10, 6, "IVA");
        $this->SetX($r1 + 20);
        $this->Cell(10, 6, "DESCRIZIONE");
        $this->SetX($r1 + 52);
        $this->Cell(10, 6, "IMPONIBILE");
        $this->SetX($r1 + 80);
        $this->Cell(10, 6, "IMPOSTA");

        $this->SetFont("Arial", "", 10);

        if ($tot_imponibile > 0) {
            $this->SetXY($r1 + 2, $y1 + 7);
            $this->Cell(10, 6, $aliq_iva . "%", "", 0, "C");
            $this->SetX($r1 + 20);
            $this->Cell(10, 6, "", "", 0, "L");
            $this->SetX($r1 + 52);
            $this->Cell(22, 6, number_format($tot_imponibile, 2, ',', '.'), "", 0, "R");
            $this->SetX($r1 + 80);
            $this->Cell(18, 6, number_format($tot_iva, 2, ',', '.'), "", 0, "R");
        }

        /**
         * Box riepilogo Totale imponibile e iva
         */
        $r1 = 112;
        $r2 = $r1 + 40;
        $y1 = 250;
        $y2 = $y1 + 25;
        $this->SetFillColor(230, 230, 230);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2 - $y1), 2.5, 'DF');
        $mid = $y1 + (($y2 - $y1) / 2);
        $this->Line($r1, $mid, $r2, $mid);

        $this->SetFont("Arial", "B", 10);

        $this->SetXY($r1 + 28, $y1);
        $this->Cell(10, 6, "Tot. Imponibile", "", 0, "R");
        $this->SetXY($r1 + 28, $y1 + 12);
        $this->Cell(10, 6, "Tot. Imposta", "", 0, "R");

        $imponibile = $tot_imponibile;
        $iva = $tot_iva;

        $this->SetFont("Arial", "", 10);

        $this->SetXY($r1 + 28, $y1 + 6);
        $this->Cell(10, 6, number_format($imponibile, 2, ',', '.'), "", 0, "R");

        $this->SetXY($r1 + 28, $y1 + 18);
        $this->Cell(10, 6, number_format($iva, 2, ',', '.'), "", 0, "R");

        /**
         * Box totale documento
         */
        $r1 = 154;
        $r2 = $r1 + 48;
        $y1 = 250;
        $y2 = $y1 + 25;
        $this->SetFillColor(230, 230, 230);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2 - $y1), 2.5, 'DF');

        $this->SetFont("Arial", "B", 10);

        $this->SetXY($r1 + 34, $y1);
        $this->Cell(10, 6, "TOTALE", "", 0, "R");

        $netto = $imponibile + $iva;

        $this->SetFont("Arial", "B", 14);

        $this->SetXY($r1 + 34, $y1 + 10);
        $this->Cell(10, 6, EURO . " " . number_format($netto, 2, ',', '.'), "", 0, "R");
    }

    public function go() {

    }

    public function start() {

    }

}

?>
