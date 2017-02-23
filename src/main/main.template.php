<?php

require_once 'nexus6.abstract.class.php';
require_once 'main.presentation.interface.php';

class MainTemplate extends Nexus6Abstract implements MainPresentationInterface {

	function __construct() {

		require_once 'utility.class.php';

		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
		
		$this->testata = $this->root . $this->array['testataPagina'];
		$this->piede = $this->root . $this->array['piedePagina'];
		$this->messaggioErrore = $this->root . $this->array['messaggioErrore'];
		$this->messaggioInfo = $this->root . $this->array['messaggioInfo'];		
	}

	public static function getInstance() {

		if (!isset($_SESSION["Obj_maintemplate"])) $_SESSION["Obj_maintemplate"] = serialize(new MainTemplate());
		return unserialize($_SESSION["Obj_maintemplate"]);		
	}

	public function controlliLogici() {}
	
	public function displayPagina() {

		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------

		$utility = Utility::getInstance();		
		$array = $utility->getConfig();		

		$form = $this->root . $array['template'] . self::MAIN_PAGE;
				
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
		
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);		
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
		
		include($this->piede);		
	}
}
?>