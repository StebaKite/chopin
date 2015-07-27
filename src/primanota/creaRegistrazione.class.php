<?php

require_once 'primanota.abstract.class.php';

class creaRegistrazione extends primanotaAbstract {

	public static $azioneCreaRegistrazione = "../primanota/creaRegistrazioneFacade.class.php?modo=go";
	
	function __construct() {
	
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	
		require_once 'utility.class.php';
	
		$utility = new utility();
		$array = $utility->getConfig();
	
		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	
	}
	
	// ------------------------------------------------
	
	public function start() {
	
		require_once 'registrazione.template.php';
	
		$registrazioneTemplate = new registrazioneTemplate();
		$this->preparaPagina($registrazioneTemplate);
	
		// Compone la pagina
		include(self::$testata);
		$registrazioneTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
		
		
		
		
		
		
		
		
	}
	
	

	public function preparaPagina($registrazioneTemplate) {
	
		$registrazioneTemplate->setAzione(self::$azioneCreaRegistrazione);
		$registrazioneTemplate->setConfermaTip("%ml.confermaCreaRegistrazione%");
		$registrazioneTemplate->setTitoloPagina("%ml.creaNuovoRegistrazione%");
	}	
}
?>