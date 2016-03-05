<?php

require_once 'riepiloghiComparati.abstract.class.php';

class EstraiPdfRiepilogoNegozio extends RiepiloghiComparatiAbstract {

	private static $_instance = null;
	
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
		$pdf = $this->generaSezioneTabellaCosti($pdf, $utility, $db);
		$pdf = $this->generaSezioneTabellaRicavi($pdf, $utility, $db);
		
		if ($_SESSION["soloContoEconomico"] == "N") {
			$pdf = $this->generaSezioneTabellaAttivo($pdf, $utility, $db);
			$pdf = $this->generaSezioneTabellaPassivo($pdf, $utility, $db);
		}
		
		$pdf = $this->generaSezioneTabellaMct($pdf, $utility, $db);
		
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

	public function generaSezioneTabellaCosti($pdf, $utility, $db) {

		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"]
		);
		
		$this->ricercaCostiComparati($utility, $db, $replace);
				
		$pdf->AddPage('L');
	
		$header = array("Costi", "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziTable($header, $_SESSION["costiComparati"]);
	
		return $pdf;
	}
	
	public function generaSezioneTabellaRicavi($pdf, $utility, $db) {

		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"]
		);
	
		$this->ricercaRicaviComparati($utility, $db, $replace);

		$pdf->Cell(100,10,'','',0,'R',$fill);
		$pdf->Ln();
		
		$header = array("Ricavi", "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);		
		$pdf->riepilogoNegoziTable($header, $_SESSION["ricaviComparati"]);
	
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

		$pdf->Cell(100,10,'','',0,'R',$fill);
		$pdf->Ln();
			
		$header = array("Passivo", "Brembate", "Trezzo", "Villa D'Adda", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziTable($header, $_SESSION["passivoComparati"]);
	
		return $pdf;
	}	
	
	public function generaSezioneTabellaMct($pdf, $utility, $db) {

		$this->ricercaCostiVariabiliNegozi($utility, $db);
		$this->ricercaCostiFissiNegozi($utility, $db);
		$this->ricercaRicaviFissiNegozi($utility, $db);
		
		$datiMCT = array();
		
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
		
		$pdf->AddPage('L');
		
		$header = array("MCT", "Brembate", "Trezzo", "Villa D'Adda");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziMctTable($header, $datiMCT);
		
		return $pdf;
		
	}
}	

?>
