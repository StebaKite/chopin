<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'generaMastrinoConto.template.php';
require_once 'ricercaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'sottoconto.class.php';


class GeneraMastrinoConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
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
		if (!isset($_SESSION[self::ESTRAI_MASTRINO])) $_SESSION[self::ESTRAI_MASTRINO] = serialize(new GeneraMastrinoConto());
		return unserialize($_SESSION[self::ESTRAI_MASTRINO]);
	}

	public function start() { $this->go(); }
	
	public function go() {	
	
		$sottoconto = Sottoconto::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$generaMastrinoContoTemplate = GeneraMastrinoContoTemplate::getInstance();
	
		if ($sottoconto->cercaRegistrazioni($db)) {
	
			$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);				
			
			if ($sottoconto->getQtaRegistrazioniTrovate() > 0) {
				
				$this->preparaPagina($generaMastrinoContoTemplate);

				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
				echo $utility->tailTemplate($template);
				
				$generaMastrinoContoTemplate->displayPagina();
				
				$_SESSION[self::MESSAGGIO] = self::GENERA_MASTRINO_OK;
				
				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
				
				include($this->piede);				
			}
			else {

				$_SESSION[self::MSG_DA_GENERAZIONE_MASTRINO] = self::REGISTRAZIONI_NON_TROVATE;

				$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaConto::getInstance()));
				
				$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
				$controller->setRequest("go");
				$controller->start();
			}
		}
		else {
	
			$this->preparaPagina($generaMastrinoContoTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
			echo $utility->tailTemplate($template);
				
			$generaMastrinoContoTemplate->displayPagina();
	
			$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA ;
	
			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include($this->piede);
		}
	}
	
	public function preparaPagina($generaMastrinoContoTemplate) {
	
		$_SESSION[self::AZIONE] = self::AZIONE_GENERA_MASTRINO;
		$_SESSION[self::TIP_CONFERMA] = "%ml.cercaTip%";
		$_SESSION[self::TITOLO_PAGINA] = "%ml.mastrinoConto%";
	}	
}

?>