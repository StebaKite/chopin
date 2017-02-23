<?php

require_once "anagrafica.abstract.class.php";
require_once "anagrafica.business.interface.php";
require_once "ricercaFornitore.template.php";
require_once "utility.class.php";
require_once "database.class.php";

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
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		unset($_SESSION[self::FORNITORI]);
	
		$ricercaFornitoreTemplate = RicercaFornitoreTemplate::getInstance();
	
		if ($this->ricercaDati($utility)) {
	
			$this->preparaPagina($ricercaFornitoreTemplate);
				
			$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);
			
			$ricercaFornitoreTemplate->displayPagina();
	
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			*/
			if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovati " . $_SESSION[self::QTA_FORNITORI] . " fornitori";
				unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
			}
			else {
				$_SESSION[self::MESSAGGIO] = "Trovati " . $_SESSION[self::QTA_FORNITORI] . " fornitori";
			}
	
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
	
			if ($_SESSION[self::QTA_FORNITORI] > 0) {
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
	
	private function ricercaDati($utility) {
		
		$replace = array();
	
		$array = $utility->getConfig();
		$sqlTemplate = $this->root . $array['query'] . self::QUERY_RICERCA_FORNITORE;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION[self::FORNITORI] = $result;
		}
		else {
			unset($_SESSION[self::FORNITORI]);
			$_SESSION[self::QTA_FORNITORI] = 0;
		}
		return $result;
	}
	
	private function preparaPagina() {
	
		$_SESSION["azione"] = self::AZIONE_RICERCA_FORNITORE;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaFornitore%";
	}
}	

?>