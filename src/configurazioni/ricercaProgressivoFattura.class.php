<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaProgressivoFattura.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';


class RicercaProgressivoFattura extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::RICERCA_PROGRESSIVO_FATTURA])) $_SESSION[self::RICERCA_PROGRESSIVO_FATTURA] = serialize(new RicercaProgressivoFattura());
		return unserialize($_SESSION[self::RICERCA_PROGRESSIVO_FATTURA]);
	}

	public function start()
	{
		$this->go();
	}

	public function go()
	{
		$progressivoFattura = ProgressivoFattura::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();	
		$ricercaProgressivoFatturaTemplate = RicercaProgressivoFatturaTemplate::getInstance();

		$this->preparaPagina($ricercaProgressivoFatturaTemplate);

		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		if ($this->refreshProgressiviFattura($db, $progressivoFattura)) {
							
			$ricercaProgressivoFatturaTemplate->displayPagina();

			if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovati " . $progressivoFattura->getQtaProgressiviFattura() . " progressivi fattura";
				unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
			}
			elseif (isset($_SESSION[self::MSG_DA_CREAZIONE])) {
				$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CREAZIONE] . "<br>" . "Trovati " . $progressivoFattura->getQtaProgressiviFattura() . " progressivi fattura";
				unset($_SESSION[self::MSG_DA_CREAZIONE]);
			}
			else {
				$_SESSION[self::MESSAGGIO] = "Trovati " . $progressivoFattura->getQtaProgressiviFattura() . " progressivi fattura";
			}
				
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				
			if ($progressivoFattura->getQtaProgressiviFattura() > 0) {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			}

			echo $utility->tailTemplate($template);
			
			include($this->piede);
		}
		else {
	
			$this->preparaPagina($ricercaProgressivoFatturaTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);
				
			$ricercaProgressivoFatturaTemplate->displayPagina();
	
			$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;
	
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
			
			include($this->piede);
		}
	}

	private function refreshProgressiviFattura($db, $progressivoFattura) {
	
		if (sizeof($progressivoFattura->getProgressiviFattura()) == 0) {
	
			if (!$progressivoFattura->load($db)) {
				$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;
				return false;
			}
			$_SESSION[self::PROGRESIVO_FATTURA] = serialize($progressivoFattura);
		}
		return true;
	}
	
	public function preparaPagina($ricercaProgressivoFatturaTemplate) {
	
		$_SESSION[self::AZIONE] = self::AZIONE_RICERCA_PROGRESSIVO_FATTURA;
		$_SESSION[self::TIP_CONFERMA] = "%ml.cercaTip%";
		$_SESSION[self::TITOLO_PAGINA] = "%ml.ricercaProgressivoFattura%";
	}
}

?>