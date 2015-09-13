<?php

require_once 'fpdf.php';

class Pdf extends FPDF {

	private static $_instance = null;	

	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new Pdf();
	
		return self::$_instance;
	}
	
	
	public function Header() {

		define('EURO', chr(128));
		
		$this->Image($_SESSION["logo"],5,5,20);		

		$this->SetFont('Arial','B',15);
		$this->Cell(40);
		$this->Cell(30, 10, utf8_decode($_SESSION["title"]), 0, 1);
		
		$this->Ln(20);
	}

	public function Footer() {

		$this->SetY(-15);												// Position at 1.5 cm from bottom		
		$this->SetFont('Arial','I',8);									// Arial italic 8
		$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');	// Page number
		
		$this->SetY(-10);												// Position at 1 cm from bottom		
		$this->Cell(60);
		$this->Cell(0, 10, utf8_decode("Generato dal database di Nexus6 il " . date("d/m/Y")), 0, 1);
		
		
	}
		
	// Simple table
	public function BasicTable($header, $data)
	{
	    foreach($data as $row)
	    {
	        foreach($row as $col)
	            $this->Cell(190,6,$col,1);
	        $this->Ln();
	    }
	}
	
	// Better table
	public function ImprovedTable($header, $data)
	{
	    // Column widths
	    $w = array(40, 35, 40, 45);
	    // Header
	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],7,$header[$i],1,0,'C');
	    $this->Ln();
	    // Data
	    foreach($data as $row)
	    {
	        $this->Cell($w[0],6,$row[0],'LR');
	        $this->Cell($w[1],6,$row[1],'LR');
	        $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
	        $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
	        $this->Ln();
	    }
	    // Closing line
	    $this->Cell(array_sum($w),0,'','T');
	}

	/** ************************************************************
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
	public function MultiCellBulletList($w, $h, $blt_array, $border=0, $align='J', $fill=0) {

		if (is_array($blt_array)) {
			
			$bak_x = $this->x;
			
			for ($i=0; $i<sizeof($blt_array['text']); $i++) {
				
				// Prendo il bullet incluso il margine
				$blt_width = $this->GetStringWidth($blt_array['bullet'] . $blt_array['margin']) + $this->cMargin*2;
				$this->SetX($bak_x);
				
				// indentazione
				if ($blt_array['indent'] > 0) $this->Cell($blt_array['indent']);
				
				// output del bullet
				$this->Cell($blt_width, $h, $blt_array['bullet'] . $blt_array['margin'], 0, '', $fill);
				
				// output del testo
				$this->MultiCell($w - $blt_width, $h, $blt_array['text'][$i], $border, $align, $fill);
				
				// Inserisco lo spacer fra gli elementi se non è l'ultima linea
				if ($i != sizeof($blt_array['text']) - 1) $this->Ln($blt_array['spacer']);
				
				// Incremento il bullet se è un numero
				if (is_numeric($blt_array['bullet'])) $blt_array['bullet']++;
				
				// ripristino x
				$this->x = $bak_x;				
			}
		}
	}
	
	
	/**
	 * Tabella per stampa scadenziario
	 */
	public function ScadenzeTable($header, $data)
	{
	    // Colors, line width and bold font
	    $this->SetFillColor(28,148,196);
	    $this->SetTextColor(255);
	    $this->SetDrawColor(128,0,0);
	    $this->SetLineWidth(.3);
	    $this->SetFont('','B',10);
	    
	    // Header
	    $w = array(20, 110, 30, 30);
	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],10,$header[$i],1,0,'C',true);
	    $this->Ln();
	    
	    // Color and font restoration
	    $this->SetFillColor(224,235,255);
	    $this->SetTextColor(0);
	    $this->SetFont('');
	    
	    // Data
	    $fill = false;
	    foreach($data as $row)
	    {
	        $this->Cell($w[0],6,utf8_decode($row['dat_scadenza']),'LR',0,'L',$fill);
	    	$this->Cell($w[1],6,utf8_decode($row['nota_scadenza']),'LR',0,'L',$fill);
	    	$this->Cell($w[2],6,utf8_decode($row['tip_addebito']),'LR',0,'C',$fill);
	    	$this->Cell($w[3],6, EURO . number_format($row['imp_in_scadenza'], 2, ',', '.'),'LR',0,'R',$fill);
	        $this->Ln();
	        $fill = !$fill;
	    }
	    
   	    $this->Cell(array_sum($w),0,'','T');	  
	}
}

?>