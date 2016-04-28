<?php

require_once 'chopin.abstract.class.php';

class CorpoTemplate extends ChopinAbstract {

	public static $root;
	public static $pagina = "/main/corpo.form.html";
	
	private static $_instance = null;

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

			self::$_instance = new CorpoTemplate();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function displayPagina() {

		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------

		$utility = Utility::getInstance();		
		$array = $utility->getConfig();		
		$form = self::$root . $array['template'] . self::$pagina;
				
		// Pagina -----------------------------------------------------
		
		$replace = array();		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
		}
	}

?>