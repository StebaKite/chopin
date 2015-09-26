<?php

require_once 'anagrafica.abstract.class.php';

class ModificaCliente extends AnagraficaAbstract {

	private static $_instance = null;

	public static $azioneModificaCliente = "../anagrafica/modificaClienteFacade.class.php?modo=go";

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

			self::$_instance = new ModificaCliente();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaCliente.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$this->prelevaCliente($utility);

		$modificaClienteTemplate = ModificaClienteTemplate::getInstance();
		$this->preparaPagina($modificaClienteTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaClienteTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'modificaCliente.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$modificaClienteTemplate = ModificaClienteTemplate::getInstance();

		if ($modificaClienteTemplate->controlliLogici()) {

			// Aggiornamento del DB ------------------------------

			if ($this->aggiornaCliente($utility)) {

				$_SESSION["messaggio"] = "Cliente salvato con successo";

				$this->preparaPagina($modificaClienteTemplate);

				include(self::$testata);
				$modificaClienteTemplate->displayPagina();

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
				
				$_SESSION["messaggio"] = "Errore durante l'aggiornamento del cliente";
				
				$this->preparaPagina($modificaClienteTemplate);
					
				include(self::$testata);
				$modificaClienteTemplate->displayPagina();
					
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
	}

	public function prelevaCliente($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();

		$result = $this->leggiIdCliente($db, $utility, $_SESSION["idcliente"]);

		if ($result) {

			$cliente = pg_fetch_all($result);
			foreach ($cliente as $row) {

				$_SESSION["codcliente"] = $row["cod_cliente"];
				$_SESSION["descliente"] = $row["des_cliente"];
				$_SESSION["indcliente"] = $row["des_indirizzo_cliente"];
				$_SESSION["cittacliente"] = $row["des_citta_cliente"];
				$_SESSION["capcliente"] = $row["cap_cliente"];
				$_SESSION["tipoaddebito"] = $row["tip_addebito"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati cliente : " . $_SESSION["idcliente"] . " <<<<<<<<" );
		}
	}

	public function aggiornaCliente($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();
		$db->beginTransaction();

		$idcliente = $_SESSION["idcliente"];
		$codcliente = $_SESSION["codcliente"];
		$descliente = $_SESSION["descliente"];
		$indcliente = ($_SESSION["indcliente"] != "") ? "'" . $_SESSION["indcliente"] . "'" : "null" ;
		$cittacliente = ($_SESSION["cittacliente"] != "") ? "'" . $_SESSION["cittacliente"] . "'" : "null" ;
		$capcliente = ($_SESSION["capcliente"] != "") ? "'" . $_SESSION["capcliente"] . "'" : "null" ;
		$tipoaddebito = $_SESSION["tipoaddebito"];

		if ($this->updateCliente($db, $utility, $idcliente, $codcliente, $descliente, $indcliente, $cittacliente, $capcliente, $tipoaddebito)) {

			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento cliente, eseguito Rollback");
			return FALSE;
		}
	}

	public function preparaPagina($modificaClienteTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$modificaClienteTemplate->setAzione(self::$azioneModificaCliente);
		$modificaClienteTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaClienteTemplate->setTitoloPagina("%ml.modificaCliente%");
	}
}
?>