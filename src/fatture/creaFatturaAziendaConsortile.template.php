<?php

require_once 'fattura.abstract.class.php';

class CreaFatturaAziendaConsortileTemplate extends FatturaAbstract {

	private static $_instance = null;

	private static $pagina = "/fatture/creaFatturaAziendaConsortile.form.html";

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

			self::$_instance = new CreaFatturaAziendaConsortileTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {
		
	}

	public function displayPagina() {
	
		require_once 'utility.class.php';
	
		// Template --------------------------------------------------------------
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = self::$root . $array['template'] . self::$pagina;

		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%numfat%' => $_SESSION["numfat"],
				'%datafat%' => $_SESSION["datafat"],
				'%descfat%' => $_SESSION["descfat"],
				'%impofat%' => $_SESSION["impofat"],
				'%impivafat%' => $_SESSION["impivafat"],
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%elenco_clienti%' => $_SESSION["elenco_clienti"]
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
		
?>
				
		
	
	