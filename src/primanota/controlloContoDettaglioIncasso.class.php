<?php

require_once 'primanota.abstract.class.php';

class ControlloContoDettaglioIncasso extends PrimanotaAbstract {

	public static $queryTrovaDescrizioneCliente = "/anagrafica/trovaDescCliente.sql";
	
	public static $replace;

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

			self::$_instance = new ControlloContoDettaglioIncasso();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$array = $utility->getConfig();
		
		/**
		 * Faccio il controllo del conto solo per i conti clienti 
		 */
		
		if (strstr($array['contiCliente'], trim(substr($_SESSION["conto"],0,3)))) {
			
			/**
			 *  cerco il codice del cliente selezionato
			 */
			
			$cliente = ($_SESSION["cliente"] != "") ? $this->leggiDescrizioneCliente($db, $utility, str_replace("'", "''", $_SESSION["cliente"])) : "null" ;
			
			/**
			 * il codice del cliente deve corrispondere al codice del sottoconto selezionato
			 */
			
			if ($cliente != "null") {
				if (trim(substr($_SESSION["conto"], 3)) != trim($cliente)) {
					echo "Il conto selezionato non appartiene a questo cliente, dettaglio non inserito" ;
				}
				else {
					echo "Dettaglio ok";
				}
			}
			else {
				echo "cliente non esistente, controllo conto dettaglio non eseguito!";
			}
		}
		else {
			echo "Dettaglio ok";
		}		
	}
	
	/**
	 * Questo metodo fa l'override del super perchè restituisce il codice del cliente anzichè il suo ID
	 * @see ChopinAbstract::leggiDescrizioneCliente()
	 */
	public function leggiDescrizioneCliente($db, $utility, $descliente) : string {
	
		$array = $utility->getConfig();
		$replace = array(
				'%des_cliente%' => trim($descliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaDescrizioneCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		$rows = pg_fetch_all($result);
	
		foreach($rows as $row) {
			$cod_cliente = $row['cod_cliente'];
		}
		return $cod_cliente;
	}
}

?>