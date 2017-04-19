<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'configuraCausale.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'configurazioneCausale.class.php';

class ConfiguraCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::CONFIGURA_CAUSALE])) $_SESSION[self::CONFIGURA_CAUSALE] = serialize(new ConfiguraCausale());
		return unserialize($_SESSION[self::CONFIGURA_CAUSALE]);
	}

	public function start() {

		$configurazioneCausale = ConfigurazioneCausale::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$this->refreshContiConfigurati($db, $configurazioneCausale);
		$this->refreshContiConfigurabili($db, $configurazioneCausale);

		$_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);

		$configuraCausaleTemplate = ConfiguraCausaleTemplate::getInstance();
		$this->preparaPagina($configuraCausaleTemplate);

		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);

		$configuraCausaleTemplate->displayPagina();
		include($this->piede);
	}

	public function go() {}

	private function refreshContiConfigurati($db, $configurazioneCausale)
	{
		if (sizeof($configurazioneCausale->getContiConfigurati()) == 0) {

			if (!$configurazioneCausale->loadContiConfigurati($db)) {
				$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;
				return false;
			}
			$_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
		}
		return true;
	}

	private function refreshContiConfigurabili($db, $configurazioneCausale)
	{
		if (sizeof($configurazioneCausale->getContiConfigurabili()) == 0) {

			if (!$configurazioneCausale->loadContiConfigurabili($db)) {
				$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;
				return false;
			}
			$_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
		}
		return true;
	}

	public function preparaPagina($configuraCausaleTemplate) {

		$configuraCausaleTemplate->setTitoloPagina("%ml.configuraCausale%");
	}
}

?>