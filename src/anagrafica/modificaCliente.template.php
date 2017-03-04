<?php

require_once 'anagrafica.abstract.class.php';
require_once "anagrafica.presentation.interface.php";
require_once "cliente.class.php";
require_once "categoriaCliente.class.php";
require_once 'utility.class.php';

class ModificaClienteTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

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
		if (!isset($_SESSION[self::MODIFICA_CLIENTE_TEMPLATE])) $_SESSION[self::MODIFICA_CLIENTE_TEMPLATE] = serialize(new ModificaClienteTemplate());
		return unserialize($_SESSION[self::MODIFICA_CLIENTE_TEMPLATE]);
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {

		$cliente = Cliente::getInstance();
		
		$esito = TRUE;
		$msg = "<br>";
	
		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($cliente->getCatCliente() == "") {
			$msg = $msg . "<br>&ndash; Manca la categoria del cliente";
			$esito = FALSE;
		}
		
		if ($cliente->getCodCliente() == "") {
			$msg = $msg . "<br>&ndash; Manca il codice del cliente";
			$esito = FALSE;
		}

		if ($cliente->getDesCliente() == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del cliente";
			$esito = FALSE;
		}
		
// 		if ($_SESSION["codfisc"] != "") {

// 			include_once 'cf.class.php';
				
// 			$cf = new CodiceFiscale();
// 			$cf->SetCF($_SESSION["codfisc"]);
// 			if (!($cf->GetCodiceValido())) {
// 				$msg = $msg . "<br>&ndash; Codice fiscale non corretto";
// 				$esito = FALSE;				
// 			}
// 		}

		if (($cliente->getEsitoPivaCliente() != "P.iva Ok!") and ($cliente->getEsitoPivaCliente() != "")) {
			$msg = $msg . "<br>&ndash; P.iva cliente gi&agrave; esistente";
			unset($_SESSION["codpiva"]);
			$esito = FALSE;
		}
		
		if (($cliente->getEsitoCfisCliente() != "C.fisc Ok!") and ($cliente->getEsitoCfisCliente() != "")) {
			$msg = $msg . "<br>&ndash; C.fisc cliente gi&agrave; esistente";
			unset($_SESSION["codfisc"]);
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

		$cliente = Cliente::getInstance();
		$categoriaCliente = CategoriaCliente::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = $this->root . $array['template'] . self::PAGINA_MODIFICA_CLIENTE;
		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%elenco_categorie_cliente%' => $categoriaCliente->getElencoCategorieCliente(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codcliente%' => $cliente->getCodCliente(),
				'%descliente%' => str_replace("'","&apos;",$cliente->getDesCliente()),
				'%indcliente%' => str_replace("'","&apos;",$cliente->getDesIndirizzoCliente()),
				'%cittacliente%' => str_replace("'","&apos;",$cliente->getDesCittaCliente()),
				'%capcliente%' => $cliente->getCapCliente(),
				'%codpiva%' => $cliente->getCodPiva(),
				'%codfisc%' => $cliente->getCodFisc(),
				'%catcliente%' => $cliente->getCatCliente(),
				'%bonifico_checked%' => (trim($cliente->getTipAddebito()) == "BONIFICO") ? "checked" : "",
				'%riba_checked%' => (trim($cliente->getTipAddebito()) == "RIBA") ? "checked" : "",
				'%rimdiretta_checked%' => (trim($cliente->getTipAddebito()) == "RIM_DIR") ? "checked" : "",
				'%assegnobancario_checked%' => (trim($cliente->getTipAddebito()) == "ASS_BAN") ? "checked" : "",
				'%addebitodiretto_checked%' => (trim($cliente->getTipAddebito()) == "ADD_DIR") ? "checked" : ""
		);
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}	
}

?>