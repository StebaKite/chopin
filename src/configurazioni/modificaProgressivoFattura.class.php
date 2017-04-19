<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'modificaProgressivoFattura.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';

class ModificaProgressivoFattura extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA])) $_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA] = serialize(new ModificaProgressivoFattura());
		return unserialize($_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA]);
	}

	public function start()
	{
		
		$progressivoFattura = ProgressivoFattura::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$progressivoFattura->leggi($db);
		$_SESSION[self::PROGRESIVO_FATTURA] = serialize($progressivoFattura);
		
		$modificaProgressivoFatturaTemplate = ModificaProgressivoFatturaTemplate::getInstance();
		$this->preparaPagina($modificaProgressivoFatturaTemplate);
			
		// Compone la pagina
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		$modificaProgressivoFatturaTemplate->displayPagina();
		include($this->piede);
	}

	public function go()
	{
		$progressivoFattura = ProgressivoFattura::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();	
		
		$modificaProgressivoFatturaTemplate = ModificaProgressivoFatturaTemplate::getInstance();
		$this->preparaPagina($modificaProgressivoFatturaTemplate);
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		if ($modificaProgressivoFatturaTemplate->controlliLogici()) {
	
			if ($progressivoFattura->update($db)) {
	
				$_SESSION[self::MESSAGGIO] = self::AGGIORNA_PROGRESSIVO_OK;
				$modificaProgressivoFatturaTemplate->displayPagina();
	
				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
			}
			else {
				$modificaProgressivoFatturaTemplate->displayPagina();
				
				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
			}
		}
		include($this->piede);
	}
	
	public function preparaPagina($modificaProgressivoFatturaTemplate)
	{
		$modificaProgressivoFatturaTemplate->setAzione(self::AZIONE_MODIFICA_PROGRESSIVO_FATTURA);
		$modificaProgressivoFatturaTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaProgressivoFatturaTemplate->setTitoloPagina("%ml.modificaProgressivoFattura%");
	}
}

?>