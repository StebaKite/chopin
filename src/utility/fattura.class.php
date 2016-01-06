<?php

require_once 'fpdf.php';

class Fattura extends FPDF {


	public static $mese = array(
			'01' => 'gennaio',
			'02' => 'febbraio',
			'03' => 'marzo',
			'04' => 'aprile',
			'05' => 'maggio',
			'06' => 'giugno',
			'07' => 'luglio',
			'08' => 'agosto',
			'09' => 'settembre',
			'10' => 'ottobre',
			'11' => 'novembre',
			'12' => 'dicembre'
	);
	
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

		$this->SetFont('Arial','B',15);
		$this->Cell(0,10,utf8_decode($_SESSION["title"]),0,0,'C');
		$this->Ln();
		
		$this->SetFont('Arial','I',12);
		$this->Cell(0,10,utf8_decode($_SESSION["title1"]),0,0,'C');
		$this->Ln();
		
		if (isset($_SESSION["title2"])) {

			$this->SetFont('Arial','I',12);
			$this->Cell(0,10,utf8_decode($_SESSION["title2"]),0,0,'C');
			$this->Ln();
		}		
		$this->Ln(10);
	}

	public function Footer() {

		$this->SetY(-15);												// Position at 1.5 cm from bottom		
		$this->SetFont('Arial','B',8);									// Arial italic 8
		$this->Cell(0,10,'Cooperativa Chopin - Cooperativa sociale - ONLUS',0,0,'C');
				
		$this->SetY(-10);												// Position at 1 cm from bottom		
		$this->SetFont('Arial','',8);									// Arial italic 8
		$this->Cell(0, 10, utf8_decode("Domicilio fiscale: via San Martino, 1 - 24030 Villa d'Adda (BG) - tel. 3453208724 C.F./P.IVA: 03691430163"),0,1,'C');		
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
		$y1  = 40;
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
		$y1  = 52;
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
		$y1  = 40;
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
	
	public function identificativiFattura($datafat, $numfat, $codneg) {
		
		$negozio = ($codneg == "VIL") ? "Villa d'Adda" : $negozio;
		$negozio = ($codneg == "TRE") ? "Trezzo" : $negozio;
		$negozio = ($codneg == "BRE") ? "Brembate" : $negozio;
		
		$nfat = str_pad($numfat, 2, "0", STR_PAD_LEFT);
		
		$anno = substr($datafat, 6);
		$nmese = substr($datafat, 3,2);
		$giorno = substr($datafat, 0,2);
				
		$meserif = SELF::$mese[str_pad($nmese, 2, "0", STR_PAD_LEFT)];
		
		$r1  = 10;
		$r2  = $r1 + 192;
		$y1  = 84;
		$y2  = $y1+10;
		$mid = $y1 + (($y2-$y1) / 2);
		$this->SetFillColor(166, 221, 242);		
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'DF');
		$this->SetXY( $r1 + 5, $y1 + 3 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,4, "REG. SEZ. 1PA" . str_repeat(" ",48) . $negozio . "     Fattura N. :  " . $nfat . "PA/" . $anno . "   del  " . $giorno . " " . $meserif . " " . $anno, 0, 0, "");				
	}
}

?>
