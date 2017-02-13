<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.extractor.interface.php';

class EstraiPdfAndamentoNegoziConfrontato extends RiepiloghiAbstract implements RiepiloghiExtractorInterface {

	private static $_instance = null;

	public $_totaliCostiRicavi = array();

	public static $azioneEstraiPdfAndamentoNegoziConfrontato = "../riepiloghi/estraiPdfAndamentoNegoziConfrontatoFacade.class.php?modo=start";

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

			self::$_instance = new EstraiPdfAndamentoNegoziConfrontato();

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
		$pdf = $this->generaSezioneTabellaProgressivi($pdf, $utility, $db, $_SESSION["elencoVociAndamentoNegozio"]);

		$pdf->Output();
	}

	public function generaSezioneIntestazione($pdf) {

		unset($_SESSION["title"]);
		unset($_SESSION["title1"]);
		unset($_SESSION["title2"]);

		$_SESSION["title"] = "Progressivi Mensili Chopin";
		$_SESSION["title1"] = "Confronto periodo " . $_SESSION["datareg_da"] . " - " . $_SESSION["datareg_a"] . " con " . $_SESSION["datareg_da_rif"] . " - " . $_SESSION["datareg_a_rif"];

		$negozio = "";
		$negozio = ($_SESSION["codneg_sel"] == "VIL") ? "Villa D'Adda" : $negozio;
		$negozio = ($_SESSION["codneg_sel"] == "BRE") ? "Brembate" : $negozio;
		$negozio = ($_SESSION["codneg_sel"] == "TRE") ? "Trezzo" : $negozio;

		if ($_SESSION["codneg_sel"] != "") {
			$_SESSION["title2"] = "Negozio di " . $negozio;
		}

		return $pdf;
	}

	private function generaSezioneTabellaProgressivi($pdf, $utility, $db, $elencoVoci) {

		require_once 'database.class.php';
		
		unset($_SESSION["totaliProgressivi"]);
		
		$codnegozio = "";
		$codnegozio = ($_SESSION["codneg_sel"] == "") ?"'VIL','TRE','BRE'" : "'" . $_SESSION["codneg_sel"] . "'";
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => $codnegozio
		);
		
		$db = Database::getInstance();
		
		/**
		 * Estrazione Costi e Ricavi del periodo corrente
		 */
		$numCostiCor = $this->ricercaVociAndamentoCostiNegozio($utility, $db, $replace);
		$numRicaviCor = $this->ricercaVociAndamentoRicaviNegozio($utility, $db, $replace);
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da_rif"],
				'%datareg_a%' => $_SESSION["datareg_a_rif"],
				'%codnegozio%' => $codnegozio
		);
		
		/**
		 * Estrazione Costi e Ricavi del periodo di riferimento
		 */
		$numCostiRif  = $this->ricercaVociAndamentoCostiNegozioRiferimento($utility, $db, $replace);
		$numRicaviRif = $this->ricercaVociAndamentoRicaviNegozioRiferimento($utility, $db, $replace);
		
		/**
		 * Un contatore che contiene "" indica un accesso a db fallito
		 */
		
		if (($numCostiCor == 0) and ($numRicaviCor == 0)) {
			unset($_SESSION['bottoneEstraiPdf']);
		}		

		$this->makeDeltaCosti();
		$this->makeDeltaRicavi();
		
		$pdf->AddPage('L');

		if (isset($_SESSION["elencoVociDeltaCostiNegozio"])) {
			$header = array("Costi", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
			$pdf->SetFont('Arial','',9);
			$pdf->progressiviNegozioConfrontatoTable($header, $_SESSION["elencoVociDeltaCostiNegozio"], 1);
	
			$pdf->AddPage('L');				
		}

		if (isset($_SESSION["elencoVociDeltaRicaviNegozio"])) {
			$header = array("Ricavi", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
			$pdf->progressiviNegozioConfrontatoTable($header, $_SESSION["elencoVociDeltaRicaviNegozio"], 1);					
		}
		return $pdf;
	}
}

?>
