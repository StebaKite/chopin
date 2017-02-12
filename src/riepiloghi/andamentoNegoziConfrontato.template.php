<?php

require_once 'riepiloghi.abstract.class.php';

class AndamentoNegoziConfrontatoTemplate extends RiepiloghiAbstract implements RiepiloghiPresentation {

	private static $_instance = null;

	private static $pagina = "/riepiloghi/andamentoNegoziConfrontato.form.html";

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

			self::$_instance = new AndamentoNegoziConfrontatoTemplate();

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

		$form = self::$root . $array['template'] . self::$pagina;

		$this->makeDeltaCosti();
		$this->makeDeltaRicavi();
		
		$andamentoCostiTable = $this->makeAndamentoCostiTable($_SESSION["elencoVociDeltaCostiNegozio"]);
		$andamentoRicaviTable = $this->makeAndamentoRicaviDeltaTable($_SESSION["elencoVociDeltaRicaviNegozio"]);

		/** ******************************************
		 * Costruisco il box delle tabs
		 */
			
		$tabs = $this->makeTabsAndamentoNegozi($andamentoCostiTable, $andamentoRicaviTable, null, null);

		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%datareg_da_rif%' => $_SESSION["datareg_da_rif"],
				'%datareg_a_rif%' => $_SESSION["datareg_a_rif"],
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%villa-selected_rif%' => ($_SESSION["codneg_sel_rif"] == "VIL") ? "selected" : "",
				'%brembate-selected_rif%' => ($_SESSION["codneg_sel_rif"] == "BRE") ? "selected" : "",
				'%trezzo-selected_rif%' => ($_SESSION["codneg_sel_rif"] == "TRE") ? "selected" : "",
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%codneg_sel_rif%' => $_SESSION["codneg_sel_rif"],
				'%tabs%' => $tabs,
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf']
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>