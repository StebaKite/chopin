<?php

require_once 'strumenti.abstract.class.php';
require_once 'database.access.interface.php';
require_once 'utility.class.php';
require_once 'database.base.class.php';

Class Database extends DatabaseBase implements DatabaseAccessInterface {

    public static $root;
    public static $dbconnection;
    public static $lastIdUsed;
    public static $numrows;

    function __construct() {
        self::$root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }  

    public static function getInstance() {
        if (parent::getIndexSession("Obj_database") === NULL) {
            parent::setIndexSession("Obj_database", serialize(new Database()));
        }
        return unserialize(parent::getIndexSession("Obj_database"));
    }

    public function getDBConnection() {
        return self::$dbconnection;
    }

    public function getLastIdUsed() {
        return self::$lastIdUsed;
    }

    public function getNumrows() {
        return self::$numrows;
    }

    public function setLastIdUsed($lastIdUsed) {
        self::$lastIdUsed = $lastIdUsed;
    }

    public function setNumrows($numrows) {
        self::$numrows = $numrows;
    }

    public function setDBConnection($dbconnection) {
        self::$dbconnection = $dbconnection;
    }

    /**
     * Questo metodo stabilisce una connessione persistente col DB.
     * Il comando pg_connect verific se esiste già una connessione con le stessa caratteristiche,
     * se esiste viene ritornata quella altrimenti ne viene ritornata una nuova.
     * 
     * {@inheritDoc}
     * @see DatabaseAccessInterface::createDatabaseConnection()
     */
    public function createDatabaseConnection($utility) {

        $array = $utility->getConfig();

        $users = shell_exec("who | cut -d' ' -f1 | sort | uniq");

        /**
         * Il comando shell_exec restituice l'output del comando come stringa
         * In questo caso tutti gli utenti loggati. Se fra questi c'è l'utente definito come utente di produzione nel
         * file di configurazione, la connessione viene aperta contro il db di produzione
         */
        if (strpos($users, $array['usernameProdLogin']) === false) {
            $dsn = "host=" . $array['hostname'] . " port=" . $array['portnum'] . " dbname=" . $array['dbnameTest'] . " user=" . $array['username'] . " password=" . $array['password'];
        } else {
            $dsn = "host=" . $array['hostname'] . " port=" . $array['portnum'] . " dbname=" . $array['dbnameProd'] . " user=" . $array['username'] . " password=" . $array['password'];
        }
        $DBConnection = pg_connect("$dsn") or die('Database connection failed');

        if ($DBConnection) {
            $this->setDBConnection($DBConnection);
            return true;
        }
        return false;
    }

    public function getData($sql) {

        $utility = Utility::getInstance();

        if ($this->getDBConnection() == null)
            $this->createDatabaseConnection($utility);
        $result = pg_query($this->getDBConnection(), $sql);
        return $result;
    }

    public function beginTransaction() {

        $utility = Utility::getInstance();

        if ($this->getDBConnection() == null)
            $this->createDatabaseConnection($utility);
        $result = pg_query($this->getDBConnection(), "BEGIN");
        if (!$result)
            error_log("BEGIN TRANSACTION FALLITO !!");
        return $result;
    }

    public function commitTransaction() {

        $utility = Utility::getInstance();

        if ($this->getDBConnection() == null)
            $this->createDatabaseConnection($utility);
        $result = pg_query($this->getDBConnection(), "COMMIT");
        if (!$result)
            error_log("COMMIT DATI FALLITO !!");
        return $result;
    }

    public function rollbackTransaction() {

        $utility = Utility::getInstance();

        if ($this->getDBConnection() == null)
            $this->createDatabaseConnection($utility);
        $result = pg_query($this->getDBConnection(), "ROLLBACK");
        if (!$result)
            error_log("RIPRISTINO DATI FALLITO !!");
        return $result;
    }

    public function execSql($sql) {

        $utility = Utility::getInstance();

        if ($this->getDBConnection() == null)
            $this->createDatabaseConnection($utility);

        // Esegue la query e se sulla INSERT e' impostata la clausola RETURNING, salva l'ID usato
        // Salva il numero di righe risultato della query

        $result = pg_query($this->getDBConnection(), $sql);

        $row = pg_fetch_row($result);
        $this->setLastIdUsed($row['0']);

        $this->setNumrows(pg_num_rows($result));
        return $result;
    }

    public function closeDBConnection() {

        if (pg_close($this->getDBConnection())) {
            parent::unsetIndexSessione("Obj_DBConnection");
            error_log("CONNESSIONE AL DATABASE CHIUSA CON SUCCESSO");
        } else
            error_log("Errore durante la chiusura della connessione al DB");
    }

}

?>
