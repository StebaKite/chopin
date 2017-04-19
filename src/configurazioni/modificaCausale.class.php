<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'modificaCausale.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class ModificaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::MODIFICA_CAUSALE])) $_SESSION[self::MODIFICA_CAUSALE] = serialize(new ModificaCausale());
		return unserialize($_SESSION[self::MODIFICA_CAUSALE]);
	}

	public function start()
	{
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();

		$causale->leggi($db);
		$_SESSION[self::CAUSALE] = serialize($causale);

		$modificaCausaleTemplate = ModificaCausaleTemplate::getInstance();
		$this->preparaPagina($modificaCausaleTemplate);

		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);

		$modificaCausaleTemplate->displayPagina();
		include($this->piede);
	}

	public function go()
	{
		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		$modificaCausaleTemplate = ModificaCausaleTemplate::getInstance();

		$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);

		$this->preparaPagina($modificaCausaleTemplate);

		if ($modificaCausaleTemplate->controlliLogici()) {

			if ($causale->aggiorna($db)) {
				$_SESSION[self::MESSAGGIO] = self::AGGIORNA_CAUSALE_OK;
				$modificaCausaleTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
			}
			else {
				$this->preparaPagina($modificaCausaleTemplate);
				$modificaCausaleTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
			}
		}
		include($this->piede);
	}

	public function preparaPagina($modificaCausaleTemplate)
	{
		$modificaCausaleTemplate->setAzione(self::AZIONE_MODIFICA_CAUSALE);
		$modificaCausaleTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaCausaleTemplate->setTitoloPagina("%ml.modificaCausale%");
	}
}

?>