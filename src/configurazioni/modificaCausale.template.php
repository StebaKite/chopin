<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'causale.class.php';
require_once 'utility.class.php';

class ModificaCausaleTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface
{
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
		if (!isset($_SESSION[self::MODIFICA_CAUSALE_TEMPLATE])) $_SESSION[self::MODIFICA_CAUSALE_TEMPLATE] = serialize(new ModificaCausaleTemplate());
		return unserialize($_SESSION[self::MODIFICA_CAUSALE_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {

		$causale = Causale::getInstance();
		$esito = TRUE;
		$msg = "<br>";

		if ($causale->getDesCausale() == "") {
			$msg .= self::ERRORE_DESCRIZIONE_CAUSALE;
			$esito = FALSE;
		}

		if ($msg != "<br>") {
			$_SESSION[self::MESSAGGIO] = $msg;
		}
		else {
			unset($_SESSION[self::MESSAGGIO]);
		}
		return $esito;
	}

	public function displayPagina() {

		$causale = Causale::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_MODIFICA_CAUSALE;

		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%causale%' => $_SESSION["causale"],
				'%codcausale%' => $causale->getCodCausale(),
				'%descausale%' => $causale->getDesCausale(),
				'%generi_checked%' => (trim($causale->getCatCausale()) == "GENERI") ? "checked" : "",
				'%incpag_checked%' => (trim($causale->getCatCausale()) == "INCPAG") ? "checked" : "",
				'%corris_checked%' => (trim($causale->getCatCausale()) == "CORRIS") ? "checked" : ""
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>