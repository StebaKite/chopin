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
	
		require_once 'database.class.php';
		require_once 'utility.class.php';	
		
		$creaRegistrazioneTemplate->setAzione(self::$azioneCreaRegistrazione);
		$creaRegistrazioneTemplate->setConfermaTip("%ml.confermaCreaRegistrazione%");
		$creaRegistrazioneTemplate->setTitoloPagina("%ml.creaNuovaRegistrazione%");
		
		$db = new database();
		$utility = new utility();
		
		// Prelievo delle causali  -------------------------------------------------------------
		
		$array = $utility->getConfig();

		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaCausali;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		while ($row = pg_fetch_row($result)) {
			if ($_SESSION['cod_causale'] == $row[0])
				$elenco_causali = $elenco_causali . "<option value='$row[0]' selected>$row[0] - $row[1]";
			else
				$elenco_causali = $elenco_causali . "<option value='$row[0]'>$row[0] - $row[1]";
		}
		
		$_SESSION['elenco_causali'] = $elenco_causali;

		// Prelievo dei fornitori  -------------------------------------------------------------
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaFornitori;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		
		while ($row = pg_fetch_row($result)) {
			if ($_SESSION['cod_fornitore'] == $row[0])
				$elenco_fornitori = $elenco_fornitori . "<option value='$row[0]' selected>$row[1] - $row[2]";
			else
				$elenco_fornitori = $elenco_fornitori . "<option value='$row[0]'>$row[1] - $row[2]";
		}
		
		$_SESSION['elenco_fornitori'] = $elenco_fornitori;
		
		
		
		
		
		
		
		
		
	}	
}
?>