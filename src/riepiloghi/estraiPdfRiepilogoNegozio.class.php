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
		$pdf = $this->generaSezioneTabellaCostiRiepilogoNegozi($pdf, $utility, $db);
		$pdf = $this->generaSezioneTabellaRicaviRiepilogoNegozi($pdf, $utility, $db);
		
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

	public function generaSezioneTabellaCostiRiepilogoNegozi($pdf, $utility, $db) {

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
	
	public function generaSezioneTabellaRicaviRiepilogoNegozi($pdf, $utility, $db) {

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
		
}	

?>
