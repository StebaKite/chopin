<?php

require_once 'primanota.abstract.class.php';

class LoadContiCausale extends PrimanotaAbstract {

	public static $replace;
	public static $queryRicercaConti = "/primanota/ricercaConti.sql";
	public static $queryRicercaContiDescrizione = "/primanota/ricercaContiDescrizione.sql";

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new LoadContiCausale();

			return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		require_once 'configurazioneCausale.php';
		
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$array = $utility->getConfig();

		self::$replace = array(
				'%cod_causale%' => trim($_SESSION["causale"])
		);

		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaConti;

		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), self::$replace);
		$result = $db->getData($sql);

		$conti= array();
		unset($_SESSION['dettagliInseriti']);
		
		/**
		 * Escludo dal caricamento tutti i clienti e i fornitori. 
		 * Perchè verrà caricato nei dettagli solo il conto fornitore/cliente selezioanto.
		 */
		foreach(pg_fetch_all($result) as $row) {	
			if (!strstr($array['contiFornitore'], trim($row['cod_conto'])) && (!strstr($array['contiCliente'], trim($row['cod_conto'])))) {
				$unconto = array($row['cod_conto'], $row['cod_sottoconto'], $row['des_sottoconto']);
				array_push($conti, $unconto);
			}
		}
		$_SESSION['dettagliInseriti'] = $conti;
		echo "Conti_Ok";
	}
}

?>