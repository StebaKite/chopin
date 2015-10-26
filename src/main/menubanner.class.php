<?php

require_once 'chopin.abstract.class.php';

class Menubanner extends ChopinAbstract {

	private static $messaggio;
	private static $queryTotaliProgressivi = "/main/totaliProgressivi.sql";
	private static $queryLavoriPianificati = "/main/lavoriPianificati.sql";
	
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
		
		$menubannerTemplate = MenubannerTemplate::getInstance();

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		$lavoriPianificati = $this->leggiLavoriPianificati($db, $utility);
		
		if ($lavoriPianificati) {				
			$rows = pg_fetch_all($lavoriPianificati);		
			
			$oggi = date("Y/m/d");
				
			foreach($rows as $row) {
				
				if (($row['dat_lavoro'] <= $oggi) && ($row['sta_lavoro'] == "00")) {
					if ($this->eseguiLavoro($row)) {
						error_log($row['des_lavoro'] . " eseguito");
					}
				}
			}
		}
		 
// 		require_once 'riportoSaldoPeriodico.class.php';
// 		$riportoSaldoPeriodico = RiportoSaldoPeriodico::getInstance();
// 		$_SESSION["dataEsecuzioneLavoro"] = "01/10/2015";
// 		$riportoSaldoPeriodico->start();
		
		// compone la pagina
		include($testata);
		$menubannerTemplate->displayPagina();
		include($piede);
	}
	
	public function leggiLavoriPianificati($db, $utility) {
	
		$dataLavoroDa = '01/' . str_pad(date("m")-1, 2, "0", STR_PAD_LEFT) . '/' . date("Y"); 
		$dataLavoroA = '01/' . str_pad(date("m")+1, 2, "0", STR_PAD_LEFT) . '/' . date("Y");
		
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
				else {
					return FALSE;
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
