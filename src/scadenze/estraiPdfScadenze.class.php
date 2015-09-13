<?php

require_once 'scadenze.abstract.class.php';

class EstraiPdfScadenze extends ScadenzeAbstract {

	private static $_instance = null;

	public static $azioneRicercaScadenze = "../scadenze/ricercaScadenzeFacade.class.php?modo=go";
	public static $queryRicercaScadenze = "/scadenze/ricercaScadenze.sql";

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

			self::$_instance = new EstraiPdfScadenze();

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
		$pdf = $this->generaSezioneTabellaScadenze($pdf, $utility);
		
		$pdf->Output();
	}
	
	public function generaSezioneIntestazione($pdf) {
			
		$_SESSION["title"] = "Scadenze dal " . $_SESSION["datascad_da"] . " al " . $_SESSION["datascad_a"];
	
		return $pdf;
	}
	
	public function generaSezioneTabellaScadenze($pdf, $utility) {

		$pdf->AddPage();
		
		$header = array("Data", "Descrizione", "Tipo Addebito", "Importo");
		$pdf->SetFont('Arial','',9);
		$pdf->ScadenzeTable($header, $this->ricercaDati($utility));
		
		return $pdf;
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		$filtro = "";
	
		$replace = array(
				'%dat_scadenza_da%' => $_SESSION["datascad_da"],
				'%dat_scadenza_a%' => $_SESSION["datascad_a"]
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaScadenze;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$db = Database::getInstance();
		$result = $db->getData($sql);
				
		return pg_fetch_all($result);
	}
}

?>