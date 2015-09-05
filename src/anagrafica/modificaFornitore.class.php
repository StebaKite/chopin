<?php

require_once 'anagrafica.abstract.class.php';

class ModificaFornitore extends AnagraficaAbstract {

	private static $_instance = null;

	public static $azioneModificaFornitore = "../anagrafica/modificaFornitoreFacade.class.php?modo=go";

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

			self::$_instance = new ModificaFornitore();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaFornitore.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		$this->prelevaFornitore($utility);
		
		$modificaFornitoreTemplate = ModificaFornitoreTemplate::getInstance();
		$this->preparaPagina($modificaFornitoreTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaFornitoreTemplate->displayPagina();
		include(self::$piede);				
	}
	
	public function go() {
	
		require_once 'modificaFornitore.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$modificaFornitoreTemplate = ModificaFornitoreTemplate::getInstance();
		
		if ($modificaFornitoreTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->aggiornaFornitore($utility)) {
		
				$_SESSION["messaggio"] = "Fornitore salvato con successo";
		
				$this->preparaPagina($modificaFornitoreTemplate);
		
				include(self::$testata);
				$modificaFornitoreTemplate->displayPagina();
		
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($modificaFornitoreTemplate);
					
				include(self::$testata);
				$modificaFornitoreTemplate->displayPagina();
					
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
	}

	public function prelevaFornitore($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->prelevaIdFornitore($db, $utility, $_SESSION["idfornitore"]);
	
		if ($result) {
	
			$fornitore = pg_fetch_all($result);
			foreach ($fornitore as $row) {
	
				$_SESSION["codfornitore"] = $row["cod_fornitore"];
				$_SESSION["desfornitore"] = $row["des_fornitore"];
				$_SESSION["indfornitore"] = $row["des_indirizzo_fornitore"];
				$_SESSION["cittafornitore"] = $row["des_citta_fornitore"];
				$_SESSION["capfornitore"] = $row["cap_fornitore"];
				$_SESSION["tipoaddebito"] = $row["tip_addebito"];
				$_SESSION["numggscadenzafattura"] = $row["num_gg_scadenza_fattura"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati fornitore : " . $_SESSION["idfornitore"] . " <<<<<<<<" );
		}
	}

	public function aggiornaFornitore($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
	
		$idfornitore = $_SESSION["idfornitore"];
		$codfornitore = $_SESSION["codfornitore"];
		$desfornitore = $_SESSION["desfornitore"];		
		$indfornitore = ($_SESSION["indfornitore"] != "") ? "'" . $_SESSION["indfornitore"] . "'" : "null" ;
		$cittafornitore = ($_SESSION["cittafornitore"] != "") ? "'" . $_SESSION["cittafornitore"] . "'" : "null" ;
		$capfornitore = ($_SESSION["capfornitore"] != "") ? "'" . $_SESSION["capfornitore"] . "'" : "null" ;
		$tipoaddebito = $_SESSION["tipoaddebito"];
		$numggscadenzafattura = $_SESSION["numggscadenzafattura"];
	
		if ($this->updateFornitore($db, $utility, $idfornitore, $codfornitore, $desfornitore, $indfornitore, $cittafornitore, $capfornitore, $tipoaddebito, $numggscadenzafattura)) {
	 
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento fornitore, eseguito Rollback");
			return FALSE;
		}
	}
	
	public function preparaPagina($modificaFornitoreTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaFornitoreTemplate->setAzione(self::$azioneModificaFornitore);
		$modificaFornitoreTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaFornitoreTemplate->setTitoloPagina("%ml.modificaFornitore%");
	}
}
		
?>