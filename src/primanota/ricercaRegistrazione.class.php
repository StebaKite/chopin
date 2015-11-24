<?php

require_once 'primanota.abstract.class.php';

class RicercaRegistrazione extends PrimanotaAbstract {

	private static $_instance = null;
	
	public static $azioneRicercaRegistrazione = "../primanota/ricercaRegistrazioneFacade.class.php?modo=go";
	public static $queryRicercaRegistrazione = "/primanota/ricercaRegistrazione.sql";
	
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
	
			self::$_instance = new RicercaRegistrazione();
	
		return self::$_instance;
	}
	
	public function start() {

		require_once 'ricercaRegistrazione.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");
		$_SESSION["numfatt"] = "";
		$_SESSION["codneg_sel"] = "VIL";
		unset($_SESSION["registrazioniTrovate"]);
		unset($_SESSION['referer_function_name']);
		
		$ricercaRegistrazioneTemplate = RicercaRegistrazioneTemplate::getInstance();
		$this->preparaPagina($ricercaRegistrazioneTemplate);
		
		// compone la pagina
		include($testata);
		$ricercaRegistrazioneTemplate->displayPagina();
		include($piede);
	}

	public function go() {

		require_once 'ricercaRegistrazione.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$ricercaRegistrazioneTemplate = RicercaRegistrazioneTemplate::getInstance();

		if ($ricercaRegistrazioneTemplate->controlliLogici()) {
			
			if ($this->ricercaDati($utility)) {
			
				$this->preparaPagina($ricercaRegistrazioneTemplate);
			
				include(self::$testata);
				$ricercaRegistrazioneTemplate->displayPagina();

				/**
				 * Gestione del messaggio proveniente da altre funzioni
				 */
				if (isset($_SESSION["messaggioCancellazione"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovate " . $_SESSION['numRegTrovate'] . " registrazioni";
					unset($_SESSION["messaggioCancellazione"]);
				}
				elseif (isset($_SESSION["messaggioModifica"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioModifica"] . "<br>" . "Trovate " . $_SESSION['numRegTrovate'] . " registrazioni";
					unset($_SESSION["messaggioModifica"]);
										}
				else {
					$_SESSION["messaggio"] = "Trovate " . $_SESSION['numRegTrovate'] . " registrazioni";
				}
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				
				if ($_SESSION['numRegTrovate'] > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);						
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);						
				}
				
				echo $utility->tailTemplate($template);
			
				include(self::$piede);
			}
			else {
			
				$this->preparaPagina($ricercaRegistrazioneTemplate);
					
				include(self::$testata);
				$ricercaRegistrazioneTemplate->displayPagina();

				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
				
			$this->preparaPagina($ricercaRegistrazioneTemplate);
		
			include(self::$testata);
			$ricercaRegistrazioneTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}
	
	public function ricercaDati($utility) {

		require_once 'database.class.php';
		
		$filtriRegistrazione = "";
		$filtriDettaglio = "";
		
		if ($_SESSION["causale"] != "") {
			$filtriRegistrazione .= "and reg.cod_causale = '" . $_SESSION["causale"] . "'";
		}
		if ($_SESSION["numfatt"] != "") {
			$filtriRegistrazione .= "and reg.num_fattura like '" . $_SESSION["numfatt"] . "%'";
		}
		if ($_SESSION["codneg_sel"] != "") {
			$filtriRegistrazione .= "and reg.cod_negozio = '" . $_SESSION["codneg_sel"] . "'";
		}		
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%filtri-registrazione%' => $filtriRegistrazione,
				'%filtri-dettaglio%' => $filtriDettaglio,
		);
		
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaRegistrazione;
		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		
		// esegue la query
		
		$db = Database::getInstance();
		$result = $db->getData($sql);
		
		if (pg_num_rows($result) > 0) {
			$_SESSION['registrazioniTrovate'] = $result;
		}
		else {
			unset($_SESSION['registrazioniTrovate']);
			$_SESSION['numRegTrovate'] = 0;
		}
		
		return $result;
	}
	
	public function preparaPagina($ricercaRegistrazioneTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$_SESSION["azione"] = self::$azioneRicercaRegistrazione;
		$_SESSION["confermaTip"] = "%ml.confermaRicercaRegistrazione%";
		$_SESSION["titoloPagina"] = "%ml.ricercaRegistrazione%";
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		// Prelievo dei dati per popolare i combo -------------------------------------------------------------
		
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
	}
}	
?>