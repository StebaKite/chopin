<?php

require_once 'riepiloghi.abstract.class.php';

class TrendsTemplate extends RiepiloghiAbstract {

	private static $_instance = null;

	private static $pagina = "/riepiloghi/trends.form.html";

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

			self::$_instance = new TrendsTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		require_once 'utility.class.php';

		// Template --------------------------------------------------------------

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = self::$root . $array['template'] . self::$pagina;

		$trendPagamenti = $_SESSION["trendPagamenti"];	
		$trendIncassi = $_SESSION["trendIncassi"];
		
		foreach(pg_fetch_all($trendPagamenti) as $row) {
			$elencoQuantitaPagamenti .= trim($row['qtapag']) . ",";				
		}

		foreach(pg_fetch_all($trendIncassi) as $row) {
			$elencoQuantitaIncassi .= trim($row['qtainc']) . ",";
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%elencoPagamenti%' => $elencoQuantitaPagamenti,
				'%elencoIncassi%' => $elencoQuantitaIncassi
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>