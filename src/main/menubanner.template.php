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
			$oggi = "";
		}
		else {
			$amb = "Ambiente di PRODUZIONE";				
		} 
		
		$form = self::$root . $array['template'] . self::$pagina;

		$utility = new utility();
		
		if (isset($_SESSION["lavoriPianificati"])) {
			
			$rows = $_SESSION["lavoriPianificati"];
			
			$tabellaLavoriPianificati .= "<div class='scroll-lavori'><table class='result'><tbody>";
			
			foreach($rows as $row) {
				
				$stato = ($row['sta_lavoro'] == '10') ? "Ok" : " ";
				$class = ($row['sta_lavoro'] == '10') ? "class='done'" : "class='todo'";
				$tabellaLavoriPianificati .= "<tr " . $class . " >";
				$tabellaLavoriPianificati .= "<td width='60'>" . $row['dat_lavoro'] . "</td>";
				$tabellaLavoriPianificati .= "<td width='110' nowrap align='left'>" . $row['des_lavoro'] . "</td>";
				$tabellaLavoriPianificati .= "<td width='37' align='center'>" . $stato . "</td>";
				$tabellaLavoriPianificati .= "</tr>";
			}
	
			$tabellaLavoriPianificati .= "</tbody></table>";			
		}

		$replace = array(
				'%amb%' => $amb,
				'%tabellaLavoriPianificati%' => $tabellaLavoriPianificati
		);

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		
		echo $utility->tailTemplate($template);
	}	
}

?>
