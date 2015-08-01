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

		require_once 'creaRegistrazione.template.php';
		require_once 'utility.class.php';

		$utility = new utility();
		
		$creaRegistrazioneTemplate = new creaRegistrazioneTemplate();
		$this->preparaPagina($creaRegistrazioneTemplate);
		
		if ($creaRegistrazioneTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------
			
			if ($this->creaRegistrazione()) {
				
				if ($this->creaDettaglioRegistrazione()) {

					$_SESSION["messaggio"] = "Registrazione salvata con successo";

					include(self::$testata);
					$creaRegistrazioneTemplate->displayPagina();
					
					self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
					echo $utility->tailTemplate($template);
						
					include(self::$piede);					
				}
			}
		}
		else {
			
			include(self::$testata);			
			$creaRegistrazioneTemplate->displayPagina();
				
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);			
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);				
		}		
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

		if (!isset($_SESSION['elenco_causali'])) {
			$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db);
		}

		// Prelievo dei fornitori  -------------------------------------------------------------
		
		if (!isset($_SESSION['elenco_fornitori'])) {
			$_SESSION['elenco_fornitori'] = $this->caricaFornitori($utility, $db);
		}

		// Prelievo dei clienti  -------------------------------------------------------------
		
		if (!isset($_SESSION['elenco_clienti'])) {
			$_SESSION['elenco_clienti'] = $this->caricaClienti($utility, $db);
		}
		
		// Prelievo dei conti ------------------------------------------------------------------

		if (!isset($_SESSION['elenco_conti'])) {
			$_SESSION['elenco_conti'] = $this->caricaConti($utility, $db);
		}
		
		
	}	
}
?>