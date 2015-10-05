<?php

require_once 'riepiloghi.abstract.class.php';

class BilancioTemplate extends RiepiloghiAbstract {

	private static $_instance = null;

	private static $pagina = "/riepiloghi/bilancio.form.html";

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

	public function controlliLogici() {}

	public function displayPagina() {
	
		require_once 'utility.class.php';
	}
}	
	
?>