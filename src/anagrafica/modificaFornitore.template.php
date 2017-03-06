<?php

require_once 'anagrafica.abstract.class.php';
require_once "anagrafica.presentation.interface.php";
require_once "cliente.class.php";
require_once "categoriaCliente.class.php";
require_once 'utility.class.php';

class ModificaFornitoreTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

	//-----------------------------------------------------------------------------

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
		
		$this->testata = $this->root . $this->array[self::TESTATA];
		$this->piede = $this->root . $this->array[self::PIEDE];
		$this->messaggioErrore = $this->root . $this->array[self::ERRORE];
		$this->messaggioInfo = $this->root . $this->array[self::INFO];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_FORNITORE_TEMPLATE])) $_SESSION[self::MODIFICA_FORNITORE_TEMPLATE] = serialize(new ModificaFornitoreTemplate());
		return unserialize($_SESSION[self::MODIFICA_FORNITORE_TEMPLATE]);
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
	
		if ($fornitore->getCodFornitore() == "") {
			$msg = $msg . "<br>&ndash; Manca il codice del fornitore";
			$esito = FALSE;
		}

		if ($fornitore->getDesFornitore() == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del fornitore";
			$esito = FALSE;
		}
		
		// ----------------------------------------------
		
		if ($msg != "<br>") {
			$_SESSION["messaggio"] = $msg;
		}
		else {
			unset($_SESSION["messaggio"]);
		}
		
		return $esito;
	}

	public function displayPagina() {

		$fornitore = Fornitore::getInstance();				
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = $this->root . $array['template'] . self::PAGINA_MODIFICA_FORNITORE;
		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codfornitore%' => $fornitore->getCodFornitore(),
				'%desfornitore%' => $fornitore->getDesFornitore(),
				'%indfornitore%' => $fornitore->getDesIndirizzoFornitore(),
				'%cittafornitore%' => $fornitore->getDesCittaFornitore(),
				'%capfornitore%' => $fornitore->getCapFornitore(),
				'%bonifico_checked%' => (trim($fornitore->getTipAddebito()) == "BONIFICO") ? "checked" : "",
				'%riba_checked%' => (trim($fornitore->getTipAddebito()) == "RIBA") ? "checked" : "",
				'%rimdiretta_checked%' => (trim($fornitore->getTipAddebito()) == "RIM_DIR") ? "checked" : "",
				'%assegnobancario_checked%' => (trim($fornitore->getTipAddebito()) == "ASS_BAN") ? "checked" : "",
				'%addebitodiretto_checked%' => (trim($fornitore->getTipAddebito()) == "ADD_DIR") ? "checked" : "",
				'%numggscadenzafattura%' => $fornitore->getNumGgScadenzaFattura()
		);
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}	
}

?>