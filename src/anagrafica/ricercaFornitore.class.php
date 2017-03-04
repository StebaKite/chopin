<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaFornitore.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fornitore.class.php';

class RicercaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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

		if (!isset($_SESSION[self::RICERCA_FORNITORE])) $_SESSION[self::RICERCA_FORNITORE] = serialize(new RicercaFornitore());
		return unserialize($_SESSION[self::RICERCA_FORNITORE]);
	}

	public function start() {
	
		// Template
		
		$fornitore = Fornitore::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$ricercaFornitoreTemplate = RicercaFornitoreTemplate::getInstance();
	
		$fornitore->setFornitori(null);
		
		if ($fornitore->load(Database::getInstance())) {

			$_SESSION[self::FORNITORE] = serialize($fornitore);
				
			$this->preparaPagina($ricercaFornitoreTemplate);
				
			$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);
			
			$ricercaFornitoreTemplate->displayPagina();
	
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			*/
			if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovati " . $fornitore->getQtaFornitori() . " fornitori";
				unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
			}
			else {
				$_SESSION[self::MESSAGGIO] = "Trovati " . $fornitore->getQtaFornitori() . " fornitori";
			}
	
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
	
			if ($fornitore->getQtaFornitori()  > 0) {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			}
	
			echo $utility->tailTemplate($template);
	
			include($this->piede);
		}
		else {
	
			$this->preparaPagina($ricercaFornitoreTemplate);
				
			$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);

			$ricercaFornitoreTemplate->displayPagina();
	
			$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
	
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include($this->piede);
		}
	}

	public function go() {}
	
	private function preparaPagina() {
	
		$_SESSION["azione"] = self::AZIONE_RICERCA_FORNITORE;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaFornitore%";
	}
}	

?>