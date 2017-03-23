<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'modificaConto.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class ModificaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::MODIFICA_CONTO])) $_SESSION[self::MODIFICA_CONTO] = serialize(new ModificaConto());
		return unserialize($_SESSION[self::MODIFICA_CONTO]);
	}

	public function start()
	{
		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$conto->leggi($db);
		$_SESSION[self::CONTO] = serialize($conto);
		
		$sottoconto->setCodConto($conto->getCodConto());
		$sottoconto->leggi($db);
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
		
		$modificaContoTemplate = ModificaContoTemplate::getInstance();
		$this->preparaPagina($modificaContoTemplate);
			
		// Compone la pagina
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		$modificaContoTemplate->displayPagina();
		
		if (isset($_SESSION["messaggio"])) {
			if ($_SESSION[self::MESSAGGIO] == self::MSG_DA_CREAZIONE_CONTO) {
				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
			}
			else {
				if ($_SESSION[self::MESSAGGIO] == self::ERRORE_CREAZIONE_CONTO) {
					self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
					$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
					echo $utility->tailTemplate($template);						
				}
			}			
			unset($_SESSION[self::MESSAGGIO]);
		}		
		include($this->piede);
	}

	public function go() {

		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();
		
		$sottoConti = "";
		
		foreach ($sottoconto->getSottoconti() as $row) {
			$sott .= trim($row[Sottoconto::COD_SOTTOCONTO]) . "#" . trim($row[Sottoconto::DES_SOTTOCONTO]) . ",";
		}
		$sottoconto->setSottocontiInseriti($sott);
		
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

		$modificaContoTemplate = ModificaContoTemplate::getInstance();
		
		if ($modificaContoTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->aggiornaConto($utility)) {

				$_SESSION[self::MESSAGGIO] = self::MSG_DA_CREAZIONE_CONTO;
				
				$this->preparaPagina($modificaContoTemplate);

				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($this->utility)) : array('%amb%' => $this->getEnvironment ( $this->array, $_SESSION ), '%menu%' => $this->makeMenu($this->utility)));
				$template = $this->utility->tailFile($this->utility->getTemplate($this->testata), $replace);
				echo $this->utility->tailTemplate($template);

				$modificaContoTemplate->displayPagina();
				
				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include($this->piede);				
			}
			else {
					
				$this->preparaPagina($modificaContoTemplate);
			
				$replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
				echo $utility->tailTemplate($template);
				
				$modificaContoTemplate->displayPagina();
			
				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
			
				include($this->piede);
			}
		}		
	}

	public function aggiornaConto($utility) {

		$conto = Conto::getInstance();
		$db = Database::getInstance();
		$db->beginTransaction();

		$conto->setDesConto(str_replace("'","''",$conto->getDesConto()));

		if ($conto->aggiorna($db)) {
		
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			return FALSE;
		}
	}
	
	public function preparaPagina($modificaContoTemplate) {
	
		$modificaContoTemplate->setAzione(self::AZIONE_MODIFICA_CONTO);
		$modificaContoTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaContoTemplate->setTitoloPagina("%ml.modificaConto%");
	}
}		
		
?>