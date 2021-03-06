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
		$this->Cell(0,10,iconv('UTF-8', 'windows-1252', $_SESSION["title"]),0,0,'C');
		$this->Ln();

		$this->SetTextColor(0, 0, 0);
		
		$this->SetFont('Arial','I',12);
		$this->Cell(0,5,iconv('UTF-8', 'windows-1252', $_SESSION["title1"]),0,0,'C');
		$this->Ln();
		
		if (isset($_SESSION["title2"])) {

			$this->SetFont('Arial','I',10);
			$this->Cell(0,10,iconv('UTF-8', 'windows-1252', $_SESSION["title2"]),0,0,'C');
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
		
		$this->SetY(-11);												// Position at 1 cm from bottom		
		$this->SetFont('Arial','I',8);									// Arial italic 8
		$this->Cell(0, 10, iconv('UTF-8', 'windows-1252', "WEB: http://www.progettochopin.it  -  Tel. 345 32 08 724"),0,1,'C');		
	}

	public function RoundedRect($x, $y, $w, $h, $r, $style = '') {
		
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
	
	public function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
		
		$h = $this->h;
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
				$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
	}
	
	public function Rotate($angle, $x=-1, $y=-1) {
		
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
	
	public function destinatario($descliente, $indirizzocliente, $cittacliente, $capcliente, $pivacliente, $cfiscliente, $titolo) {

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

		$this->SetFont( "Arial", "", 12);
		
		$this->SetXY( $r1 + 5, $y1 + 8 );
		$this->Cell(10,5,$titolo, 0,0, "");
		
		$this->SetXY( $r1 + 5, $y1 + 13 );
		$this->Cell(10,5,$descliente, 0,0, "");
		
		$this->SetXY( $r1 + 5, $y1 + 18 );
		$this->Cell(10,5,$indirizzocliente, 0,0, "");
		
		$this->SetXY( $r1 + 5, $y1 + 23 );
		$this->Cell(10,5,$capcliente . " " . $cittacliente, 0,0, "");

		if ($cfiscliente == $pivacliente) {
			$this->SetXY( $r1 + 5, $y1 + 28 );
			$this->Cell(10,5,"P.iva/C.F. : " . $pivacliente, 0,0, "");
		}
		else {
			if ($pivacliente != "") {
				$this->SetXY( $r1 + 5, $y1 + 28 );
				$this->Cell(10,5,"P.iva : " . $pivacliente, 0,0, "");
			}
			
			if ($cfiscliente != "") {
				$this->SetXY( $r1 + 5, $y1 + 33 );
				$this->Cell(10,5,"C.F. : " . $cfiscliente, 0,0, "");
			}				
		}
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
		
		$articolo = explode("\\", $linea["ARTICOLO"]);
				
		$this->Cell($w[1],6,iconv('UTF-8', 'windows-1252', $articolo[0]),"",0,"L");
		$this->Cell($w[2],6,number_format($linea["IMPORTO U."], 2, ',', '.'),"",0,'R');
		$this->Cell($w[3],6,number_format($linea["TOTALE"], 2, ',', '.'),"",0,'R');
		$this->Cell($w[4],6,number_format($linea["IMPONIBILE"], 2, ',', '.'),"",0,'R');
		$this->Cell($w[5],6,number_format($linea["IVA"], 2, ',', '.'),"",0,'R');
		$this->Cell($w[6],6,$linea["%IVA"],"",0,'C');
		$this->Ln();
		
		for($i=1;$i<count($articolo);$i++) {
			$this->Cell($w[0],4,"","",0,"C");
			$this->Cell($w[1],4,iconv('UTF-8', 'windows-1252', $articolo[$i]),"",0,"L");				
			$this->Ln();
		}
	}
	
	public function aggiungiLineaNota($d, $r1, $y1) {
	
		$this->SetFont( "Arial", "", 12);
		$this->SetXY( $r1, $y1 );
		foreach($d as $nota) {
			$this->Cell(150,6,iconv('UTF-8', 'windows-1252', $nota),"",0,"L");
			$this->Ln();
			$this->SetX( $r1 );
		}
	}
}

?>
