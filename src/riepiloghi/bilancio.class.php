<?php

require_once 'riepiloghi.abstract.class.php';

class Bilancio extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $azioneBilancio = "../riepiloghi/bilancioFacade.class.php?modo=go";
	public static $queryBilancio = "/riepiloghi/estraiRegistrazioniBilancio.sql";

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

			self::$_instance = new Bilancio();

		return self::$_instance;
	}

	public function start() {

		require_once 'bilancio.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");
		$_SESSION["codneg_sel"] = "VIL";
		$_SESSION["catconto"] = "Conto Economico";
		
		unset($_SESSION["registrazioniTrovate"]);
		
		$bilancioTemplate = BilancioTemplate::getInstance();
		$this->preparaPagina($bilancioTemplate);
		
		// compone la pagina
		include($testata);
		$bilancioTemplate->displayPagina();
		include($piede);
	}

	public function go() {

		require_once 'bilancio.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$bilancioTemplate = BilancioTemplate::getInstance();
		
		if ($bilancioTemplate->controlliLogici()) {
				
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($bilancioTemplate);
					
				include(self::$testata);
				$bilancioTemplate->displayPagina();
		
				$_SESSION["messaggio"] = "Trovate " . $_SESSION['numRegTrovate'] . " voci";
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
		
				if ($_SESSION['numRegTrovate'] > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}
		
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($bilancioTemplate);
					
				include(self::$testata);
				$bilancioTemplate->displayPagina();
		
				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
		
			$this->preparaPagina($bilancioTemplate);
		
			include(self::$testata);
			$bilancioTemplate->displayPagina();
		
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		
			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {

		require_once 'database.class.php';
				
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"],
				'%codnegozio%' => $_SESSION["codneg_sel"]
		);
		
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryBilancio;
		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		
		// esegue la query
		
		$db = Database::getInstance();
		$result = $db->getData($sql);
		
		if (pg_num_rows($result) > 0) {
			$_SESSION['registrazioniTrovate'] = $result;
		}
		else {
			unset($_SESSION['registrazioniTrovate']);
			$_SESSION['numRegTrovate'] = 0;
		}
		
		return $result;
	}

	public function preparaPagina($bilancioTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneBilancio;
		$_SESSION["confermaTip"] = "%ml.confermaEstraiBilancio%";
		$_SESSION["titoloPagina"] = "%ml.bilancio%";
	}	
}

?>