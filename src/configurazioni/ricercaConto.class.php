<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaConto.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';

class RicercaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
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
		if (!isset($_SESSION[self::RICERCA_CONTO])) $_SESSION[self::RICERCA_CONTO] = serialize(new RicercaConto());
		return unserialize($_SESSION[self::RICERCA_CONTO]);
	}

	public function start() {

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$conto->setConti(null);
		$sottoconto->preparaNuoviSottoconti();
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

		$ricercaContoTemplate = RicercaContoTemplate::getInstance();
		$this->preparaPagina($ricercaContoTemplate);

		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);

		$ricercaContoTemplate->displayPagina();
		include($this->piede);
	}

	public function go() {

		$conto = Conto::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$ricercaContoTemplate = RicercaContoTemplate::getInstance();		
		$this->preparaPagina($ricercaContoTemplate);
		
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);
		
		if ($conto->load($db)) {

			$_SESSION[self::CONTO] = serialize($conto);
			$_SESSION[self::MESSAGGIO] = "Trovati " . $conto->getQtaConti() . " conti";

			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);			
			$_SESSION[self::MSG] = $utility->tailTemplate($template);			
		}
		else {

			$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;

			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		}		
		$ricercaContoTemplate->displayPagina();
		
		include($this->piede);
	}

	public function preparaPagina($ricercaContoTemplate) {

		$_SESSION[self::AZIONE] = self::AZIONE_RICERCA_CONTO;
		$_SESSION[self::TIP_CONFERMA] = "%ml.cercaTip%";
		$_SESSION[self::TITOLO_PAGINA] = "%ml.ricercaConto%";
	}
}

?>
