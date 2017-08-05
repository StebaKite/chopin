<?php

require_once 'riepiloghi.abstract.class.php';

class EstraiPdfAndamentoMercati extends RiepiloghiAbstract {

	private static $_instance = null;

	public $_totaliCostiRicavi = array();

	public static $azioneEstraiPdfAndamentoMercati = "../riepiloghi/estraiPdfAndamentoMercatiFacade.class.php?modo=start";

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

			self::$_instance = new EstraiPdfAndamentoMercati();

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


		$negozi = explode(",", $array["negozi"]);
		foreach($negozi as $negozio) {

			$vociRicavo = $_SESSION["elencoVociAndamentoRicaviMercato_" . $negozio];
			if (count($vociRicavo) > 0) {
				$pdf = $this->generaSezioneTabellaProgressivi($pdf, $utility, $db, $vociRicavo, $negozio);
			}
		}

//		$pdf = $this->generaSezioneTabellaUtilePerdita($pdf, $utility, $db, $_SESSION["totaliComplessiviAcquistiMesi"], $_SESSION["totaliComplessiviRicaviMesi"]);
//		$pdf = $this->generaSezioneTabellaMctProgressivi($pdf, $utility, $db, $_SESSION["totaliAcquistiMesi"], $_SESSION["totaliRicaviMesi"]);

		$pdf->Output();
	}

	public function generaSezioneIntestazione($pdf) {

		unset($_SESSION["title"]);
		unset($_SESSION["title1"]);
		unset($_SESSION["title2"]);

		$_SESSION["title"] = "Progressivi Mensili Mercati Chopin";
		$_SESSION["title1"] = "Dal " . $_SESSION["datareg_da"] . " al " . $_SESSION["datareg_a"];

		return $pdf;
	}

	public function generaSezioneTabellaProgressivi($pdf, $utility, $db, $elencoVoci, $negozio) {

		$pdf->AddPage('L');

		if ($negozio == "VIL") $descNeg = "Villa d'Adda";
		if ($negozio == "TRE") $descNeg = "Trezzo";
		if ($negozio == "BRE") $descNeg = "Brembate";

		$header = array("Mercati di " . $descNeg, "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->progressiviMercatoTable($header, $elencoVoci, 1);

		return $pdf;
	}
}

?>
