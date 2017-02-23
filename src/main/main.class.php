<?php

require_once 'nexus6.abstract.class.php';
require_once 'main.business.interface.php';

class Main extends Nexus6Abstract implements MainBusinessInterface {

	public $messaggio;
	public $utility;
	public $array;
	
	function __construct() {

		require_once 'utility.class.php';
		
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
		
		$this->testata = $this->root . $this->array['testataPagina'];
		$this->piede = $this->root . $this->array['piedePagina'];
		$this->messaggioErrore = $this->root . $this->array['messaggioErrore'];
		$this->messaggioInfo = $this->root . $this->array['messaggioInfo'];
	}

	public function getInstance() {

		if (!isset($_SESSION["Obj_main"])) $_SESSION["Obj_main"] = serialize(new Main());
		return unserialize($_SESSION["Obj_main"]);
	}

	public function start() {

		require_once 'main.template.php';
		require_once 'database.class.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$db = Database::getInstance();		

		if ($db->getDBConnection() == null) {
			
			/**
			 * Apertura della connessione col Database
			 */
			
			if ($db->createDatabaseConnection($utility)) {
					
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
			else {
				$errorTemplate = ErrorTemplate::getInstance();
				$_SESSION['Errore fatale durante la creazione della connessione al Database'];
				$errorTemplate->displayPagina();
			}
		}
	}
	
	public function go() {}	
}

?>