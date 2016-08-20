<?php

require_once 'primanota.abstract.class.php';

class VisualizzaCorrispettivo extends primanotaAbstract {

	private static $_instance = null;
	private static $categoria_causali = 'GENERI';

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
			self::$_instance = new VisualizzaCorrispettivo();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'visualizzaCorrispettivo.template.php';
		require_once 'utility.class.php';
		
		$utility = Utility::getInstance();
		$this->prelevaDatiRegistrazione($utility);
		$this->prelevaDatiDettagliRegistrazione($utility);
		
		$visualizzaCorrispettivoTemplate = VisualizzaCorrispettivoTemplate::getInstance();
		$this->preparaPagina($visualizzaCorrispettivoTemplate);
			
		// Compone la pagina
		include(self::$testata);
		$visualizzaCorrispettivoTemplate->displayPagina();
		include(self::$piede);	
	}

	public function prelevaDatiRegistrazione($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiRegistrazione($db, $utility, $_SESSION["idRegistrazione"]);
	
		if ($result) {
	
			$registrazione = pg_fetch_all($result);
			foreach ($registrazione as $row) {
	
				$_SESSION["descreg"] = $row["des_registrazione"];
				$_SESSION["datareg"] = $row["dat_registrazione"];
				$_SESSION["codneg"] = $row["cod_negozio"];
				$_SESSION["causale"] = $row["cod_causale"];								
				$_SESSION["idmercato"] = $row["id_mercato"];
			}
		}
		else {
			error_log(">>>>>> Errore prelievo dati registrazione : " . $_SESSION["idRegistrazione"] . " <<<<<<<<" );
		}
	}
	
	public function prelevaDatiDettagliRegistrazione($utility) {
	
		require_once 'database.class.php';
	
		$db = Database::getInstance();
	
		$result = $this->leggiDettagliRegistrazione($db, $utility, $_SESSION["idRegistrazione"]);
	
		if ($result) {
			$_SESSION["elencoDettagliRegistrazione"] = $result;
		}
		else {
			error_log(">>>>>> Errore prelievo dati registrazione (dettagli) : " . $_SESSION["idRegistrazione"] . " <<<<<<<<" );
		}
	}

	public function preparaPagina($modificaCorrispettivoTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$modificaCorrispettivoTemplate->setConfermaTip("%ml.salvaTip%");
		$modificaCorrispettivoTemplate->setTitoloPagina("%ml.visualizzaCorrispettivoMercato%");
	
		$db = Database::getInstance();
		$utility = Utility::getInstance();
	
		// Prelievo dei dati per i combo --------------------------------------------------------
	
		$_SESSION['elenco_causali'] = $this->caricaCausali($utility, $db, self::$categoria_causali);
		$_SESSION['elenco_mercati'] = $this->caricaMercati($utility, $db);
	}
}		
		
?>