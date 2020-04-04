<?php

require_once 'fatturaBase.class.php';
require_once 'fatture.business.interface.php';

class FatturaAziendaConsortile extends FatturaBase implements FattureBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {

        if (parent::getIndexSession(self::FATTURA_AZIENDA_CONSORTILE) === NULL) {
            parent::setIndexSession(self::FATTURA_AZIENDA_CONSORTILE, serialize(new FatturaAziendaConsortile()));
        }
        return unserialize(parent::getIndexSession(self::FATTURA_AZIENDA_CONSORTILE));
        }

    public function identificativiFatturaAziendaConsortile($giorno, $meserif, $anno, $numfat, $codneg) {

        $negozio = ($codneg == self::VILLA) ? self::NEGOZIO_VILLA : $negozio;
        $negozio = ($codneg == self::TREZZO) ? self::NEGOZIO_TREZZO : $negozio;
        $negozio = ($codneg == self::BREMBATE) ? self::NEGOZIO_BREMBATE : $negozio;

        $prefisso = ($codneg == self::VILLA) ? "" : $prefisso;
        $prefisso = ($codneg == self::TREZZO) ? "T" : $prefisso;
        $prefisso = ($codneg == self::BREMBATE) ? "B" : $prefisso;

        $nfat = str_pad($numfat, 2, "0", STR_PAD_LEFT);

        $r1 = 10;
        $r2 = $r1 + 192;
        $y1 = 94;
        $y2 = $y1 + 10;
        $mid = $y1 + (($y2 - $y1) / 2);
        $this->SetFillColor(189, 229, 244);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2 - $y1), 2.5, 'DF');
        $this->SetXY($r1 + 5, $y1 + 3);
        $this->SetFont("Arial", "B", 10);
        $this->Cell(10, 4, "REG. SEZ. 1PA" . str_repeat(" ", 48) . $negozio . "     Fattura N. :  " . $nfat . $prefisso . "PA/" . $anno . "   del  " . $giorno . " " . $meserif . " " . $anno, 0, 0, "");
    }

    public function aggiungiLineaLiberaAziendaConsortile($w, $linea) {

        $this->SetX(15);
        $this->SetFont("Arial", "", 12);

        $articolo = explode("\\", $linea["ARTICOLO"]);

        $this->Cell($w[0], 6, "N. " . $linea["QUANTITA"], "");
        $this->Cell($w[1], 6, iconv('UTF-8', 'windows-1252', $articolo[0]), "");
        $this->Cell($w[2], 6, EURO, "", 0, 'R');
        $this->Cell($w[3], 6, number_format(floatval($linea["TOTALE"]), 2, ',', '.'), "", 0, 'R');
        $this->Ln();

        for ($i = 1; $i < count($articolo); $i++) {
            $this->SetX(30);
            $this->Cell($w[1], 6, iconv('UTF-8', 'windows-1252', $articolo[$i]), "", 0, "L");
            $this->Ln();
        }
    }

    public function totaliFatturaAziendaConsortile($tot_dettagli, $tot_imponibile, $tot_iva) {

        $this->SetFont( "Arial", "B", 10);
        $r1  = 10;
        $r2  = $r1 + 192;
        $y1  = 260;
        $y2  = $y1+15;
        $this->SetFillColor(230, 230, 230);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
        $this->Line( $r1, $y1+6, $r2, $y1+6);
        $this->Line( $r1+25, $y1, $r1+25, $y2);  // davanti all' IVA
        $this->Line( $r1+50, $y1, $r1+50, $y2);  // davanti al totale
        $this->Line( $r1+75, $y1, $r1+75, $y2);  // davanti all'IVA a Vs. carico
        $this->Line( $r1+150, $y1, $r1+150, $y2);  // davanti all'IVA a Vs. carico

        $this->SetXY( $r1+3, $y1);
        $this->Cell(20,6, "IMPONIBILE","",0,"C");
        $this->SetX( $r1+30 );
        $this->Cell(20,6, "IVA","",0,"C");
        $this->SetX( $r1+50 );
        $this->Cell(20,6, "TOTALE","",0,"C");
        $this->SetX( $r1+92 );
        $this->Cell(40,6, "IVA Vs. carico ex art. 17-ter, DPR n.633/72","",0,"C");
        $this->SetX( $r1+159 );
        $this->Cell(20,6, "NETTO A PAGARE","",0,"C");

        $this->SetXY( $r1+3, $y1+7);
        $this->Cell(20,6,number_format(floatval($tot_imponibile), 2, ',', '.'),"",0,"C");
        $this->SetXY( $r1+30, $y1+7);
        $this->Cell(20,6,number_format(floatval($tot_iva), 2, ',', '.'),"",0,"C");
        $this->SetXY( $r1+50, $y1+7);
        $this->Cell(20,6,number_format(floatval($tot_dettagli), 2, ',', '.'),"",0,"C");
        $this->SetX( $r1+90 );
        $this->Cell(40,6,"-" . number_format(floatval($tot_iva), 2, ',', '.'),"",0,"C");
        $this->SetXY( $r1+159, $y1+7);
        $this->Cell(20,6,EURO . " " . number_format(floatval($tot_imponibile), 2, ',', '.'),"",0,"C");   
    }

    public function go() {

    }

    public function start() {

    }

}
?>

