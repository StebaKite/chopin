<?php

require_once 'configurazioni.abstract.class.php';

class ModificaProgressivoFattura extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneModificaProgressivoFattura = "../configurazioni/modificaProgressivoFatturaFacade.class.php?modo=go";

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

			self::$_instance = new ModificaProgressivoFattura();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaProgressivoFattura.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		$this->prelevaProgressivoFattura($utility);
		
		$modificaProgressivoFatturaTemplate = ModificaProgressivoFatturaTemplate::getInstance();
		$this->preparaPagina($modificaProgressivoFatturaTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaProgressivoFatturaTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {
	
		require_once 'modificaProgressivoFattura.template.php';
		require_once 'utility.class.php';
	
		$utility = Utility::getInstance();	
		$modificaProgressivoFatturaTemplate = ModificaProgressivoFatturaTemplate::getInstance();
	
		if ($modificaProgressivoFatturaTemplate->controlliLogici()) {
	
			// Aggiornamento del DB ------------------------------
	
			if ($this->aggiornaProgressivoFattura($utility)) {
	
				$_SESSION["messaggio"] = "Progressivo fattura salvato con successo";
	
				$this->preparaPagina($modificaProgressivoFatturaTemplate);
	
				include(self::$testata);
				$modificaProgressivoFatturaTemplate->displayPagina();
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($modificaProgressivoFatturaTemplate);
					
				include(self::$testata);
				$modificaProgressivoFatturaTemplate->displayPagina();
					
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
	}
	
	public function prelevaProgressivoFattura($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiProgressivoFattura($db, $utility, $_SESSION["catcliente"], $_SESSION["codneg"]);
	
		if ($result) {
	
			$progressivo = pg_fetch_all($result);
			foreach ($progressivo as $row) {
	
				$_SESSION["numfatt"] = $row["num_fattura_ultimo"];
				$_SESSION["notatesta"] = $row["nota_testa_fattura"];
				$_SESSION["notapiede"] = $row["nota_piede_fattura"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati progressivo fattura : " . $_SESSION["catcliente"] . "-" . $_SESSION["codneg"] + " <<<<<<<<" );
		}
	}

	public function aggiornaProgressivoFattura($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
		$db->beginTransaction();
		
		if ($this->updateProgressivoFattura($db, $utility, $_SESSION["catcliente"], $_SESSION["codneg"], $_SESSION["numfatt"], $_SESSION["notatesta"], $_SESSION["notapiede"])) {
	
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento progressivo fattura, eseguito Rollback");
			return FALSE;
		}
	}
	
	public function preparaPagina($modificaProgressivoFatturaTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaProgressivoFatturaTemplate->setAzione(self::$azioneModificaProgressivoFattura);
		$modificaProgressivoFatturaTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaProgressivoFatturaTemplate->setTitoloPagina("%ml.modificaProgressivoFattura%");
	}
}

?>