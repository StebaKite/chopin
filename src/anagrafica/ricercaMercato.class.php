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
		
		$this->preparaPagina($ricercaMercatoTemplate);
		
		$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		$mercato->setMercati(null);

		if ($mercato->load(Database::getInstance())) {
			
			$_SESSION[self::MERCATO] = serialize($mercato);
	
			$_SESSION["messaggio"] = "Trovati " . $mercato->getQtaMercati() . " mercati";			
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			
			$pos = strpos($_SESSION[self::MESSAGGIO],"ERRORE");
			if ($pos === false) {
				if ($mercato->getQtaMercati() > 0)
					$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
					else $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			}
			else $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			
			$_SESSION[self::MSG] = $utility->tailTemplate($template);
		}
		else {
			$_SESSION["messaggio"] = self::ERRORE_LETTURA;
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		}
		$ricercaMercatoTemplate->displayPagina();
		
		include($this->piede);	
	}
	
	public function go() {}
	
	private function preparaPagina() {
	
		$_SESSION["azione"] = self::AZIONE_RICERCA_MERCATO;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaMercato%";	
	}
}
	
?>	