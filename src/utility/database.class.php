<?php

require_once 'database.access.interface.php';

Class Database implements DatabaseAccessInterface {

	private static $root;
	private static $DBConnection;
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
	
	
	public function getDBConnection() {
		return self::$DBConnection;
	}
	public function getLastIdUsed() {
		return self::$lastIdUsed;
	}
	public function getNumrows() {
		return self::$numrows;
	}

	public function setDBConnection($DBConnection) {
		self::$DBConnection = $DBConnection;
	}
	public function setLastIdUsed($lastIdUsed) {
		self::$lastIdUsed = $lastIdUsed;
	}
	public function setNumrows($numrows) {
		self::$numrows = $numrows;
	}

	public function createDatabaseConnection() {

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
		$DBConnection = pg_connect("$dsn") or die('Connection failed');

		if ($DBConnection) {
			$this->setDBConnection($DBConnection);
			return true;
		}		
		return false;			
	}

	public function getData($sql) {	
		
		if ($this->getDBConnection() == null) $this->createDatabaseConnection();			
		$result = pg_query($this->getDBConnection(), $sql);
		return $result;
	}
	
	public function beginTransaction() {

		if ($this->getDBConnection() == null) $this->createDatabaseConnection();		
		$result = pg_query($this->getDBConnection(), "BEGIN");
		if (!$result) error_log("BEGIN TRANSACTION FALLITO !!");
		return $result;
	}
	
	public function commitTransaction() {
		
		if ($this->getDBConnection() == null) $this->createDatabaseConnection();
		$result = pg_query($this->getDBConnection(), "COMMIT");		
		if (!$result) error_log("COMMIT DATI FALLITO !!");		
		return $result;
	}
	
	public function rollbackTransaction() {
	
		if ($this->getDBConnection() == null) $this->createDatabaseConnection();
		$result = pg_query($this->getDBConnection(), "ROLLBACK");		
		if (!$result) error_log("RIPRISTINO DATI FALLITO !!");
		return $result;
	}
	
	public function execSql($sql) {

		if ($this->getDBConnection() == null) $this->createDatabaseConnection();
					
		// Esegue la query e se sulla INSERT e' impostata la clausola RETURNING, salva l'ID usato
		// Salva il numero di righe risultato della query
		
		$result = pg_query($this->getDBConnection(), $sql);
		
		$row = pg_fetch_row($result);			
		$this->setLastIdUsed($row['0']);
		
		$this->setNumrows(pg_num_rows($result));	
		return $result;	
	}
	
	public function closeDBConnection() {

		if (pg_close($this->getDBConnection()))
			error_log("CONNESSIONE AL DATABASE CHIUSA CON SUCCESSO");
		else
			error_log("Errore durante la chiusura della connessione al DB");
	}
}

?>
