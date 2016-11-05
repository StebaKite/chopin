<?php

require_once 'configurazioni.abstract.class.php';

class RicercaCausale extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneRicercaCausale = "../configurazioni/ricercaCausaleFacade.class.php?modo=go";
	public static $queryRicercaCausale = "/configurazioni/ricercaCausale.sql";

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

			self::$_instance = new RicercaCausale();

		return self::$_instance;
	}

	public function start() {

		require_once 'ricercaCausale.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		unset($_SESSION["causaliTrovate"]);
		$_SESSION["codcausale"] = "";

		$ricercaCausaleTemplate = RicercaCausaleTemplate::getInstance();
		$this->preparaPagina($ricercaCausaleTemplate);

		// compone la pagina
			
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);
		$ricercaCausaleTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'ricercaCausale.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		unset($_SESSION["causaliTrovate"]);
		
		$ricercaCausaleTemplate = RicercaCausaleTemplate::getInstance();
		
		if ($this->ricercaDati($utility)) {
				
			$this->preparaPagina($ricercaCausaleTemplate);
				
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$ricercaCausaleTemplate->displayPagina();
		
			/**
			 * Gestione del messaggio proveniente dalla cancellazione
			*/
			if (isset($_SESSION["messaggioCancellazione"])) {
				$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovate " . $_SESSION['numCausaliTrovate'] . " causali";
				unset($_SESSION["messaggioCancellazione"]);
			}
			else {
				$_SESSION["messaggio"] = "Trovate " . $_SESSION['numCausaliTrovate'] . " causali";
			}
				
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				
			if ($_SESSION['numCausaliTrovate'] > 0) {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			}
		
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
		else {
		
			$this->preparaPagina($ricercaCausaleTemplate);
		
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
			$ricercaCausaleTemplate->displayPagina();
				
			$_SESSION["messaggio"] = "Errore fatale durante la lettura delle causali" ;
				
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}		
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';

		$causale = "";
		
		if ($_SESSION['causale'] != "") {
			$causale = "WHERE causale.cod_causale = '" . $_SESSION['causale'] . "'";
		}
		
		$replace = array(
				'%cod_causale%' => $causale
		);
		
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaCausale;
		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		
		// esegue la query
		
		$db = Database::getInstance();
		$result = $db->getData($sql);
		
		if (pg_num_rows($result) > 0) {
			$_SESSION['causaliTrovate'] = $result;
		}
		else {
			unset($_SESSION['causaliTrovate']);
			$_SESSION['numCausaliTrovate'] = 0;
		}		
		return $result;
	}

	public function preparaPagina($ricercaCausaleTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaCausale;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaCausale%";
	}
}

?>