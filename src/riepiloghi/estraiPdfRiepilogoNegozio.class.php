<?php

require_once 'riepiloghiComparati.abstract.class.php';

class EstraiPdfRiepilogoNegozio extends RiepiloghiComparatiAbstract {

	private static $_instance = null;

	public $_datiMCT = array();
	public $_totaliCostiRicavi = array();
	
	public static $azioneEstraiPdfRiepilogoNegozio = "../riepiloghi/estraiPdfRiepilogoNegoziFacade.class.php?modo=start";

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new EstraiPdfRiepilogoNegozio();
	
			return self::$_instance;
	}
	
	public function start() {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'pdf.class.php';
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";
	
		$pdf = Pdf::getInstance();
		$db = Database::getInstance();
		
		$pdf->AliasNbPages();

		/**
		 * Generazione del documento
		 */
		
		$pdf = $this->generaSezioneIntestazione($pdf);
		$pdf = $this->generaSezioneTabellaCosti($pdf, $utility, $db, $this->get_totaliCostiRicavi());
		$pdf = $this->generaSezioneTabellaRicavi($pdf, $utility, $db, $this->get_totaliCostiRicavi());
		$pdf = $this->generaSezioneTabellaTotali($pdf, $utility, $db, $this->get_totaliCostiRicavi());
		
		if ($_SESSION["soloContoEconomico"] == "N") {
			$pdf = $this->generaSezioneTabellaAttivo($pdf, $utility, $db);
			$pdf = $this->generaSezioneTabellaPassivo($pdf, $utility, $db);
		}
		
		$pdf = $this->generaSezioneTabellaMct($pdf, $utility, $db, $this->get_datiMCT());
		$pdf = $this->generaSezioneTabellaBep($pdf, $utility, $db, $this->get_datiMCT());
		
		$pdf->Output();
	}
	
	public function generaSezioneIntestazione($pdf) {

		unset($_SESSION["title"]);
		unset($_SESSION["title1"]);
		unset($_SESSION["title2"]);
		
		$_SESSION["title"] = "Riepilogo Costi e Ricavi Negozi";
		$_SESSION["title1"] = "Dal " . $_SESSION["datareg_da"] . " al " . $_SESSION["datareg_a"];
				
		return $pdf;
	}

	public function generaSezioneTabellaCosti($pdf, $utility, $db, $totaliCostiRicavi) {

		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"]
		);
		
		$this->ricercaCostiComparati($utility, $db, $replace);

		/**
		 * Metto i totali dei costi visualizzati in pagina in una array per la sezione dei totali Utile o Perdita
		 */
		$totaliCostiRicavi["totaleCosti_Bre"] = $_SESSION['totaleCosti_Bre'];
		$totaliCostiRicavi["totaleCosti_Tre"] = $_SESSION['totaleCosti_Tre'];
		$totaliCostiRicavi["totaleCosti_Vil"] = $_SESSION['totaleCosti_Vil'];
		$totaliCostiRicavi["totaleCosti"] 	  = $_SESSION['totaleCosti'];

		$this->set_totaliCostiRicavi($totaliCostiRicavi);
		
		$pdf->AddPage('L');
	
		$header = array("Costi", "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziTable($header, $_SESSION["costiComparati"]);
	
		return $pdf;
	}
	
	public function generaSezioneTabellaRicavi($pdf, $utility, $db, $totaliCostiRicavi) {

		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"]
		);
	
		$this->ricercaRicaviComparati($utility, $db, $replace);

		/**
		 * Metto i totali dei ricavi visualizzati in pagina in una array per la sezione dei totali Utile o Perdita
		 */
		$totaliCostiRicavi["totaleRicavi_Bre"] = $_SESSION['totaleRicavi_Bre'];
		$totaliCostiRicavi["totaleRicavi_Tre"] = $_SESSION['totaleRicavi_Tre'];
		$totaliCostiRicavi["totaleRicavi_Vil"] = $_SESSION['totaleRicavi_Vil'];
		$totaliCostiRicavi["totaleRicavi"] 	   = $_SESSION['totaleRicavi'];
		
		$this->set_totaliCostiRicavi($totaliCostiRicavi);
		
		$pdf->Cell(100,10,'','',0,'R',$fill);
		$pdf->Ln();
		
		$header = array("Ricavi", "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);		
		$pdf->riepilogoNegoziTable($header, $_SESSION["ricaviComparati"]);
	
		return $pdf;
	}
	
	public function generaSezioneTabellaTotali($pdf, $utility, $db, $totaliCostiRicavi) {
		
		$nomeTabella = strtoupper($this->nomeTabTotali($totaliCostiRicavi["totaleRicavi"], $totaliCostiRicavi["totaleCosti"]));

		/**
		 * Calcolo le differenze fra ricavi e costi e metto i totali in array 
		 */

		$totaliCostiRicavi["totale_Bre"] = $totaliCostiRicavi["totaleRicavi_Bre"] - $totaliCostiRicavi["totaleCosti_Bre"];
		$totaliCostiRicavi["totale_Tre"] = $totaliCostiRicavi["totaleRicavi_Tre"] - $totaliCostiRicavi["totaleCosti_Tre"];
		$totaliCostiRicavi["totale_Vil"] = $totaliCostiRicavi["totaleRicavi_Vil"] - $totaliCostiRicavi["totaleCosti_Vil"];
		$totaliCostiRicavi["totale"]     = $totaliCostiRicavi["totale_Bre"] + $totaliCostiRicavi["totale_Tre"] + $totaliCostiRicavi["totale_Vil"];

		$this->set_totaliCostiRicavi($totaliCostiRicavi);

		$pdf->AddPage('L');
		
		$header = array($nomeTabella, "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziTotaliTable($header, $this->get_totaliCostiRicavi());
		
		return $pdf;
	}
	
	public function generaSezioneTabellaAttivo($pdf, $utility, $db) {

		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"]
		);
		
		$this->ricercaAttivoComparati($utility, $db, $replace);
		
		$pdf->AddPage('L');
		
		$header = array("Attivo", "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziTable($header, $_SESSION["attivoComparati"]);
		
		return $pdf;
	}

	public function generaSezioneTabellaPassivo($pdf, $utility, $db) {
	
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"]
		);
	
		$this->ricercaPassivoComparati($utility, $db, $replace);

		$pdf->AddPage('L');
			
		$header = array("Passivo", "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziTable($header, $_SESSION["passivoComparati"]);
	
		return $pdf;
	}	
	
	public function generaSezioneTabellaMct($pdf, $utility, $db, $datiMCT) {

		$this->ricercaCostiVariabiliNegozi($utility, $db);
		$this->ricercaCostiFissiNegozi($utility, $db);
		$this->ricercaRicaviFissiNegozi($utility, $db);
		
		// Villa ---------------------------------------------------------------------
		
		foreach(pg_fetch_all($_SESSION['costoVariabileVIL']) as $row) {
			$datiMCT["totaleCostiVariabiliVIL"] = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiVIL']) as $row) {
			$datiMCT["totaleRicaviVIL"] = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoVIL']) as $row) {
			$datiMCT["totaleCostiFissiVIL"] = trim($row['totalecostofisso']);
		}
		
		$datiMCT["margineTotaleVIL"] = abs($datiMCT["totaleRicaviVIL"]) - $datiMCT["totaleCostiVariabiliVIL"];
		$datiMCT["marginePercentualeVIL"] = ($datiMCT["margineTotaleVIL"] * 100 ) / abs($datiMCT["totaleRicaviVIL"]);
		$datiMCT["ricaricoPercentualeVIL"] = ($datiMCT["margineTotaleVIL"] * 100 ) / abs($datiMCT["totaleCostiVariabiliVIL"]);
		
		// Trezzo ---------------------------------------------------------------------
		
		foreach(pg_fetch_all($_SESSION['costoVariabileTRE']) as $row) {
			$datiMCT["totaleCostiVariabiliTRE"] = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiTRE']) as $row) {
			$datiMCT["totaleRicaviTRE"] = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoTRE']) as $row) {
			$datiMCT["totaleCostiFissiTRE"] = trim($row['totalecostofisso']);
		}
		
		$datiMCT["margineTotaleTRE"] = abs($datiMCT["totaleRicaviTRE"]) - $datiMCT["totaleCostiVariabiliTRE"];
		$datiMCT["marginePercentualeTRE"] = ($datiMCT["margineTotaleTRE"] * 100 ) / abs($datiMCT["totaleRicaviTRE"]);
		$datiMCT["ricaricoPercentualeTRE"] = ($datiMCT["margineTotaleTRE"] * 100 ) / abs($datiMCT["totaleCostiVariabiliTRE"]);
		
		// Brembate ---------------------------------------------------------------------
		
		foreach(pg_fetch_all($_SESSION['costoVariabileBRE']) as $row) {
			$datiMCT["totaleCostiVariabiliBRE"] = trim($row['totalecostovariabile']);
		}
		
		foreach(pg_fetch_all($_SESSION['ricavoVenditaProdottiBRE']) as $row) {
			$datiMCT["totaleRicaviBRE"] = trim($row['totalericavovendita']);
		}
		
		foreach(pg_fetch_all($_SESSION['costoFissoBRE']) as $row) {
			$datiMCT["totaleCostiFissiBRE"] = trim($row['totalecostofisso']);
		}
		
		$datiMCT["margineTotaleBRE"] = abs($datiMCT["totaleRicaviBRE"]) - $datiMCT["totaleCostiVariabiliBRE"];
		$datiMCT["marginePercentualeBRE"] = ($datiMCT["margineTotaleBRE"] * 100 ) / abs($datiMCT["totaleRicaviBRE"]);
		$datiMCT["ricaricoPercentualeBRE"] = ($datiMCT["margineTotaleBRE"] * 100 ) / abs($datiMCT["totaleCostiVariabiliBRE"]);
		
		$this->set_datiMCT($datiMCT);
		
		// Nuova pagina documento -----------------------------------------------
		
		$pdf->AddPage('L');
		
		$header = array("MCT", "Brembate", "Trezzo", "Villa D'Adda");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziMctTable($header, $datiMCT);
		
		return $pdf;
		
	}

	/**
	 * Questo metodo calcola il BEP per tutti i negozi.
	 * Utilizza gli stessi dati estratti per il calcolo del margine di contribuzione (MCT) e aggiunge all'array i totali calcolati
	 * @param unknown $pdf
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $datiMCT
	 * @return unknown
	 */
	public function generaSezioneTabellaBep($pdf, $utility, $db, $datiMCT) {
		
		// Villa ---------------------------------------------------------------------
	
		$datiMCT["incidenzaCostiVariabiliSulFatturatoVIL"] = 1 - ($datiMCT["totaleCostiVariabiliVIL"] / abs($datiMCT["totaleRicaviVIL"]));
		$datiMCT["bepVIL"] = $datiMCT["totaleCostiFissiVIL"] / round($datiMCT["incidenzaCostiVariabiliSulFatturatoVIL"], 2);

		// Trezzo ---------------------------------------------------------------------

		$datiMCT["incidenzaCostiVariabiliSulFatturatoTRE"] = 1 - ($datiMCT["totaleCostiVariabiliTRE"] / abs($datiMCT["totaleRicaviTRE"]));
		$datiMCT["bepTRE"] = $datiMCT["totaleCostiFissiTRE"] / round($datiMCT["incidenzaCostiVariabiliSulFatturatoTRE"], 2);

		// Brembate ---------------------------------------------------------------------

		$datiMCT["incidenzaCostiVariabiliSulFatturatoBRE"] = 1 - ($datiMCT["totaleCostiVariabiliBRE"] / abs($datiMCT["totaleRicaviBRE"]));
		$datiMCT["bepBRE"] = $datiMCT["totaleCostiFissiBRE"] / round($datiMCT["incidenzaCostiVariabiliSulFatturatoBRE"], 2);

		$this->set_datiMCT($datiMCT);
		
		// Nuova pagina documento -----------------------------------------------

		$pdf->Cell(100,10,'','',0,'R',$fill);
		$pdf->Ln();
				
		$header = array("BEP", "Brembate", "Trezzo", "Villa D'Adda");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziBepTable($header, $datiMCT);
		
		return $pdf;
	}		
	
	public function get_datiMCT() {
		return $this->_datiMCT;
	}
	
	public function set_datiMCT($datiMCT) {
		$this->_datiMCT = $datiMCT;
	}
	
	public function get_totaliCostiRicavi() {
		return $this->_totaliCostiRicavi;
	}
	
	public function set_totaliCostiRicavi($totaliCostiRicavi) {
		$this->_totaliCostiRicavi = $totaliCostiRicavi;
	}
}	

?>
