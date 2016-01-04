<?php

require_once 'fattura.abstract.class.php';

/**
 * Crazione della fattura per le aziende consortili
 * 
 * @author stefano
 *
 */
class CreaFatturaAziendaConsortile extends FatturaAbstract {

	private static $_instance = null;
	
	public static $azioneCreaFatturaAziendaConsortile = "../fatture/creaFatturaAziendaConsortileFacade.class.php?modo=go";
	
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
	
			self::$_instance = new CreaFatturaAziendaConsortile();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------
	
	public function start() {
	
		require_once 'creaFatturaAziendaConsortile.template.php';
	
		$creaFatturaAziendaConsortileTemplate = CreaFatturaAziendaConsortileTemplate::getInstance();
		$this->preparaPagina($creaFatturaAziendaConsortileTemplate);

		// Data del giorno preimpostata solo in entrata -------------------------
		
		$_SESSION["datafat"] = date("d/m/Y");
		$_SESSION["codneg"] = "";
		$_SESSION["numfat"] = "";
		$_SESSION["tipoadd"] = "";
		$_SESSION["ragsocbanca"] = "";
		$_SESSION["ibanbanca"] = "";
		$_SESSION["dettagliInseriti"] = "";
		$_SESSION["indexDettagliInseriti"] = "";
		
		// Compone la pagina
		include(self::$testata);
		$creaFatturaAziendaConsortileTemplate->displayPagina();
		include(self::$piede);
	}
	
	public function go() {
		
		require_once 'creaFatturaAziendaConsortile.template.php';
		
		$creaFatturaAziendaConsortileTemplate = CreaFatturaAziendaConsortileTemplate::getInstance();
		$this->preparaPagina($creaFatturaAziendaConsortileTemplate);
		
		// Creo la fattura -------------------------
		


		
		
		
		
		
		// Compone la pagina
		include(self::$testata);
		$creaFatturaAziendaConsortileTemplate->displayPagina();
		include(self::$piede);
		
		
		
		
		
		
		
		
		
		
		
		
		
	}
	
	public function preparaPagina($creaFatturaAziendaConsortileTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaFatturaAziendaConsortileTemplate->setAzione(self::$azioneCreaFatturaAziendaConsortile);
		$creaFatturaAziendaConsortileTemplate->setConfermaTip("%ml.confermaCreaFattura%");
		$creaFatturaAziendaConsortileTemplate->setTitoloPagina("%ml.creaFatturaAziendaConsortile%");
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle aziende consortili -------------------------------------------------------------
		
		$_SESSION['elenco_clienti'] = $this->caricaClientiFatturabili($utility, $db, "1200");	// Categoria=1200 -> Aziende
	}
}	

?>