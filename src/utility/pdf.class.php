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
		$this->Cell(0,10,utf8_decode($_SESSION["title"]),0,0,'C');
		$this->Ln();
		
		$this->SetFont('Arial','B',14);
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
		$this->SetFont('Arial','I',8);									// Arial italic 8
		$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');	// Page number
		
		$this->SetY(-10);												// Position at 1 cm from bottom		
		$this->Cell(0, 10, utf8_decode("Generato dal database di Nexus6 il " . date("d/m/Y")),0,1,'C');
		
		
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
	public function ImprovedTable($header, $data) {
		
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
	 * Tabella per stampa mastrino conto
	 */
	public function MastrinoContoTable($header, $data) {

		// Colors, line width and bold font
		$this->SetFillColor(28,148,196);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);
		 
		// Header
		$w = array(20, 110, 20, 20, 20);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],10,$header[$i],1,0,'C',true);
		$this->Ln();
					 
		// Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		$this->SetFont('','',8);
		
		// Data
		$totaleDare = 0;
		$totaleAvere = 0;
		$saldo = 0;
		 
		$fill = false;
		foreach($data as $row) {

			if ($row['ind_dareavere'] == 'D') {
				$totaleDare = $totaleDare + $row['imp_registrazione'];
				$impDare = $row['imp_registrazione'];
				$euroDare = EURO;
				$euroAvere = "";
				$impAvere = "";
			}
			elseif ($row['ind_dareavere'] == 'A') {
				$totaleAvere = $totaleAvere + $row['imp_registrazione'];
				$impDare = "";
				$impAvere = $row['imp_registrazione'];
				$euroDare = "";
				$euroAvere = EURO;
			}
			
			if (trim($row['tip_conto']) == "Dare") {
				$saldo = $totaleDare - $totaleAvere;
			}
			elseif (trim($row['tip_conto']) == "Avere") {
				$saldo = $totaleAvere - $totaleDare;
			}
	
			$this->SetFont('','',10);
			$fill = !$fill;
				
			$this->Cell($w[0],6,utf8_decode(date("d/m/Y",strtotime($row['dat_registrazione']))),'LR',0,'L',$fill);
			$this->Cell($w[1],6,utf8_decode(trim($row['des_registrazione'])),'LR',0,'L',$fill);
			$this->Cell($w[2],6, $euroDare . number_format($impDare, 2, ',', '.'),'LR',0,'R',$fill);
			$this->Cell($w[3],6, $euroAvere . number_format($impAvere, 2, ',', '.'),'LR',0,'R',$fill);
			
			if ($saldo < 0) {
				$this->SetTextColor(255,0,0);
				$this->SetFont('','B',10);
			}

			$this->Cell($w[4],6, EURO . number_format($saldo, 2, ',', '.'),'LR',0,'R',$fill);
			$this->Ln();
							
			$this->SetFont('');
			$this->SetTextColor(0);
		}
		
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		
		$this->SetFont('','B',10);
		$this->Cell($w[0],6,'','LR',0,'L',$fill);
		$this->Cell($w[1],6,'Saldo Finale','LR',0,'R',$fill);
		$this->Cell($w[2],6,'','LR',0,'R',$fill);
		$this->Cell($w[3],6,'','LR',0,'C',$fill);
		$this->Cell($w[4],6, EURO . number_format($saldo, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Ln();
		
		$this->Cell(array_sum($w),0,'','T');
	}
	
	/**
	 * Tabella per stampa scadenziario Fornitori
	 */
	public function ScadenzeTable($header, $data) {
		
	    // Colors, line width and bold font
	    $this->SetFillColor(28,148,196);
	    $this->SetTextColor(255);
	    $this->SetDrawColor(128,0,0);
	    $this->SetLineWidth(.3);
	    $this->SetFont('','B',10);
	    
	    // Header
		$w = array(20, 80, 90, 25, 25, 25);
	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],10,$header[$i],1,0,'C',true);
	    $this->Ln();
	    
	    // Color and font restoration
	    $this->SetFillColor(224,235,255);
	    $this->SetTextColor(0);
	    $this->SetFont('');
	    $this->SetFont('','',8);
	     
	    // Data
	    
	    $idfornitore_break = "";
	    $datscadenza_break = "";
	    $totale_fornitore = 0;
	    $totale_scadenze = 0;	     
	    
	    $fill = false;
	    foreach($data as $row) {
	    	
	    	if (($idfornitore_break == "") && ($datscadenza_break == "")) {
	    		$idfornitore_break = trim($row['id_fornitore']);
	    		$datscadenza_break = trim($row['dat_scadenza']);
	    		$desfornitore = trim($row['des_fornitore']);
	    		$datscadenza  = trim($row['dat_scadenza']);
	    	}
	    	
	    	if ((trim($row['id_fornitore']) != $idfornitore_break) | (trim($row['dat_scadenza']) != $datscadenza_break)) {
	    		
	    		$this->SetFont('','B',12);
	    		$this->Cell($w[0],10,'','LR',0,'L',$fill);
	    		$this->Cell($w[1],10,'','LR',0,'L',$fill);
	    		$this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
	    		$this->Cell($w[3],10,'','LR',0,'L',$fill);
	    		$this->Cell($w[4],10,'','LR',0,'C',$fill);
	    		$this->Cell($w[5],10, EURO . number_format($totale_fornitore, 2, ',', '.'),'LR',0,'R',$fill);
	    		$this->Ln();
	    		$fill = !$fill;

	    		$desfornitore = trim($row['des_fornitore']);
	    		$datscadenza  = trim($row['dat_scadenza']);
	    		$idfornitore_break = trim($row['id_fornitore']);
	    		$datscadenza_break = trim($row['dat_scadenza']);
	    		
	    		$totale_scadenze += $totale_fornitore;
	    		$totale_fornitore = 0;
	    	}
	    	
	    	if (trim($row['sta_scadenza']) == "00") {
	    		$stascadenza = "Da Pagare";
	    		$c1 = "0";
	    		$c2 = "0";
	    		$c3 = "0";
	    	}	    	
	    	if (trim($row['sta_scadenza']) == "10") {
	    		$stascadenza = "Pagato";
	    		$c1 = "0";
	    		$c2 = "0";
	    		$c3 = "0";
	    	}   	
	    	if (trim($row['sta_scadenza']) == "02") {
	    		$stascadenza = "Posticipato";
	    		$c1 = "51";
	    		$c2 = "153";
	    		$c3 = "255";
	    	}	    	
	    	
	    	$this->SetFont('','',10);
	    	$this->Cell($w[0],6,utf8_decode($datscadenza),'LR',0,'L',$fill);
	    	$this->Cell($w[1],6,utf8_decode($desfornitore),'LR',0,'L',$fill);
	    	$this->Cell($w[2],6,utf8_decode($row['nota_scadenza']),'LR',0,'L',$fill);
	    	$this->Cell($w[3],6,utf8_decode($row['tip_addebito']),'LR',0,'C',$fill);
	    	$this->SetFont('','',10);	    	
	    	$this->SetTextColor($c1, $c2, $c3);
	    	$this->Cell($w[4],6,utf8_decode($stascadenza),'LR',0,'C',$fill);
	    	$this->Cell($w[5],6, EURO . number_format($row['imp_in_scadenza'], 2, ',', '.'),'LR',0,'R',$fill);
	    	$this->SetFont('','B',8);
	    	$this->SetTextColor(0);
	        $this->Ln();
	        $fill = !$fill;
	        
	        $desfornitore = "";
	        $datscadenza = "";
	        $totale_fornitore += trim($row['imp_in_scadenza']);	        
	    }
	    
	    $this->SetFont('','B',12);
	    $this->Cell($w[0],10,'','LR',0,'L',$fill);
	    $this->Cell($w[1],10,'','LR',0,'L',$fill);
	    $this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
	    $this->Cell($w[3],10,'','LR',0,'L',$fill);
	    $this->Cell($w[4],10,'','LR',0,'C',$fill);
	    $this->Cell($w[5],10, EURO . number_format($totale_fornitore, 2, ',', '.'),'LR',0,'R',$fill);
	    $this->Ln();
	    $fill = !$fill;

	    $totale_scadenze += $totale_fornitore;

	    $this->SetFont('','B',12);
	    $this->Cell($w[0],15,'','LR',0,'L',$fill);
	    $this->Cell($w[1],15,'','LR',0,'L',$fill);
	    $this->Cell($w[2],15,'Totale Scadenze','LR',0,'R',$fill);
	    $this->Cell($w[3],15,'','LR',0,'L',$fill);
	    $this->Cell($w[4],15,'','LR',0,'C',$fill);
	    $this->Cell($w[5],15, EURO . number_format($totale_scadenze, 2, ',', '.'),'LR',0,'R',$fill);
	    $this->Ln();
	    $fill = !$fill;
	    
   	    $this->Cell(array_sum($w),0,'','T');	  
	}

	/**
	 * Tabella per stampa scadenziario Clienti
	 */
	public function ScadenzeClientiTable($header, $data) {
	
		// Colors, line width and bold font
		$this->SetFillColor(28,148,196);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);
		 
		// Header
		$w = array(20, 80, 90, 25, 25, 25);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],10,$header[$i],1,0,'C',true);
			$this->Ln();
		  
			// Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		$this->SetFont('','',8);
	
		// Data
		 
		$idcliente_break = "";
		$datregistrazione_break = "";
		$totale_cliente = 0;
		$totale_scadenze = 0;
		 
		$fill = false;
		foreach($data as $row) {
	
			if (($idfornitore_break == "") && ($datregistrazione_break == "")) {
				$idcliente_break = trim($row['id_cliente']);
				$datregistrazione_break = trim($row['dat_registrazione']);
				$descliente = trim($row['des_cliente']);
				$datregistrazione  = trim($row['dat_registrazione']);
			}
		
			if ((trim($row['id_cliente']) != $idcliente_break) | (trim($row['dat_registrazione']) != $datregistrazione_break)) {
			 
				$this->SetFont('','B',12);
				$this->Cell($w[0],10,'','LR',0,'L',$fill);
	    		$this->Cell($w[1],10,'','LR',0,'L',$fill);
	    		$this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
	    		$this->Cell($w[3],10,'','LR',0,'L',$fill);
	    		$this->Cell($w[4],10,'','LR',0,'C',$fill);
	    		$this->Cell($w[5],10, EURO . number_format($totale_cliente, 2, ',', '.'),'LR',0,'R',$fill);
	    		$this->Ln();
	    		$fill = !$fill;
	
	    		$descliente = trim($row['des_cliente']);
	    		$datregistrazione  = trim($row['dat_registrazione']);
	    		$idcliente_break = trim($row['id_cliente']);
	    		$datregistrazione_break = trim($row['dat_registrazione']);
		    		 
	    		$totale_scadenze += $totale_cliente;
	    		$totale_cliente = 0;
			}
		
			if (trim($row['sta_scadenza']) == "00") {
				$stascadenza = "Da Pagare";
				$c1 = "0";
				$c2 = "0";
				$c3 = "0";
			}
			if (trim($row['sta_scadenza']) == "10") {
				$stascadenza = "Pagato";
				$c1 = "0";
				$c2 = "0";
				$c3 = "0";
			}
			if (trim($row['sta_scadenza']) == "02") {
				$stascadenza = "Posticipato";
				$c1 = "51";
				$c2 = "153";
				$c3 = "255";
			}
		
			$this->SetFont('','',10);
			$this->Cell($w[0],6,utf8_decode($datregistrazione),'LR',0,'L',$fill);
			$this->Cell($w[1],6,utf8_decode($descliente),'LR',0,'L',$fill);
	    	$this->Cell($w[2],6,utf8_decode($row['nota']),'LR',0,'L',$fill);
			$this->Cell($w[3],6,utf8_decode($row['tip_addebito']),'LR',0,'C',$fill);
			$this->SetFont('','',10);
			$this->SetTextColor($c1, $c2, $c3);
			$this->Cell($w[4],6,utf8_decode($stascadenza),'LR',0,'C',$fill);
			$this->Cell($w[5],6, EURO . number_format($row['imp_registrazione'], 2, ',', '.'),'LR',0,'R',$fill);
	    	$this->SetFont('','B',8);
	    	$this->SetTextColor(0);
	    	$this->Ln();
	    	$fill = !$fill;
	    	 
	    	$descliente = "";
	    	$datregistrazione = "";
	    	$totale_cliente += trim($row['imp_registrazione']);
		}
		 
		$this->SetFont('','B',12);
		$this->Cell($w[0],10,'','LR',0,'L',$fill);
		$this->Cell($w[1],10,'','LR',0,'L',$fill);
		$this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
		$this->Cell($w[3],10,'','LR',0,'L',$fill);
		$this->Cell($w[4],10,'','LR',0,'C',$fill);
		$this->Cell($w[5],10, EURO . number_format($totale_cliente, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Ln();
		$fill = !$fill;
	
		$totale_scadenze += $totale_cliente;
	
		$this->SetFont('','B',12);
		$this->Cell($w[0],10,'','LR',0,'L',$fill);
		$this->Cell($w[1],10,'','LR',0,'L',$fill);
		$this->Cell($w[2],10,'Totale Incassi','LR',0,'R',$fill);
		$this->Cell($w[3],10,'','LR',0,'L',$fill);
		$this->Cell($w[4],10,'','LR',0,'C',$fill);
	    $this->Cell($w[5],10, EURO . number_format($totale_scadenze, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Ln();
		$fill = !$fill;
				 
		$this->Cell(array_sum($w),0,'','T');
	}
	
	public function BilancioTable($data) {
		
		// Column widths
		$w = array(150, 25);

		$desconto_break = ""; 
		$ind_visibilita_sottoconti_break = "";
		$totaleConto = 0;
		
		$sottoconti = array();
		
		// Data
		foreach($data as $row) {
			
			$totaleSottoconto = trim($row['tot_conto']);
				
			$importo = number_format($totaleSottoconto, 2, ',', '.');
			
			if (trim($row['des_conto']) != $desconto_break ) {
			
				if ($desconto_break != "") {
			
					$totconto = number_format($totaleConto, 2, ',', '.');

					$this->Ln();
					$this->SetFont('','B',12);
					$this->Cell($w[0],6, iconv('UTF-8', 'windows-1252', $desconto_break),'',0,'L');
					$this->Cell($w[1],6,$totconto,'',0,'R');
					
					if ($ind_visibilita_sottoconti_break == 'S') {
						
						foreach($sottoconti as $sottoconto) {
							
							$this->Ln();
							$this->SetFont('','',11);
							$this->Cell($w[0],6,str_repeat(' ',7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']),'',0,'L');
							$this->Cell($w[1],6,$sottoconto['importo'] . str_repeat(' ',35),'',0,'R');								
						}
					}
						
					$this->Ln();
					$i=0;
					$totaleConto = 0;
					$sottoconti = array();	
				}

				array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));				
			
				$desconto_break = trim($row['des_conto']);
				$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
			}
			else {
				array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));				
			}
			$totaleConto += $totaleSottoconto;
		}

		/**
		 * Ultimo totale di fine ciclo
		 */		
		$totconto = number_format($totaleConto, 2, ',', '.');
		
		$this->Ln();
		$this->SetFont('','B',12);
		$this->Cell($w[0],6, iconv('UTF-8', 'windows-1252', $desconto_break),'',0,'L');
		$this->Cell($w[1],6,$totconto,'',0,'R');
		
		if ($ind_visibilita_sottoconti_break == 'S') {
		
			foreach($sottoconti as $sottoconto) {
				
				if ($sottoconto['importo'] > 0) {
					$this->Ln();
					$this->SetFont('','',11);
					$this->Cell($w[0],6,str_repeat(' ',7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']),'',0,'L');
					$this->Cell($w[1],6,$sottoconto['importo'] . str_repeat(' ',35),'',0,'R');
				}
			}
		}
	} 	

	public function BilancioTableRicavi($data) {
	
			// Column widths
		$w = array(150, 25);

		$desconto_break = ""; 
		$ind_visibilita_sottoconti_break = "";
		$totaleConto = 0;
		
		$sottoconti = array();
		
		// Data
		foreach($data as $row) {
			
			$totaleSottoconto = trim($row['tot_conto']);
				
			$importo = number_format(abs($totaleSottoconto), 2, ',', '.');
			
			if (trim($row['des_conto']) != $desconto_break ) {
			
				if ($desconto_break != "") {
			
					$totconto = number_format(abs($totaleConto), 2, ',', '.');

					if ($totconto > 0) {
						$this->Ln();
						$this->SetFont('','B',12);
						$this->Cell($w[0],6, iconv('UTF-8', 'windows-1252', $desconto_break),'',0,'L');
						$this->Cell($w[1],6,$totconto,'',0,'R');
					}
					
					if ($ind_visibilita_sottoconti_break == 'S') {
						
						foreach($sottoconti as $sottoconto) {
							
							if ($sottoconto['importo'] > 0) {
								$this->Ln();
								$this->SetFont('','',11);
								$this->Cell($w[0],6,str_repeat(' ',7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']),'',0,'L');
								$this->Cell($w[1],6,$sottoconto['importo'] . str_repeat(' ',35),'',0,'R');								
							}
						}
					}
						
					$this->Ln();
					$i=0;
					$totaleConto = 0;
					$sottoconti = array();	
				}

				array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));				
			
				$desconto_break = trim($row['des_conto']);
				$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
			}
			else {
				array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));				
			}
			$totaleConto += $totaleSottoconto;
		}

		/**
		 * Ultimo totale di fine ciclo
		 */		
		$totconto = number_format(abs($totaleConto), 2, ',', '.');
		
		if ($totconto > 0) {
			$this->Ln();
			$this->SetFont('','B',12);
			$this->Cell($w[0],6, iconv('UTF-8', 'windows-1252', $desconto_break),'',0,'L');
			$this->Cell($w[1],6,$totconto,'',0,'R');
		}
		
		if ($ind_visibilita_sottoconti_break == 'S') {
		
			foreach($sottoconti as $sottoconto) {
				
				if ($sottoconto['importo'] > 0) {
					$this->Ln();
					$this->SetFont('','',11);
					$this->Cell($w[0],6,str_repeat(' ',7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']),'',0,'L');
					$this->Cell($w[1],6,$sottoconto['importo'] . str_repeat(' ',35),'',0,'R');
				}
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
		foreach($data as $row) {
				
			$totaleSottoconto = trim($row['tot_conto']);
	
			$importo = number_format(abs($totaleSottoconto), 2, ',', '.');
				
			if (trim($row['des_conto']) != $desconto_break ) {
					
				if ($desconto_break != "") {
						
					$totconto = number_format(abs($totaleConto), 2, ',', '.');

					if ($totconto > 0) {
						$this->Ln();
						$this->SetFont('','B',12);
						$this->Cell($w[0],6, iconv('UTF-8', 'windows-1252', $desconto_break),'',0,'L');
						$this->Cell($w[1],6,$totconto,'',0,'R');						
					}
						
					if ($ind_visibilita_sottoconti_break == 'S') {
					
						foreach($sottoconti as $sottoconto) {
							
							if ($sottoconto['importo'] > 0) {
								$this->Ln();
								$this->SetFont('','',11);
								$this->Cell($w[0],6,str_repeat(' ',7) . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']),'',0,'L');
								$this->Cell($w[1],6,$sottoconto['importo'] . str_repeat(' ',35),'',0,'R');
							}
						}
					}					
					$this->Ln();
					$i=0;
					$totaleConto = 0;
					$sottoconti = array();					
				}

				array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));
					
				$desconto_break = trim($row['des_conto']);
				$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
			}
			else {
				array_push($sottoconti, array('descrizione' => trim($row['des_sottoconto']), 'importo' => $importo));
			}
			$totaleConto += $totaleSottoconto;
		}
			
		/**
		 * Ultimo totale di fine ciclo
		 */
		$totconto = number_format(abs($totaleConto), 2, ',', '.');

		if ($totconto > 0) {
			$this->Ln();
			$this->SetFont('','B',12);
			$this->Cell($w[0],6, iconv('UTF-8', 'windows-1252', $desconto_break),'',0,'L');
			$this->Cell($w[1],6,$totconto,'',0,'R');
		}
		
		if ($ind_visibilita_sottoconti_break == 'S') {
		
			foreach($sottoconti as $sottoconto) {
				
				if ($sottoconto['importo'] > 0) {
					$this->Ln();
					$this->SetFont('','',11);
					$this->Cell($w[0],6,'       ' . iconv('UTF-8', 'windows-1252', $sottoconto['descrizione']),'',0,'L');
					$this->Cell($w[1],6,$sottoconto['importo'] . str_repeat(' ',35),'',0,'R');						
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
		$this->SetFont('','B',12);
		$this->SetTextColor(51, 153, 255);

		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'______________________________________','',0,'L');
		
		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Totale Ricavi','',0,'L');
		$this->Cell($w[2],8,number_format(abs($totaleRicavi), 2, ',', '.'),'',0,'R');
		
		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Totale Costi','',0,'L');
		$this->Cell($w[2],8,number_format(abs($totaleCosti), 2, ',', '.'),'',0,'R');

		$utile = $totaleRicavi - $totaleCosti;
		
		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Utile del periodo','',0,'L');
		$this->Cell($w[2],8,number_format($utile, 2, ',', '.'),'',0,'R');

		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'______________________________________','',0,'L');
		
		$this->SetTextColor(0);
	}

	public function BilancioRicaviTable($totaleRicavi, $totaleCosti) {
	
		// Column widths
		$w = array(100, 50, 25);
	
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('','B',12);
		$this->SetTextColor(51, 153, 255);

		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'RIEPILOGO________________________','',0,'L');
		
		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Totale Ricavi','',0,'L');
		$this->Cell($w[2],8,number_format(abs($totaleRicavi), 2, ',', '.'),'',0,'R');

		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Totale Costi','',0,'L');
		$this->Cell($w[2],8,number_format(abs($totaleCosti), 2, ',', '.'),'',0,'R');
		
		$perdita =  $totaleRicavi - $totaleCosti;
	
		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Perdita del periodo','',0,'L');
		$this->Cell($w[2],8,number_format($perdita, 2, ',', '.'),'',0,'R');

		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'________________________________','',0,'L');
		
		$this->SetTextColor(0);
	}
	
	public function TotaleCostiTable($totaleCosti) {

		// Column widths
		$w = array(100, 50, 25);
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('','B',12);
		$this->SetTextColor(51, 153, 255);

		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'______________________________________','',0,'L');
		
		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Totale Costi','',0,'L');
		$this->Cell($w[2],8,number_format($totaleCosti, 2, ',', '.'),'',0,'R');

		$this->SetTextColor(0);
	}

	public function TotaleRicaviTable($totaleRicavi) {
	
		// Column widths
		$w = array(100, 50, 25);
	
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('','B',12);
		$this->SetTextColor(51, 153, 255);

		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'______________________________________','',0,'L');
		
		$this->Ln();
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,'Totale Ricavi','',0,'L');
		$this->Cell($w[2],8,number_format(abs($totaleRicavi), 2, ',', '.'),'',0,'R');
	
		$this->SetTextColor(0);
	}
	
	public function TotaleAttivoTable($totaleAttivo) {

		// Column widths
		$w = array(100, 50, 25);
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('','B',12);
		$this->SetTextColor(51, 153, 255);
		
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,utf8_decode('Totale Attività'),'',0,'L');
		$this->Cell($w[2],8,number_format(abs($totaleAttivo), 2, ',', '.'),'',0,'R');
		
		$this->SetTextColor(0);
	}

	public function TotalePassivoTable($totalePassivo) {
	
		// Column widths
		$w = array(100, 50, 25);
	
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('','B',12);
		$this->SetTextColor(51, 153, 255);
	
		$this->Cell($w[0],8,'','',0,'L');
		$this->Cell($w[1],8,utf8_decode('Totale Passività'),'',0,'L');
		$this->Cell($w[2],8,number_format(abs($totalePassivo), 2, ',', '.'),'',0,'R');
	
		$this->SetTextColor(0);
	}

	public function riepilogoNegoziCostiTable($header, $data) {

		// Colors, line width and bold font
		$this->SetFillColor(28,148,196);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);
		 
		// Header
		$w = array(110, 20, 20, 20, 20);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],10,$header[$i],1,0,'C',true);
		$this->Ln();
					 
		// Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		$this->SetFont('','',8);
		
		$numReg = 0;
		$totaleCosti = 0;
		$desconto_break = "";
		
		$totaleConto_Bre = 0;
		$totaleConto_Tre = 0;
		$totaleConto_Vil = 0;
		
		$totale_Bre = 0;
		$totale_Tre = 0;
		$totale_Vil = 0;

		foreach(pg_fetch_all($data) as $row) {
		
			$totaleConto = trim($row['tot_conto']);
			$totaleCosti += $totaleConto;
				
			if (trim($row['cod_negozio']) == "BRE") $totale_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totale_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totale_Vil += $totaleConto;
		
			$numReg ++;
		
			if (trim($row['des_conto']) != $desconto_break ) {
		
				if ($desconto_break != "") {
		
					$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
					$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
					$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
					$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

					$this->SetFont('','',10);
					$fill = !$fill;
					
					$this->Cell($w[0],6,utf8_decode(trim($desconto_break)),'LR',0,'L',$fill);
					$this->Cell($w[1],6, $euroDare . number_format($totVil, 2, ',', '.'),'LR',0,'R',$fill);
					$this->Cell($w[2],6, $euroAvere . number_format($totTre, 2, ',', '.'),'LR',0,'R',$fill);
					$this->Cell($w[3],6, EURO . number_format($totBre, 2, ',', '.'),'LR',0,'R',$fill);
					$this->Cell($w[4],6, EURO . number_format($tot, 2, ',', '.'),'LR',0,'R',$fill);
					$this->Ln();
												
					$totaleConto_Bre = 0;
					$totaleConto_Tre = 0;
					$totaleConto_Vil = 0;
				}
		
				$desconto_break = trim($row['des_conto']);
			}
				
			if (trim($row['cod_negozio']) == "BRE") $totaleConto_Bre += $totaleConto;
			if (trim($row['cod_negozio']) == "TRE") $totaleConto_Tre += $totaleConto;
			if (trim($row['cod_negozio']) == "VIL") $totaleConto_Vil += $totaleConto;
		}

		$totBre = ($totaleConto_Bre != 0) ? number_format(abs($totaleConto_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totaleConto_Tre != 0) ? number_format(abs($totaleConto_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totaleConto_Vil != 0) ? number_format(abs($totaleConto_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totaleConto_Bre + $totaleConto_Tre + $totaleConto_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$this->SetFont('','',10);
		$fill = !$fill;
			
		$this->Cell($w[0],6,utf8_decode(trim($desconto_break)),'LR',0,'L',$fill);
		$this->Cell($w[1],6, $euroDare . number_format($totVil, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Cell($w[2],6, $euroAvere . number_format($totTre, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Cell($w[3],6, EURO . number_format($totBre, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Cell($w[4],6, EURO . number_format($tot, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Ln();

		/**
		 * Totale complessivo di colonna
		 */
		
		$totBre = ($totale_Bre != 0) ? number_format(abs($totale_Bre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totTre = ($totale_Tre != 0) ? number_format(abs($totale_Tre), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		$totVil = ($totale_Vil != 0) ? number_format(abs($totale_Vil), 2, ',', '.') : "&ndash;&ndash;&ndash;";
		
		$totale = $totale_Bre + $totale_Tre + $totale_Vil;
		$tot = ($totale != 0) ? number_format(abs($totale), 2, ',', '.') : "&ndash;&ndash;&ndash;";

		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
			
		$this->SetFont('','B',10);
		$this->Cell($w[0],6,"Totale Costi",'LR',0,'L',$fill);
		$this->Cell($w[1],6, $euroDare . number_format($totVil, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Cell($w[2],6, $euroAvere . number_format($totTre, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Cell($w[3],6, EURO . number_format($totBre, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Cell($w[4],6, EURO . number_format($tot, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Ln();
		
		$this->Cell(array_sum($w),0,'','T');
	}	
}

?>
