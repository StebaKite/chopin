<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
require_once 'creaFornitore.template.php';
require_once 'ricercaFornitore.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class CreaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
		if (!isset($_SESSION[self::CREA_FORNITORE])) $_SESSION[self::CREA_FORNITORE] = serialize(new CreaFornitore());
		return unserialize($_SESSION[self::CREA_FORNITORE]);
	}

	// ------------------------------------------------

	public function start()
	{
		$creaFornitoreTemplate = CreaFornitoreTemplate::getInstance();
		$this->preparaPagina($creaFornitoreTemplate);

		$fornitore = Fornitore::getInstance();
		$fornitore->prepara();

		$_SESSION[self::FORNITORE] = serialize($fornitore);

		// Compongo la pagina
		$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
		$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
		echo $this->utility->tailTemplate($template);

		$creaFornitoreTemplate->displayPagina();
		include($this->piede);
	}

	public function go()
	{
		$creaFornitoreTemplate = CreaFornitoreTemplate::getInstance();

		if ($creaFornitoreTemplate->controlliLogici()) {

			if ($this->creaFornitore()) {
				$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_FORNITORE_OK;
			}
			else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREA_FORNITORE;
		}
		else $_SESSION[self::MSG_DA_CREAZIONE] = $_SESSION[self::MESSAGGIO];

		$_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaFornitore::getInstance()));
		$controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
		$controller->start();
	}

	private function creaFornitore()
	{
		$fornitore = Fornitore::getInstance();

		$descrizione = ($fornitore->getDesFornitore() != "") ? str_replace("'","''",$fornitore->getDesFornitore()) : "" ;
		$fornitore->setDesFornitore($descrizione);

		$_SESSION[self::FORNITORE] = serialize($fornitore);
		
		$db = Database::getInstance();
		$db->beginTransaction();

		if ($fornitore->inserisci($db)) {

			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		return FALSE;
	}

	private function preparaPagina($creaFornitoreTemplate)
	{
		$creaFornitoreTemplate->setAzione(self::AZIONE_CREA_FORNITORE);
		$creaFornitoreTemplate->setConfermaTip("%ml.confermaCreaFornitore%");
		$creaFornitoreTemplate->setTitoloPagina("%ml.creaNuovoFornitore%");
	}
}

?>
