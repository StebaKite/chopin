<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';

class BilancioTemplate extends RiepiloghiAbstract implements RiepiloghiPresentationInterface {

	private static $_instance = null;

	private static $paginaPeriodico = "/riepiloghi/bilancioPeriodico.form.html";
	private static $paginaEsercizio = "/riepiloghi/bilancioEsercizio.form.html";
	
	//-----------------------------------------------------------------------------

	function __construct() {
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new BilancioTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() { return TRUE;}

	public function displayPagina() {

		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		if ($_SESSION["tipoBilancio"] == "Periodico") {
			$form = self::$root . $array['template'] . self::$paginaPeriodico;
		}
		elseif ($_SESSION["tipoBilancio"] == "Esercizio") {
			$form = self::$root . $array['template'] . self::$paginaEsercizio;
		}

		$risultato_esercizio = "";
		$tabs = "";

		/** ******************************************************
		 * Costruzione delle tabelle 
		 */

		$risultato_costi = $this->makeCostiTable($array);
		$risultato_ricavi = $this->makeRicaviTable($array);
		$risultato_attivo = $this->makeAttivoTable();
		$risultato_passivo = $this->makePassivoTable();
		
		/** ******************************************
		 * Costruisco il box delle tabs
		 */
					
		$tabs = $this->makeTabs($risultato_costi, $risultato_ricavi, $risultato_attivo, $risultato_passivo);
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%catconto_sel%' => $_SESSION["catconto_sel"],				
				'%contoeco-selected%' => ($_SESSION["catconto_sel"] == "Conto Economico") ? "selected" : "",
				'%statopat-selected%' => ($_SESSION["catconto_sel"] == "Stato Patrimoniale") ? "selected" : "",
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%tabs%' => $tabs,
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf'],
				'%saldiInclusichecked%' => ($_SESSION["saldiInclusi"] == "S") ? "checked" : "",
				'%saldiEsclusichecked%' => ($_SESSION["saldiInclusi"] == "N") ? "checked" : "",
				'%saldiInclusi%' => $_SESSION["saldiInclusi"],
				'%tuttiContichecked%' => ($_SESSION["soloContoEconomico"] == "N") ? "checked" : "",
				'%soloContoEconomicochecked%' => ($_SESSION["soloContoEconomico"] == "S") ? "checked" : "",
				'%soloContoEconomico%' => $_SESSION["soloContoEconomico"],
				'%ml.anno_esercizio_corrente%' => date("Y"),
				'%ml.anno_esercizio_menouno%' => date("Y")-1,
				'%ml.anno_esercizio_menodue%' => date("Y")-2,
				'%ml.anno_esercizio_menotre%' => date("Y")-3
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}

	public function tabellaTotaliRiepilogoNegozi($tipoTotale) {}
}	
	
?>