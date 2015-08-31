<?php

require_once 'chopin.abstract.class.php';

class MenubannerTemplate extends ChopinAbstract {

	public static $root;
	public static $pagina = "/main/menubanner.form.html";
	public static $totaliProgressivi;
	
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
	
			self::$_instance = new MenubannerTemplate();
	
		return self::$_instance;
	}
	
	// ------------------------------------------------

	public function displayPagina() {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$tabellaTotali = "";
		
		// Template --------------------------------------------------------------
		
		$utility = new utility();
		$array = $utility->getConfig();

		$users = shell_exec("who | cut -d' ' -f1 | sort | uniq");
		
		if (strpos($users, $array['usernameProdLogin']) === false) {
			$amb = "Ambiente di TEST";				
			$who = "";
			$oggi = "";
		}
		else {
			$amb = "Ambiente di PRODUZIONE";				
			$who = "User connesso: " . ucfirst($array['usernameProdLogin']);
			$oggi = "Oggi non ci sono impegni in agenda";
		} 
		
		$form = self::$root . $array['template'] . self::$pagina;

		$utility = new utility();
		
// 		if ($this->getTotaliProgressivi() != "") {
			
// 			$tabellaTotali .= "<table class='result' id='resultTable'>";
// 			$tabellaTotali .= "<thead><th>%ml.oggetto%</th><th>%ml.totale%</th></thead><tbody>";
			
// 			foreach($this->getTotaliProgressivi() as $row) {
// 				$tabellaTotali .= "<tr class='on'> <td width='150'>" . $row['entita'] . "</td><td width='50' align='right'>" . $row['totale'] . "</td></tr>";
// 			}
			
// 			$tabellaTotali .= "</tbody></table>";			
// 		}

		$replace = array(
				'%amb%' => $amb,
				'%who%' => $who,
				'%oggi%' => $oggi
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		
		echo $utility->tailTemplate($template);
	}	
}

?>
