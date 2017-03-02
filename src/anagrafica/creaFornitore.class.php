<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'creaFornitore.template.php';
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

			// Aggiornamento del DB ------------------------------

			if ($this->creaFornitore()) {

				$fornitore = Fornitore::getInstance();
				$fornitore->prepara();

				$_SESSION[self::FORNITORE] = serialize($fornitore);

				$_SESSION["messaggio"] = self::CREA_FORNITORE_OK;

				$this->preparaPagina($creaFornitoreTemplate);

				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
				$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
				echo $this->utility->tailTemplate($template);

				$creaFornitoreTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $this->utility->tailFile($this->utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $this->utility->tailTemplate($template);

				include($this->piede);
			}
			else {

				$this->preparaPagina($creaFornitoreTemplate);

				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
				echo $utility->tailTemplate($template);

				$creaFornitoreTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);

				include($this->piede);
			}
		}
		else {

			$this->preparaPagina($creaFornitoreTemplate);

			$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);

			$creaFornitoreTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include($this->piede);
		}
	}

	private function creaFornitore()
	{
		$fornitore = Fornitore::getInstance();

		$db = Database::getInstance();
		$db->beginTransaction();

		if ($fornitore->inserisci($db)) {

			$db->commitTransaction();
			return TRUE;
		}
		$db->rollbackTransaction();
		$_SESSION[self::MESSAGGIO] = self::ERRORE_CREA_FORNITORE;
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
