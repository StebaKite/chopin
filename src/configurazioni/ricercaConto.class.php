<?php

require_once 'configurazioni.abstract.class.php';

class RicercaConto extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneRicercaConto = "../configurazioni/ricercaContoFacade.class.php?modo=go";
	public static $queryRicercaConto = "/configurazioni/ricercaConto.sql";

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new RicercaConto();

		return self::$_instance;
	}

	public function start() {

		require_once 'ricercaConto.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		unset($_SESSION["contiTrovati"]);
		
		$ricercaContoTemplate = RicercaContoTemplate::getInstance();
		$this->preparaPagina($ricercaContoTemplate);
		
		// compone la pagina
		include($testata);
		$ricercaContoTemplate->displayPagina();
		include($piede);
	}

	public function go() {

		require_once 'ricercaConto.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		unset($_SESSION["contiTrovati"]);
		
		$ricercaContoTemplate = RicercaContoTemplate::getInstance();
		
		if ($this->ricercaDati($utility)) {
			
			$this->preparaPagina($ricercaContoTemplate);
			
			include($testata);
			$ricercaContoTemplate->displayPagina();
				
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			 */
			if (isset($_SESSION["messaggioCancellazione"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovati " . $_SESSION['numContiTrovati'] . " conti";
				unset($_SESSION["messaggioCancellazione"]);
			}
			else {
				$_SESSION["messaggio"] = "Trovati " . $_SESSION['numContiTrovati'] . " conti";
			}
			
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			
			if ($_SESSION['numContiTrovati'] > 0) {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			}
				
			echo $utility->tailTemplate($template);

			include($piede);	
		}
		else {

			$this->preparaPagina($ricercaContoTemplate);
				
			include(self::$testata);
			$ricercaContoTemplate->displayPagina();
			
			$_SESSION["messaggio"] = "Errore fatale durante la lettura dei conti" ;
			
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
				
			include(self::$piede);
		}
				
		
	}
	
	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
		
		$categoriaCond = "";
		$tipcontoCond = "";
		
		if ($_SESSION["categoria"] != "") {
			$categoriaCond = "and cat_conto = '" . $_SESSION["categoria"] . "'";   
		}
		if ($_SESSION["tipoconto"] != "") {
			$tipcontoCond = "and tip_conto = '" . $_SESSION["tipoconto"] . "'";
		}
		
		$replace = array(
				'%categoria%' => $categoriaCond,
				'%tipconto%' => $tipcontoCond
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaConto;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['contiTrovati'] = $result;
		}
		else {
			unset($_SESSION['contiTrovati']);
			$_SESSION['numContiTrovati'] = 0;
		}
	
		return $result;
	}
	
	public function preparaPagina($ricercaContoTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaConto;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaConto%";
	}		
}	
		
?>