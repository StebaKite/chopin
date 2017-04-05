<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaCliente.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';

class RicercaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

	function __construct() {

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
		if (!isset($_SESSION[self::RICERCA_CLIENTE])) $_SESSION[self::RICERCA_CLIENTE] = serialize(new RicercaCliente());
		return unserialize($_SESSION[self::RICERCA_CLIENTE]);
	}

	public function start() {
		
		// Template
		
		$cliente = Cliente::getInstance();		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$ricercaClienteTemplate = RicercaClienteTemplate::getInstance();
		
		$cliente->setClienti(null);
		
		if ($cliente->load(Database::getInstance())) {
	
			$_SESSION[self::CLIENTE] = serialize($cliente);
				
			$this->preparaPagina($ricercaClienteTemplate);

			$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);
				
			$ricercaClienteTemplate->displayPagina();
	
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			 */
			
			if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti";
				unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
			}
			elseif (isset($_SESSION[self::MSG_DA_CREAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CREAZIONE] . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti";
				unset($_SESSION[self::MSG_DA_CREAZIONE]);
			}
			else {
				$_SESSION[self::MESSAGGIO] = "Trovati " . $cliente->getQtaClienti() . " clienti";
			}
	
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
	
			if ($cliente->getQtaClienti() > 0) {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			}
	
			echo $utility->tailTemplate($template);
	
			include($this->piede);
		}
		else {
	
			$this->preparaPagina($ricercaClienteTemplate);

			$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);
				
			$ricercaClienteTemplate->displayPagina();
	
			$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
	
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include($this->piede);
		}
	}
	
	public function go() {
		$this->start();
	}
	
	private function preparaPagina($ricercaCausaleTemplate) {
	
		$_SESSION["azione"] = self::AZIONE_RICERCA_CLIENTE;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaCliente%";
	}
}

?>