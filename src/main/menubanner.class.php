<?php

require_once 'chopin.abstract.class.php';

class Menubanner extends ChopinAbstract {

	private static $messaggio;
	private static $queryTotaliProgressivi = "/main/totaliProgressivi.sql";
	private static $queryLavoriPianificati = "/main/lavoriPianificati.sql";
	private static $queryCambioStatoLavoroPianificato = "/main/cambioStatoLavoroPianificato.sql";
	
	public static $sourceFolder = "/chopin/src/saldi/";
	
	private static $_instance = null;
	
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
	
			self::$_instance = new Menubanner();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------

	public function start() {
			
		require_once 'menubanner.template.php';
		require_once 'utility.class.php';
		require_once 'database.class.php';

		$utility = Utility::getInstance();
		
		$menubannerTemplate = MenubannerTemplate::getInstance();

		$array = $utility->getConfig();

		if ($array['lavoriPianificatiAttivati'] == "Si") {
			
			$db = Database::getInstance();

			$db->beginTransaction();
				
			$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
			
			if ($lavoriPianificati) {
				$rows = pg_fetch_all($lavoriPianificati);
				$_SESSION["lavoriPianificati"] = $rows;	
				
				$oggi = date("Y/m/d");
			
				foreach($rows as $row) {
			
					if ((strtotime($row['dat_lavoro']) <= strtotime($oggi)) && ($row['sta_lavoro'] == "00")) {
						if ($this->eseguiLavoro($row)) {
							$this->cambioStatoLavoroPianificato($db, $utility, $row['pk_lavoro_pianificato'], '10');								
							error_log($row['des_lavoro'] . " eseguito");
							
							$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
							$_SESSION["lavoriPianificati"] = pg_fetch_all($lavoriPianificati);
						}
					}
				}
			}
			else {
				unset($_SESSION["lavoriPianificati"]);
			}
			$db->commitTransaction();
		}
		else {
			error_log("Lavori pianificati non attivi!!");
		}
		
		// compone la pagina
		include($testata);
		$menubannerTemplate->displayPagina();
		include($piede);
	}
	
	public function leggiLavoriPianificati($db, $utility) {

		/**
		 * Prendo tutto le pianificazioni di tutto l'anno
		 */
		$dataLavoroDa = '01/01/' . date("Y"); 
		$dataLavoroA = '31/12/' . date("Y");
		
		$replace = array(
				'%datalavoro_da%' => $dataLavoroDa,
				'%datalavoro_a%' => $dataLavoroA
		);
		
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryLavoriPianificati;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		return $result;
	}	

	public function cambioStatoLavoroPianificato($db, $utility, $pklavoro, $stato) {
	
	
		$replace = array(
				'%sta_lavoro%' => $stato,
				'%pk_lavoro_pianificato%' => $pklavoro
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryCambioStatoLavoroPianificato;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		return $result;
	}
	
	
	
	public function eseguiLavoro($row) {

		$className = trim($row['cla_esecuzione_lavoro']);
		$fileClass = self::$root . self::$sourceFolder . trim($row['fil_esecuzione_lavoro']) . '.class.php';
	
		if (file_exists($fileClass)) {
				
			require_once trim($row['fil_esecuzione_lavoro']) . '.class.php';
				
			if (class_exists($className)) {
				$instance = new $className();
				$_SESSION["dataEsecuzioneLavoro"] = str_replace("-", "/", $row["dat_lavoro"]);				
				if ($instance->start()) {					
					return TRUE;
				}
			}
			else {
				error_log("Il nome classe '" . $className . "' non &egrave; definita, lavoro non eseguito");
				return FALSE;
			}
		}
		else {
			error_log("Il file '" . $fileClass . "' non esiste, lavoro non eseguito");
			return FALSE;
		}
	}		
}

?>
