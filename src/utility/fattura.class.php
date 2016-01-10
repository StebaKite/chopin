<?php

require_once 'fpdf.php';

class Fattura extends FPDF {
	
	private static $_instance = null;	

	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new Fattura();
	
		return self::$_instance;
	}
	
	
	public function Header() {

		define('EURO', chr(128));
		
		$this->Image($_SESSION["logo"],5,5,20);		
		
		$this->SetTextColor(36, 169, 219);
		
		$this->SetFont('Arial','B',15);
		$this->Cell(0,10,utf8_decode($_SESSION["title"]),0,0,'C');
		$this->Ln();

		$this->SetTextColor(0, 0, 0);
		
		$this->SetFont('Arial','I',12);
		$this->Cell(0,5,utf8_decode($_SESSION["title1"]),0,0,'C');
		$this->Ln();
		
		if (isset($_SESSION["title2"])) {

			$this->SetFont('Arial','I',10);
			$this->Cell(0,10,utf8_decode($_SESSION["title2"]),0,0,'C');
			$this->Ln();
		}		
		$this->Ln(10);
	}

	public function Footer() {

		$this->SetY(-16);												// Position at 1.5 cm from bottom		
		$this->SetTextColor(36, 169, 219);
				
		$this->SetFont('Arial','B',8);
		$this->Cell(0,10,'Cooperativa Chopin - Cooperativa sociale - ONLUS',0,0,'C');
		$this->SetTextColor(0, 0, 0);
		
		$this->SetY(-13);												// Position at 1 cm from bottom		
		$this->SetFont('Arial','I',8);									// Arial italic 8
		$this->Cell(0, 10, utf8_decode("WEB: http://www.progettochopin.it  -  Tel. 345 32 08 724"),0,1,'C');

		$this->SetY(-10);												// Position at 1 cm from bottom
		$this->SetFont('Arial','I',8);									// Arial italic 8
		$this->Cell(0, 10, utf8_decode("Iscritta al Registro Imprese di Bergamo - Numero R.E.A. 400220 - Numero Albo Cooperative A208408"),0,1,'C');

		$this->SetY(-7);												// Position at 1 cm from bottom
		$this->SetFont('Arial','I',8);									// Arial italic 8
		$this->Cell(0, 10, utf8_decode("Iscritta all'Albo Regionale delle Cooperative Sociali: sezione A al n.1423 con decreto n.166; sezione B al n.814con decreto 4"),0,1,'C');
		
	}

	private function RoundedRect($x, $y, $w, $h, $r, $style = '') {
		
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
		$op='B';
		else
			$op='S';
		$MyArc = 4/3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
		$xc = $x+$w-$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
	
		$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
		$xc = $x+$w-$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
		$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
		$xc = $x+$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
		$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
		$xc = $x+$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
		$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}
	
	private function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
		
		$h = $this->h;
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
				$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
	}
	
	private function Rotate($angle, $x=-1, $y=-1) {
		
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		
		if($angle!=0) {
			
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}
	
	
	public function pagamento( $mode ) {
		
		$r1  = 10;
		$r2  = $r1 + 70;
		$y1  = 50;
		$y2  = $y1+10;
		$mid = $y1 + (($y2-$y1) / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1+1 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,4, "PAGAMENTO", 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1 + 5 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$mode, 0,0, "C");
	}
	
	public function banca($ragsocbanca, $ibanbanca) {

		$r1  = 10;
		$r2  = $r1 + 70;
		$y1  = 62;
		$y2  = $y1+30;
		$mid = $y1 + (($y2-$y1) / 6);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1+1 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,4, "BANCA", 0,0, "C");
		
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1 + 8 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$ragsocbanca, 0,0, "C");
		
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1 + 13 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$ibanbanca, 0,0, "C");
	}
	
	public function destinatario($descliente, $indirizzocliente, $cittacliente, $capcliente, $pivacliente, $cfiscliente) {

		$r1  = 82;
		$r2  = $r1 + 120;
		$y1  = 50;
		$y2  = $y1+42;
		$mid = $y1 + (($y2-$y1) / 8);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1+1 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,4, "DESTINATARIO", 0,0, "C");
		
		$this->SetXY( $r1 + 5, $y1 + 8 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,"Spett.le", 0,0, "");
		
		$this->SetXY( $r1 + 5, $y1 + 13 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$descliente, 0,0, "");
		
		$this->SetXY( $r1 + 5, $y1 + 18 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$indirizzocliente, 0,0, "");
		
		$this->SetXY( $r1 + 5, $y1 + 23 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$capcliente . " " . $cittacliente, 0,0, "");
		
		if (($cfiscliente == "") or ($cfiscliente == $pivacliente)) {
			$this->SetXY( $r1 + 5, $y1 + 28 );
			$this->SetFont( "Arial", "", 10);
			$this->Cell(10,5,"P.iva/C.F. : " . $pivacliente, 0,0, "");				
		}
		else {
			$this->SetXY( $r1 + 5, $y1 + 28 );
			$this->SetFont( "Arial", "", 10);
			$this->Cell(10,5,"P.iva : " . $pivacliente, 0,0, "");
				
			$this->SetXY( $r1 + 5, $y1 + 33 );
			$this->SetFont( "Arial", "", 10);
			$this->Cell(10,5,"C.F. : " . $cfiscliente, 0,0, "");
		}		
	}
	
	public function identificativiFattura($giorno, $meserif, $anno, $numfat, $codneg) {
		
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

	public function identificativiFatturaEntePubblico($giorno, $meserif, $anno, $numfat, $codneg) {
	
		$negozio = ($codneg == "VIL") ? "Villa d'Adda" : $negozio;
		$negozio = ($codneg == "TRE") ? "Trezzo" : $negozio;
		$negozio = ($codneg == "BRE") ? "Brembate" : $negozio;

		$fatneg = ($codneg == "VIL") ? "" : $fatneg;
		$fatneg = ($codneg == "TRE") ? "T" : $fatneg;
		$fatneg = ($codneg == "BRE") ? "B" : $fatneg;		
		
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
		$this->Cell(10,4, "REG. SEZ. 1" . str_repeat(" ",48) . $negozio . "     Fattura N. :  " . $nfat . $fatneg . "/" . $anno . "   del  " . $giorno . " " . $meserif . " " . $anno, 0, 0, "");
	}

	public function identificativiFatturaCliente($giorno, $meserif, $anno, $numfat, $codneg) {
	
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
		$this->Cell(10,4, "REG. SEZ. 0" . str_repeat(" ",48) . $negozio . "     Fattura N. :  " . $nfat . "/" . $anno . "   del  " . $giorno . " " . $meserif . " " . $anno, 0, 0, "");
	}
	
	public function boxDettagli() {

		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 106;
		$y2  = $y1+169;
		$mid = $y1 + (($y2-$y1) / 2);
		
		$this->SetDrawColor(204, 204, 204);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
	}
	
	public function aggiungiLineaTabella($w, $linea) {
		
		$this->SetDrawColor(204, 204, 204);
		$this->SetFont( "Arial", "", 10);
		
		$this->Cell($w[0],6,$linea["QUANTITA"],"",0,"C");
		$this->Cell($w[1],6,$linea["ARTICOLO"],"",0,"L");
		$this->Cell($w[3],6,EURO . number_format($linea["IMPORTO U."], 2, ',', '.'),"",0,'R');
		$this->Cell($w[4],6,EURO . number_format($linea["IMPONIBILE"], 2, ',', '.'),"",0,'R');
		$this->Cell($w[5],6,EURO . number_format($linea["IVA"], 2, ',', '.'),"",0,'R');
		$this->Cell($w[6],6,$linea["%IVA"],"",0,'C');
		$this->Ln();
	}

	public function aggiungiLineaLibera($w, $linea) {
	
		$this->SetX( 15 );		
		$this->SetFont( "Arial", "", 10);
	
		$this->Cell($w[0],6,"N. " . $linea["QUANTITA"],"");
		$this->Cell($w[1],6,$linea["ARTICOLO"] . " da " . EURO . " " . $linea["IMPORTO U."] ,"");
		$this->Cell($w[2],6,EURO,"",0,'R');
		$this->Cell($w[3],6,number_format($linea["TOTALE"], 2, ',', '.'),"",0,'R');
		$this->Ln();
	}

	public function aggiungiLineaLiberaEntePubblico($w, $linea, $r1, $y1) {
	
		$this->SetXY( $r1, $y1 );
		$this->SetFont( "Arial", "", 10);
	
		$this->Cell($w[0],6,$linea["ARTICOLO"],"");
		$this->Cell($w[1],6,EURO,"",0,'R');
		$this->Cell($w[2],6,number_format($linea["TOTALE"], 2, ',', '.'),"",0,'R');
		$this->Ln();
	}

	public function aggiungiLineaLiberaCliente($w, $linea, $r1, $y1) {
	
		$this->SetXY( $r1, $y1 );
		$this->SetFont( "Arial", "", 10);
	
		$this->Cell($w[0],6,$linea["ARTICOLO"],"");
		$this->Cell($w[1],6,EURO,"",0,'R');
		$this->Cell($w[2],6,number_format($linea["TOTALE"], 2, ',', '.'),"",0,'R');
		$this->Ln();
	}
	
	public function aggiungiLineaNota($d, $r1, $y1) {
	
		$this->SetFont( "Arial", "I", 10);
		$this->SetXY( $r1, $y1 );

		foreach($d as $nota) {
			$this->SetX( $r1 );
			$this->Cell(150,6,$nota,"");
			$this->Ln();
		}
	}
	
	public function totaliFattura($tot_dettagli, $tot_imponibile, $tot_iva) {

		$this->SetFont( "Arial", "B", 10);
		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 260;
		$y2  = $y1+15;
		$this->SetFillColor(230, 230, 230);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
		$this->Line( $r1, $y1+6, $r2, $y1+6);
		$this->Line( $r1+50, $y1, $r1+50, $y2);  // davanti all' IVA
		$this->Line( $r1+100, $y1, $r1+100, $y2);  // davanti al totale
		
		$this->SetXY( $r1+16, $y1);
		$this->Cell(10,6, "IMPONIBILE");
		$this->SetX( $r1+71 );
		$this->Cell(10,6, "IVA 4%");
		$this->SetX( $r1+169 );
		$this->Cell(10,6, "TOTALE");
		
		$this->SetXY( $r1+16, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'));
		$this->SetXY( $r1+71, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_iva, 2, ',', '.'));
		$this->SetXY( $r1+169, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_dettagli, 2, ',', '.'));
		
	}

	public function totaliFatturaEntePubblico($tot_dettagli, $tot_imponibile, $tot_iva) {
	
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
		$this->Cell(10,6, "IMPONIBILE");
		$this->SetX( $r1+30 );
		$this->Cell(10,6, "IVA 4%");
		$this->SetX( $r1+50 );
		$this->Cell(10,6, "TOTALE");		
		$this->SetX( $r1+77 );
		$this->Cell(10,6, "IVA Vs. carico ex art. 17-ter, DPR n. 633/72");
		$this->SetX( $r1+154 );
		$this->Cell(10,6, "NETTO A PAGARE");
		
		$this->SetXY( $r1+3, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'));
		$this->SetXY( $r1+30, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_iva, 2, ',', '.'));
		$this->SetXY( $r1+50, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_dettagli, 2, ',', '.'));
		$this->SetX( $r1+107 );
		$this->Cell(10,6,EURO . " -" . number_format($tot_iva, 2, ',', '.'));
		$this->SetXY( $r1+154, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'));		
	}

	public function totaliFatturaContributoCliente($tot_dettagli, $tot_imponibile, $tot_iva) {
	
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
		$this->Cell(10,6, "IMPONIBILE");
		$this->SetX( $r1+30 );
		$this->Cell(10,6, "IVA 4%");
		$this->SetX( $r1+50 );
		$this->Cell(10,6, "TOTALE");
		$this->SetX( $r1+77 );
		$this->Cell(10,6, "IVA Vs. carico ex art. 17-ter, DPR n. 633/72");
		$this->SetX( $r1+154 );
		$this->Cell(10,6, "NETTO A PAGARE");
	
		$this->SetXY( $r1+3, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'));
		$this->SetXY( $r1+30, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_iva, 2, ',', '.'));
		$this->SetXY( $r1+50, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_dettagli, 2, ',', '.'));
		$this->SetX( $r1+107 );
		$this->Cell(10,6,EURO . " -" . number_format($tot_iva, 2, ',', '.'));
		$this->SetXY( $r1+154, $y1+7);
		$this->Cell(10,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'));
	}

	public function totaliFatturaVenditaCliente($tot_imponibile, $tot_iva, $tot_imponibile_10, $tot_iva_10, $tot_imponibile_22, $tot_iva_22) {

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
		
		if ($tot_imponibile_10 > 0) {
			$this->SetXY( $r1+2, $y1+7);
			$this->Cell(10,6,"10","",0,"C");					
			$this->SetX( $r1+20 );
			$this->Cell(10,6,"Iva 10%","",0,"L");					
			$this->SetX( $r1+52 );
			$this->Cell(22,6,EURO . " " . number_format($tot_imponibile_10, 2, ',', '.'),"",0,"R");
			$this->SetX( $r1+80 );
			$this->Cell(18,6,EURO . " " . number_format($tot_iva_10, 2, ',', '.'),"",0,"R");
		}

		if ($tot_imponibile_22 > 0) {
			$this->SetXY( $r1+2, $y1+12);
			$this->Cell(10,6,"22","",0,"C");
			$this->SetX( $r1+20 );
			$this->Cell(10,6,"Iva 22%","",0,"L");
			$this->SetX( $r1+52 );
			$this->Cell(22,6,EURO . " " . number_format($tot_imponibile_22, 2, ',', '.'),"",0,"R");
			$this->SetX( $r1+80 );
			$this->Cell(18,6,EURO . " " . number_format($tot_iva_22, 2, ',', '.'),"",0,"R");
			$this->Ln();
		}

		if ($tot_imponibile > 0) {
			$this->SetXY( $r1+2, $y1+17);
			$this->Cell(10,6,"  ","",0,"C");
			$this->SetX( $r1+20 );
			$this->Cell(10,6,"Esente","",0,"L");
			$this->SetX( $r1+52 );
			$this->Cell(22,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'),"",0,"R");
			$this->SetX( $r1+80 );
			$this->Cell(18,6,EURO . " " . number_format($tot_iva, 2, ',', '.'),"",0,"R");
			$this->Ln();
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

		$imponibile = $tot_imponibile + $tot_imponibile_10 + $tot_imponibile_22;
		$iva = $tot_iva + $tot_iva_10 + $tot_iva_22;

		$this->SetFont( "Arial", "", 10);
		
		$this->SetXY( $r1+28, $y1+6);
		$this->Cell(10,6,EURO . " " . number_format($imponibile, 2, ',', '.'),"",0,"R");

		$this->SetXY( $r1+28, $y1+18);
		$this->Cell(10,6,EURO . " " . number_format($iva, 2, ',', '.'),"",0,"R");

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
		$this->Cell(10,6, "NETTO A PAGARE","",0,"R");

		$netto = $imponibile + $iva;

		$this->SetFont( "Arial", "B", 14);
		
		$this->SetXY( $r1+34, $y1+10);
		$this->Cell(10,6,EURO . " " . number_format($netto, 2, ',', '.'),"",0,"R");
		
		
	
// 		$this->SetXY( $r1+3, $y1+7);
// 		$this->Cell(10,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'));
// 		$this->SetXY( $r1+30, $y1+7);
// 		$this->Cell(10,6,EURO . " " . number_format($tot_iva, 2, ',', '.'));
// 		$this->SetXY( $r1+50, $y1+7);
// 		$this->Cell(10,6,EURO . " " . number_format($tot_dettagli, 2, ',', '.'));
// 		$this->SetX( $r1+107 );
// 		$this->Cell(10,6,EURO . " -" . number_format($tot_iva, 2, ',', '.'));
// 		$this->SetXY( $r1+154, $y1+7);
// 		$this->Cell(10,6,EURO . " " . number_format($tot_imponibile, 2, ',', '.'));
	}
	
}

?>
