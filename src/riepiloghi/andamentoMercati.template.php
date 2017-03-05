<?php

require_once 'riepiloghi.abstract.class.php';

class AndamentoMercatiTemplate extends RiepiloghiAbstract {

	private static $_instance = null;

	private static $pagina = "/riepiloghi/andamentoMercati.form.html";

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

			self::$_instance = new AndamentoMercatiTemplate();

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

		$mercatiTabs = array();
		
		$negozi = explode(",", $array["negozi"]);
		foreach($negozi as $negozio) {
			
			$vociRicavo = pg_fetch_all($_SESSION["elencoVociAndamentoRicaviMercato_" . $negozio]);
			if (count($vociRicavo) > 0) {	
				$mercatiTabs[$negozio] = $this->makeAndamentoRicaviMercatoTable($vociRicavo);
			}					
		}

		/** ******************************************
		 * Costruisco il box delle tabs
		 */
		
		$tabs = "";
		if (count($mercatiTabs) > 0) {			
			$tabs = $this->makeTabsAndamentoMercati($mercatiTabs, $negoziTabs);
		}

		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%tabs%' => $tabs,
				'%bottoneEstraiPdf%' => $_SESSION['bottoneEstraiPdf']
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>