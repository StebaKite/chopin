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
	
		require_once 'creaRegistrazione.template.php';
	
		$creaRegistrazioneTemplate = new creaRegistrazioneTemplate();
		$this->preparaPagina($creaRegistrazioneTemplate);
	
		// Compone la pagina
		include(self::$testata);
		$creaRegistrazioneTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
		
		
		
		
		
		
		
		
	}
	
	

	public function preparaPagina($creaRegistrazioneTemplate) {
	
		$creaRegistrazioneTemplate->setAzione(self::$azioneCreaRegistrazione);
		$creaRegistrazioneTemplate->setConfermaTip("%ml.confermaCreaRegistrazione%");
		$creaRegistrazioneTemplate->setTitoloPagina("%ml.creaNuovaRegistrazione%");
		
		//-------------------------------------------------------------
		$sql = "select idListino, descrizioneListino from paziente.listino";
		$result = $db->getData($sql);
		while ($row = pg_fetch_row($result)) {
			if ($paziente->getListino() == $row[0])
				$listino = $listino . "<option value='$row[0]' selected>$row[1]";
			else
				$listino = $listino . "<option value='$row[0]'>$row[1]";
		}
		
		
		
		
		
	}	
}
?>