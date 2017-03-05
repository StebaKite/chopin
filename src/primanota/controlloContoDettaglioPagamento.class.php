<?php

require_once 'primanota.abstract.class.php';

class ControlloContoDettaglioPagamento extends PrimanotaAbstract {

	public static $queryTrovaDescrizioneFornitore = "/anagrafica/trovaDescFornitore.sql";
	
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

			self::$_instance = new ControlloContoDettaglioPagamento();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$array = $utility->getConfig();
		
		if ($_SESSION["conto"] != "") {
			
			/**
			 * Faccio il controllo del conto solo per i conti fornitori 
			 */
			
			if (strstr($array['contiFornitore'], trim(substr($_SESSION["conto"],0,3)))) {
				
				/**
				 *  cerco il codice del fornitore selezionato
				 */
				
				$fornitore = ($_SESSION["fornitore"] != "") ? $this->leggiDescrizioneFornitore($db, $utility, str_replace("'", "''", $_SESSION["fornitore"])) : "null" ;
				
				/**
				 * il codice del fornitore deve corrispondere al codice del sottoconto selezionato
				 */
				
				if ($fornitore != "null") {
					if (trim(substr($_SESSION["conto"], 3)) != trim($fornitore)) {
						echo "Il conto selezionato non appartiene a questo fornitore, dettaglio non inserito" ;
					}
					else {
						echo "Dettaglio ok";
					}
				}
				else {
					echo "Fornitore non esistente, controllo conto dettaglio non eseguito!";
				}
			}
			else {
				echo "Dettaglio ok";
			}		
		}
		else {
			echo "Conto non selezionato, dettaglio registrazione non inserito";
		}
	}
	
	/**
	 * Questo metodo fa l'override del super perchè restituisce il codice del fornitore anzichè il suo ID
	 * @see ChopinAbstract::leggiDescrizioneFornitore()
	 */
	public function leggiDescrizioneFornitore($db, $utility, $desfornitore) : string {
	
		$array = $utility->getConfig();
		$replace = array(
				'%des_fornitore%' => trim($desfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaDescrizioneFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		$rows = pg_fetch_all($result);
	
		foreach($rows as $row) {
			$descrizione_fornitore = $row['cod_fornitore'];
		}
		return $descrizione_fornitore;
	}
}

?>