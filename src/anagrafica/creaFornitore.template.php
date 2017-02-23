<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.presentation.interface.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class CreaFornitoreTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
		
		$this->testata = $this->root . $this->array[self::TESTATA];
		$this->piede = $this->root . $this->array[self::PIEDE];
		$this->messaggioErrore = $this->root . $this->array[self::ERRORE];
		$this->messaggioInfo = $this->root . $this->array[self::INFO];
	}

	public function getInstance() {
		
		if (!isset($_SESSION[self::CREA_FORNITORE_TEMPLATE])) $_SESSION[self::CREA_FORNITORE_TEMPLATE] = serialize(new CreaFornitoreTemplate());
		return unserialize($_SESSION[self::CREA_FORNITORE_TEMPLATE]);
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}
	
	public function controlliLogici() {

		$fornitore = Fornitore::getInstance();
		
		$esito = TRUE;
		$msg = "<br>";
	
		/**
		 * Controllo presenza dati obbligatori
		 */
	
		if ($fornitore->get_cod_fornitore() == "") {
			$msg = $msg . self::ERRORE_CODICE_FORNITORE;
			$esito = FALSE;
		}
		
		if ($fornitore->get_des_fornitore() == "") {
			$msg = $msg . self::ERRORE_DESCRIZIONE_FORNITORE;
			$esito = FALSE;
		}
		
		// ----------------------------------------------
		
		if ($msg != "<br>") {
			$_SESSION[self::MESSAGGIO] = $msg;
		}
		else {
			unset($_SESSION[self::MESSAGGIO]);
		}		
		return $esito;
	}

	public function displayPagina() {
	
		$form = $this->root . $this->array['template'] . self::PAGINA_CREA_FORNITORE;

		$fornitore = Fornitore::getInstance();
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codfornitore%' => $fornitore->get_cod_fornitore(),
				'%desfornitore%' => $fornitore->get_des_fornitore(),
				'%indfornitore%' => $fornitore->get_des_indirizzo_fornitore(),
				'%cittafornitore%' => $fornitore->get_des_citta_fornitore(),
				'%capfornitore%' => $fornitore->get_cap_fornitore(),
				'%tipoaddebito%' => $fornitore->get_tip_addebito()
		);
	
		$template = $this->utility->tailFile($this->utility->getTemplate($form), $replace);
		echo $this->utility->tailTemplate($template);
	}	
}		

?>