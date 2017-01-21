<?php

require_once 'anagrafica.abstract.class.php';

class CreaCliente extends AnagraficaAbstract {

	public static $_instance = null;

	public static $azioneCreaCliente = "../anagrafica/creaClienteFacade.class.php?modo=go";

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

			self::$_instance = new CreaCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'creaCliente.template.php';

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$creaClienteTemplate = CreaClienteTemplate::getInstance();
		$this->preparaPagina($creaClienteTemplate);

		$_SESSION["codcliente"] = $this->prelevaUltimoCodiceCliente($utility, $db) + 1;
		unset($_SESSION["descliente"]);
		unset($_SESSION["indcliente"]);
		unset($_SESSION["cittacliente"]);
		unset($_SESSION["capcliente"]);
		unset($_SESSION["codpiva"]);
		unset($_SESSION["codfisc"]);
		
		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$creaClienteTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'creaCliente.template.php';
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();

		$creaClienteTemplate = CreaClienteTemplate::getInstance();

		if ($creaClienteTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------

			if ($this->creaCliente($utility)) {

				$db = Database::getInstance();
				$utility = Utility::getInstance();
				
				$_SESSION["codcliente"] = $this->prelevaUltimoCodiceCliente($utility, $db) + 1;
				unset($_SESSION["descliente"]);
				unset($_SESSION["indcliente"]);
				unset($_SESSION["cittacliente"]);
				unset($_SESSION["capcliente"]);
				unset($_SESSION["codpiva"]);
				unset($_SESSION["codfisc"]);
				
				$_SESSION["messaggio"] = "Cliente salvato con successo";

				$this->preparaPagina($creaClienteTemplate);

				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);
				
				$creaClienteTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {

				$this->preparaPagina($creaClienteTemplate);

				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$creaClienteTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);

				include(self::$piede);
			}
		}
		else {

			$this->preparaPagina($creaClienteTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$creaClienteTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);
		}
	}

	public function creaCliente($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();
		$db->beginTransaction();


		$codcliente = $_SESSION["codcliente"];
		$descliente = str_replace("'","''",$_SESSION["descliente"]);
		$indcliente = ($_SESSION["indcliente"] != "") ? "'" . str_replace("'","''",$_SESSION["indcliente"]) . "'" : "null" ;
		$cittacliente = ($_SESSION["cittacliente"] != "") ? "'" . str_replace("'","''",$_SESSION["cittacliente"]) . "'" : "null" ;
		$capcliente = ($_SESSION["capcliente"] != "") ? "'" . $_SESSION["capcliente"] . "'" : "null";
		$tipoaddebito = $_SESSION["tipoaddebito"];

		$codpiva = ($_SESSION["codpiva"] != "") ? "'" . $_SESSION["codpiva"] . "'" : "null" ;
		$codfisc = ($_SESSION["codfisc"] != "") ? "'" . $_SESSION["codfisc"] . "'" : "null" ;
		$catcliente = ($_SESSION["catcliente"] != "") ? "'" . $_SESSION["catcliente"] . "'" : "null" ;
		
		if ($this->inserisciCliente($db, $utility, $codcliente, $descliente, $indcliente, $cittacliente, $capcliente, $tipoaddebito, $codpiva, $codfisc, $catcliente)) {

			$db->commitTransaction();			
			
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento cliente, eseguito Rollback");
		$_SESSION["messaggio"] = "Cliente già esistente, inserimento fallito";
		return FALSE;
	}

	public function preparaPagina($creaClienteTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$creaClienteTemplate->setAzione(self::$azioneCreaCliente);
		$creaClienteTemplate->setConfermaTip("%ml.confermaCreaCliente%");
		$creaClienteTemplate->setTitoloPagina("%ml.creaNuovoCliente%");

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle categorie -------------------------------------------------------------
		
		$_SESSION['elenco_categorie_cliente'] = $this->caricaCategorieCliente($utility, $db);
	}
}

?>