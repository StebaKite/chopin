<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaMercato.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'mercato.class.php';

class RicercaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
		if (!isset($_SESSION[self::RICERCA_MERCATO])) $_SESSION[self::RICERCA_MERCATO] = serialize(new RicercaMercato());
		return unserialize($_SESSION[self::RICERCA_MERCATO]);
	}

	public function start() {
	
		// Template
		
		$mercato = Mercato::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$ricercaMercatoTemplate = RicercaMercatoTemplate::getInstance();
		
		$mercato->setMercati(null);

		if ($mercato->load(Database::getInstance())) {
			
			$_SESSION[self::MERCATO] = serialize($mercato);
	
			$this->preparaPagina();

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);

			$ricercaMercatoTemplate->displayPagina();

			if (isset($_SESSION["messaggioCreazione"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioCreazione"] . "<br>" . "Trovati " . $mercato->getQtaMercati() . " mercati";
				unset($_SESSION["messaggioCreazione"]);
			}
			elseif (isset($_SESSION["messaggioCancellazione"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovati " . $mercato->getQtaMercati() . " mercati";
				unset($_SESSION["messaggioCancellazione"]);
			}
			elseif (isset($_SESSION["messaggioModifica"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioModifica"] . "<br>" . "Trovati " . $mercato->getQtaMercati() . " mercati";
				unset($_SESSION["messaggioModifica"]);
			}
			else $_SESSION["messaggio"] = "Trovati " . $mercato->getQtaMercati() . " mercati";
			
			
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
	
			if ($mercato->getQtaMercati() > 0) {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			}
	
			echo $utility->tailTemplate($template);
	
			include($this->piede);
		}
		else {
	
			$this->preparaPagina($ricercaMercatoTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
				
			$ricercaMercatoTemplate->displayPagina();
	
			$_SESSION["messaggio"] = self::ERRORE_LETTURA;
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include($this->piede);
		}
	}
	
	public function go() {}
	
	private function preparaPagina() {
	
		$_SESSION["azione"] = self::AZIONE_RICERCA_MERCATO;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaMercato%";	
	}
}
	
?>	