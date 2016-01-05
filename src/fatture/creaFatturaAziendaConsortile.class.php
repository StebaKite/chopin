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
		require_once 'utility.class.php';
		require_once 'fattura.class.php';		
		
		// Creo la fattura -------------------------
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";
		
		$fattura = Fattura::getInstance();
		
		$fattura->AliasNbPages();
		
		/**
		 * Generazione del documento
		 */
		
		$fattura = $this->intestazione($fattura);
		$fattura = $this->sezionePagamento($fattura); 
		$fattura = $this->sezioneBanca($fattura);
		$fattura = $this->sezioneDestinatario($fattura);
		
		
		
		
		
		
		$fattura->Output();
		
		// Compone la pagina ----------------------

		$creaFatturaAziendaConsortileTemplate = CreaFatturaAziendaConsortileTemplate::getInstance();
		$this->preparaPagina($creaFatturaAziendaConsortileTemplate);
		
		include(self::$testata);
		$creaFatturaAziendaConsortileTemplate->displayPagina();
		include(self::$piede);
	}
	
	private function intestazione($fattura) {

		$_SESSION["title"] = "Cooperativa Chopin - Cooperativa sociale - ONLUS";
		$_SESSION["title1"] = "Diversamente Impresa: Esperienza occupazionale-lavorativa";
		$_SESSION["title2"] = "";
		
		return $fattura;		
	}
	
	private function sezionePagamento($fattura) {
		$fattura->AddPage();
		$fattura->pagamento($_SESSION["tipoadd"]);		
		return $fattura;
	}	

	private function sezioneBanca($fattura) {	
		$fattura->banca($_SESSION["ragsocbanca"], $_SESSION["ibanbanca"]);
		return $fattura;
	}
	
	private function sezioneDestinatario($fattura) {
		$fattura->destinatario($_SESSION["descliente"], $_SESSION["indirizzocliente"], $_SESSION["cittacliente"], $_SESSION["capcliente"], $_SESSION["pivacliente"]);
		return $fattura;
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