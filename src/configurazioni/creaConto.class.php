<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'creaConto.template.php';
require_once 'ricercaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';


class CreaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
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
		if (!isset($_SESSION[self::CREA_CONTO])) $_SESSION[self::CREA_CONTO] = serialize(new CreaConto());
		return unserialize($_SESSION[self::CREA_CONTO]);
	}

	public function start() {
		$this->go();
	}

	public function go() {

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();

		$creaContoTemplate = CreaContoTemplate::getInstance();

		if ($creaContoTemplate->controlliLogici()) {

			if ($this->creaConto($utility,$conto,$sottoconto)) {
				$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_CONTO_OK;
			}
			else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREAZIONE_CONTO;
		}
		else $_SESSION[self::MSG_DA_CREAZIONE] = $_SESSION[self::MESSAGGIO];

		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaConto::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}

	public function creaConto($utility,$conto,$sottoconto)
	{
		$db = Database::getInstance();
		$db->beginTransaction();

		if ($conto->inserisci($db)) {

			foreach ($sottoconto->getNuoviSottoconti() as $unSottoconto) {

				$sottoconto->setCodConto($conto->getCodConto());
				$sottoconto->setCodSottoconto($unSottoconto[Sottoconto::COD_SOTTOCONTO]);
				$sottoconto->setDesSottoconto($unSottoconto[Sottoconto::DES_SOTTOCONTO]);

				$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

				if (!$sottoconto->inserisci($db)) {
					$db->rollbackTransaction();
					$_SESSION[self::MESSAGGIO] = self::ERRORE_CREAZIONE_CONTO;
					return FALSE;
				}
			}

			$db->commitTransaction();
			$sottoconto->preparaNuoviSottoconti();	// svuoto l'array dei sottoconti inseriti
			return TRUE;
		}
		$db->rollbackTransaction();
		$_SESSION[self::MESSAGGIO] = self::ERRORE_CREAZIONE_CONTO;
		return FALSE;
	}

	public function preparaPagina($creaContoTemplate) {

		$creaContoTemplate->setAzione(self::AZIONE_CREA_CONTO);
		$creaContoTemplate->setConfermaTip("%ml.confermaCreaConto%");
		$creaContoTemplate->setTitoloPagina("%ml.creaNuovoConto%");
	}
}

?>