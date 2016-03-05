<?php

require_once 'riepiloghiComparati.abstract.class.php';

class EstraiPdfRiepilogoNegozi extends RiepiloghiComparatiAbstract {

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
	
			self::$_instance = new EstraiPdfRiepilogoNegozi();
	
			return self::$_instance;
	}
	
	public function start() {
	
		require_once 'utility.class.php';
		require_once 'pdf.class.php';
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";
	
		$pdf = Pdf::getInstance();
	
		$pdf->AliasNbPages();

		/**
		 * Generazione del documento
		 */
		
		$pdf = $this->generaSezioneIntestazione($pdf);
		$pdf = $this->generaSezioneTabellaCostiRiepilogoNegozi($pdf, $utility);
		
		$pdf->Output();
	}
	
	public function generaSezioneIntestazione($pdf) {
			
		$_SESSION["title"] = "Riepilogo Comparativo Negozi dal " . $_SESSION["datareg_da"] . " al " . $_SESSION["datareg_a"];
		return $pdf;
	}

	public function generaSezioneTabellaCostiRiepilogoNegozi($pdf, $utility) {
		
		$pdf->AddPage();
	
		$header = array("Costi", "Villa D'Adda", "Trezzo", "Brembate", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->riepilogoNegoziCostiTable($header, $_SESSION["costiComparati"]);
	
		return $pdf;
	}
}	

?>
