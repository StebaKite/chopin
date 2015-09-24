<?php

require_once 'primanota.abstract.class.php';

class CreaPagamento extends primanotaAbstract {

	private static $_instance = null;

	public static $azioneCreaPagamento = "../primanota/creaPagamentoFacade.class.php?modo=go";

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

			self::$_instance = new CreaPagamento();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {
	
		require_once 'creaPagamento.template.php';
	
		$creaPagamentoTemplate = CreaPagamentoTemplate::getInstance();
		$this->preparaPagina($creaPagamentoTemplate);
	
		// Data del giorno preimpostata solo in entrata -------------------------
	
		$_SESSION["datareg"] = date("d/m/Y");
		$_SESSION["codneg"] = "VIL";
	
		// Compone la pagina
		include(self::$testata);
		$creaPagamentoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'creaPagamento.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		
		$creaPagamentoTemplate = CreaPagamentoTemplate::getInstance();
		
		
		
		
		
		
	}

	public function preparaPagina($creaPagamentoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$creaPagamentoTemplate->setAzione(self::$azioneCreaPagamento);
		$creaPagamentoTemplate->setConfermaTip("%ml.confermaCreaPagamento%");
		$creaPagamentoTemplate->setTitoloPagina("%ml.creaNuovaPagamento%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per popolare i combo -------------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
		$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
	
		/**
		 * Prepara la valorizzazione dei conti per la causale. L'ajax di pagina interviene solo sulla selezione
		 * della causale ma se viene fatta la submit del form i conti del dialogo non vengono più valorizzati
		*/
		$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
	}
}
	
?>	
	