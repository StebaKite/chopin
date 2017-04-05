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
		
		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();
		
		$creaContoTemplate = CreaContoTemplate::getInstance();
		$this->preparaPagina($creaContoTemplate);

		$conto->prepara();
		$_SESSION[self::CONTO] = serialize($conto);

		$sottoconto->preparaNuoviSottoconti();
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
		
		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		$creaContoTemplate->displayPagina();
		include($this->piede);		
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
		else $_SESSION[self::MSG_DA_CREAZIONE] = self::MESSAGGIO;

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
				$sottoconto->setCodSottoconto($unSottoconto[0]);
				$sottoconto->setDesSottoconto($unSottoconto[1]);
				
				$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
				
				if (!$sottoconto->inserisci($db)) {
					$db->rollbackTransaction();
					$_SESSION[self::MESSAGGIO] = self::ERRORE_CREAZIONE_CONTO;
					return FALSE;						
				}
			}
			
			$db->commitTransaction();
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