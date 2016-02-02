<?php

require_once 'fattura.class.php';

class FatturaAziendaConsortile extends Fattura {

	private static $_instance = null;
	
	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new FatturaAziendaConsortile();
	
		return self::$_instance;
	}

	public function identificativiFatturaAziendaConsortile($giorno, $meserif, $anno, $numfat, $codneg) {
	
		$negozio = ($codneg == "VIL") ? "Villa d'Adda" : $negozio;
		$negozio = ($codneg == "TRE") ? "Trezzo" : $negozio;
		$negozio = ($codneg == "BRE") ? "Brembate" : $negozio;
	
		$nfat = str_pad($numfat, 2, "0", STR_PAD_LEFT);
	
		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 94;
		$y2  = $y1+10;
		$mid = $y1 + (($y2-$y1) / 2);
		$this->SetFillColor(189, 229, 244);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
		$this->SetXY( $r1 + 5, $y1 + 3 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,4, "REG. SEZ. 1PA" . str_repeat(" ",48) . $negozio . "     Fattura N. :  " . $nfat . "PA/" . $anno . "   del  " . $giorno . " " . $meserif . " " . $anno, 0, 0, "");
	}

	public function aggiungiLineaLiberaAziendaConsortile($w, $linea) {
	
		$this->SetX( 15 );
		$this->SetFont( "Arial", "", 10);
	
		$articolo = explode("\\", $linea["ARTICOLO"]);
	
		$this->Cell($w[0],6,"N. " . $linea["QUANTITA"],"");
		$this->Cell($w[1],6,utf8_decode($articolo[0]) . " da " . EURO . " " . $linea["IMPORTO U."] ,"");
		$this->Cell($w[2],6,EURO,"",0,'R');
		$this->Cell($w[3],6,number_format($linea["TOTALE"], 2, ',', '.'),"",0,'R');
		$this->Ln();
	
		for($i=1;$i<count($articolo);$i++) {
			$this->Cell($w[1],6,utf8_decode($articolo[$i]),"",0,"L");
			$this->Ln();
		}
	}

	public function totaliFatturaAziendaConsortile($tot_dettagli, $tot_imponibile, $tot_iva) {
	
		/**
		 * Box riepilogo imponibili e aliquote IVA
		 */
		$r1  = 10;
		$r2  = $r1 + 100;
		$y1  = 250;
		$y2  = $y1+25;
		$this->SetFillColor(230, 230, 230);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
		$this->Line( $r1, $y1+6, $r2, $y1+6);
		$this->Line( $r1+15, $y1, $r1+15, $y2);  // davanti alla descrizione
		$this->Line( $r1+50, $y1, $r1+50, $y2);  // davanti all'imponibile
		$this->Line( $r1+75, $y1, $r1+75, $y2);  // davanti all'imposta
	
		$this->SetFont( "Arial", "B", 10);
	
		$this->SetXY( $r1+2, $y1);
		$this->Cell(10,6, "C.IVA");
		$this->SetX( $r1+20 );
		$this->Cell(10,6, "DESCRIZIONE");
		$this->SetX( $r1+52 );
		$this->Cell(10,6, "IMPONIBILE");
		$this->SetX( $r1+80 );
		$this->Cell(10,6, "IMPOSTA");
	
		$this->SetFont( "Arial", "", 10);
	
		if ($tot_imponibile > 0) {
			$this->SetXY( $r1+2, $y1+7);
			$this->Cell(10,6,"5","",0,"C");
			$this->SetX( $r1+20 );
			$this->Cell(10,6,"Iva 5%","",0,"L");
			$this->SetX( $r1+52 );
			$this->Cell(22,6,number_format($tot_imponibile, 2, ',', '.'),"",0,"R");
			$this->SetX( $r1+80 );
			$this->Cell(18,6,number_format($tot_iva, 2, ',', '.'),"",0,"R");
		}
	
		/**
		 * Box riepilogo Totale imponibile e iva
		 */
		$r1  = 112;
		$r2  = $r1 + 40;
		$y1  = 250;
		$y2  = $y1+25;
		$this->SetFillColor(230, 230, 230);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
		$mid = $y1 + (($y2-$y1) / 2);
		$this->Line( $r1, $mid, $r2, $mid);
	
		$this->SetFont( "Arial", "B", 10);
	
		$this->SetXY( $r1+28, $y1);
		$this->Cell(10,6, "Tot. Imponibile","",0,"R");
		$this->SetXY( $r1+28, $y1+12);
		$this->Cell(10,6, "Tot. Imposta","",0,"R");
	
		$imponibile = $tot_imponibile;
		$iva = $tot_iva;
	
		$this->SetFont( "Arial", "", 10);
	
		$this->SetXY( $r1+28, $y1+6);
		$this->Cell(10,6,number_format($imponibile, 2, ',', '.'),"",0,"R");
	
		$this->SetXY( $r1+28, $y1+18);
		$this->Cell(10,6,number_format($iva, 2, ',', '.'),"",0,"R");
	
		/**
		 * Box totale documento
		*/
		$r1  = 154;
		$r2  = $r1 + 48;
		$y1  = 250;
		$y2  = $y1+25;
		$this->SetFillColor(230, 230, 230);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
	
		$this->SetFont( "Arial", "B", 10);
	
		$this->SetXY( $r1+34, $y1);
		$this->Cell(10,6, "TOTALE","",0,"R");
	
		$netto = $imponibile + $iva;
	
		$this->SetFont( "Arial", "B", 14);
	
		$this->SetXY( $r1+34, $y1+10);
		$this->Cell(10,6,EURO . " " . number_format($netto, 2, ',', '.'),"",0,"R");
	}
	
// 	public function totaliFatturaAziendaConsortile($tot_dettagli, $tot_imponibile, $tot_iva) {
	
// 		$this->SetFont( "Arial", "B", 10);
// 		$r1  = 10;
// 		$r2  = $r1 + 192;
// 		$y1  = 260;
// 		$y2  = $y1+15;
// 		$this->SetFillColor(230, 230, 230);
// 		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
// 		$this->Line( $r1, $y1+6, $r2, $y1+6);
// 		$this->Line( $r1+50, $y1, $r1+50, $y2);  // davanti all' IVA
// 		$this->Line( $r1+100, $y1, $r1+100, $y2);  // davanti al totale
	
// 		$this->SetXY( $r1+16, $y1);
// 		$this->Cell(10,6, "IMPONIBILE");
// 		$this->SetX( $r1+71 );
// 		$this->Cell(10,6, "IVA 4%");
// 		$this->SetX( $r1+169 );
// 		$this->Cell(10,6, "TOTALE");
	
// 		$this->SetXY( $r1+16, $y1+7);
// 		$this->Cell(10,6,number_format($tot_imponibile, 2, ',', '.'));
// 		$this->SetXY( $r1+71, $y1+7);
// 		$this->Cell(10,6,number_format($tot_iva, 2, ',', '.'));
// 		$this->SetXY( $r1+169, $y1+7);
// 		$this->Cell(10,6,EURO . " " . number_format($tot_dettagli, 2, ',', '.'));
// 	}
}

?>

