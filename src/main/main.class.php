<?php

require_once 'chopin.abstract.class.php';

class Main extends ChopinAbstract {

	public static $messaggio;
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

			self::$_instance = new Main();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'main.template.php';
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$db = Database::getInstance();		
		
		/**
		 * I controlli in apertura vengono fatti una sola volta nella vita della sessione
		 */
		
		if (!isset($_SESSION['notificaEffettuata'])) {

			/**
			 * Qui si possono inserire i controlli da fare in apertura
			 */
			
			$registrazioniErrate = "";
			$registrazioniErrate = $this->controllaRegistrazioniInErrore($utility, $db);
			if ($registrazioniErrate != "") {
				$registrazioniErrate = $registrazioniErrate . "<hr/><br/>"; 
			}

			// ------------------------------------------------------------------------------
			$scadenzeFornitori = "";
			$scadenzeFornitori = $this->controllaScadenzeFornitoriSuperate($utility, $db);
			if ($scadenzeFornitori != "") {
				$scadenzeFornitori = $scadenzeFornitori . "<hr/><br/>";  
			}

			// ------------------------------------------------------------------------------
			$scadenzeClienti = "";
			$scadenzeClienti = $this->controllaScadenzeClientiSuperate($utility, $db);
			if ($scadenzeClienti != "") {
				$scadenzeClienti = $scadenzeClienti . "<hr/><br/>";			
			}				
			
			//--------------------------------------------------------------------------------
					
			$messaggio = $registrazioniErrate . $scadenzeFornitori . $scadenzeClienti;
			
			//--------------------------------------------------------------------------------
			
			if ($messaggio != "") {
				$_SESSION['avvisoDiv'] = "<div id='avviso' title='Notifica'><p>" . $messaggio . "</p></div>";
				$_SESSION['avvisoDialog'] = "$( '#avviso' ).dialog({ " .
						"autoOpen: true, modal: true, minimize:true, width: 700, height: 400, " .
						"buttons: [{ text: 'Ok', click: function() { $(this).dialog('close'); }} ] })";								
			}
			
			$_SESSION['notificaEffettuata'] = "SI";
		}		
		
		$mainTemplate = MainTemplate::getInstance();
		$mainTemplate->displayPagina();
	}
}

?>