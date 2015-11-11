<?php

Class Database {

	private static $root;
	private static $dblink;
	private static $lastIdUsed;
	private static $numrows;

	private static $_instance = null;
	
	function __construct() {
		
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {

		if( !is_object(self::$_instance) )
			
			self::$_instance = new Database();
		
		return self::$_instance;
	}	
	
	
	public function getDbLink() {
		return self::$dblink;
	}
	public function getLastIdUsed() {
		return self::$lastIdUsed;
	}
	public function getNumrows() {
		return self::$numrows;
	}

	public function setDbLink($dblink) {
		self::$dblink = $dblink;
	}
	public function setLastIdUsed($lastIdUsed) {
		self::$lastIdUsed = $lastIdUsed;
	}
	public function setNumrows($numrows) {
		self::$numrows = $numrows;
	}

	public function getLink() {

		require_once 'utility.class.php';

		$utility = new utility();
		$array = $utility->getConfig();

		$users = shell_exec("who | cut -d' ' -f1 | sort | uniq");
		
		/**
		 * Il comando shell_exec restituice l'output del comando come stringa
		 * In questo caso tutti gli utenti loggati. Se fra questi c'è l'utente definito come utente di produzione nel
		 * file di configurazione, la connessione viene aperta contro il db di produzione
		 */
		if (strpos($users, $array['usernameProdLogin']) === false) {
			$dsn = "host=" . $array['hostname'] . " port=" . $array['portnum'] . " dbname=" . $array['dbnameTest'] . " user=" . $array['username'] . " password=" . $array['password'];				
		}		
		else {
			$dsn = "host=" . $array['hostname'] . " port=" . $array['portnum'] . " dbname=" . $array['dbnameProd'] . " user=" . $array['username'] . " password=" . $array['password'];				
		}		

		// Create connection
		$dblink = pg_connect("$dsn") or die('Connection failed');
		
		// restituisco un oggetto connessione
		return $dblink;			
	}

	public function getData($sql) {

		$dblink = $this->getLink();
		$result = pg_query($dblink, $sql);
		pg_close($dblink); 
		return $result;
	}
	
	public function beginTransaction() {

		$dblink = $this->getLink();
		$result = pg_query($dblink, "BEGIN");
		
		if ($result) {
			$this->setDbLink($dblink);
		}
	}
	
	public function commitTransaction() {
		
		$result = pg_query($this->getDbLink(), "COMMIT");
		
		if ($result) {
			pg_close($this->getDbLink()); 
		}		
	}
	
	public function rollbackTransaction() {
	
		$result = pg_query($this->getDbLink(), "ROLLBACK");
		
		if ($result) {
			pg_close($this->getDbLink()); 
		}		
	}
		
	public function execSql($sql) {

		if ($this->getDbLink() == null) {
			error_log("CONNESSIONE AL DATABASE NON STABILITA");
			return FALSE;
		}
		else {
			
			// Esegue la query e se sulla INSERT e' impostata la clausola RETURNING, salva l'ID usato
			// Salva il numero di righe risultato della query
			
			$result = pg_query($this->getDbLink(), $sql);
			
			$row = pg_fetch_row($result);			
			$this->setLastIdUsed($row['0']);
			
			$this->setNumrows(pg_num_rows($result));	
			return $result;	
		}
	}
	
	public function closeDbLink() {

		if (pg_close($this->getDbLink()))
			error_log("CONNESSIONE AL DATABASE CHIUSA CON SUCCESSO");
		else
			error_log("Errore durante la chiusura della connessione al DB");
	}
}

?>
