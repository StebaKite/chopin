<?php

require_once 'configurazioni.abstract.class.php';

class ModificaConto extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneModificaConto = "../configurazioni/modificaContoFacade.class.php?modo=go";

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

			self::$_instance = new ModificaConto();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'modificaConto.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$this->prelevaConto($utility);
		$this->prelevaSottoconti($utility);
		
		$modificaContoTemplate = ModificaContoTemplate::getInstance();
		$this->preparaPagina($modificaContoTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$modificaContoTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'modificaConto.template.php';
		require_once 'utility.class.php';
		
		/**
		 * Crea una array dei sottoconti
		 */
		$utility = Utility::getInstance();
		$this->prelevaSottoconti($utility);

		$result = $_SESSION["elencoSottoconti"];
		
		$sottoconti = pg_fetch_all($result);
		$sott = "";
		
		foreach ($sottoconti as $row) {
			$sott = $sott . trim($row["cod_sottoconto"]) . "#" . trim($row["des_sottoconto"]) . ",";
		}
		$_SESSION['sottocontiInseriti'] = $sott;

		$modificaContoTemplate = ModificaContoTemplate::getInstance();
		
		if ($modificaContoTemplate->controlliLogici()) {
		
			// Aggiornamento del DB ------------------------------
		
			if ($this->aggiornaConto($utility)) {

				$_SESSION["messaggio"] = "Conto salvato con successo";
				
				$this->preparaPagina($modificaContoTemplate);
				
				include(self::$testata);
				$modificaContoTemplate->displayPagina();
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);				
			}
			else {
					
				$this->preparaPagina($modificaContoTemplate);
			
				include(self::$testata);
				$modificaContoTemplate->displayPagina();
			
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
			
				include(self::$piede);
			}
		}		
	}

	public function prelevaConto($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiConto($db, $utility, $_SESSION["codconto"]);
	
		if ($result) {
	
			$conto = pg_fetch_all($result);
			foreach ($conto as $row) {
	
				$_SESSION["desconto"] = $row["des_conto"];
				$_SESSION["catconto"] = $row["cat_conto"];
				$_SESSION["tipconto"] = $row["tip_conto"];
				$_SESSION["indpresenza"] = $row["ind_presenza_in_bilancio"];
				$_SESSION["indvissottoconti"] = $row["ind_visibilita_sottoconti"];
				$_SESSION["numrigabilancio"] = $row["num_riga_bilancio"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati conto : " . $_SESSION["codconto"] . " <<<<<<<<" );
		}
	}

	public function prelevaSottoconti($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiSottoconti($db, $utility, $_SESSION["codconto"]);
	
		if ($result) {
			$_SESSION["elencoSottoconti"] = $result;
		}
		else {
			error_log(">>>>>> Errore prelievo sottoconti del conto : " . $_SESSION["codconto"] . " <<<<<<<<" );
		}
	}

	public function aggiornaConto($utility) {

		require_once 'database.class.php';
		
		$db = Database::getInstance();
		$db->beginTransaction();

		$codconto = $_SESSION["codconto"];
		$desconto = $_SESSION["desconto"];
		$catconto = $_SESSION["catconto"];
		$tipconto = $_SESSION["tipconto"];
		$indpresenza = $_SESSION["indpresenza"];
		$indvissottoconti = $_SESSION["indvissottoconti"];
		$numrigabilancio = $_SESSION["numrigabilancio"];
		

		if ($this->updateConto($db, $utility, $codconto, $desconto, $catconto, $tipconto, $indpresenza, $indvissottoconti, $numrigabilancio)) {
		
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			error_log("Errore aggiornamento conto, eseguito Rollback");
			return FALSE;
		}
	}
	
	public function preparaPagina($modificaContoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaContoTemplate->setAzione(self::$azioneModificaConto);
		$modificaContoTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaContoTemplate->setTitoloPagina("%ml.modificaConto%");
	}
}		
		
?>