<?php

require_once 'chopin.abstract.class.php';

class MainTemplate extends ChopinAbstract {

	public static $root;
	public static $pagina = "/main/main.html";
	
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

			self::$_instance = new MainTemplate();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function displayPagina() {

		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------

		$utility = Utility::getInstance();		
		$array = $utility->getConfig();		

		$form = self::$root . $array['template'] . self::$pagina;
				
		$this->getEnvironment ( $array );
						
		// Pagina -----------------------------------------------------

		$ambiente = isset($_SESSION["ambiente"]) ? $_SESSION["ambiente"] : $this->getEnvironment ( $array, $_SESSION );
		
		$replace = array(
				'%amb%' => $ambiente,
				'%avvisoDiv%' => $_SESSION['avvisoDiv'],
				'%avvisoDialog%' => $_SESSION['avvisoDialog'],
				'%menu%' => $this->makeMenu($utility)
		);

		unset($_SESSION['avvisoDialog']);
		unset($_SESSION['avvisoDiv']);
		
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);		
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
		
		include(self::$piede);		
	}
}
?>