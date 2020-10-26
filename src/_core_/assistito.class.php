<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Assistito extends CoreBase implements CoreInterface {


    private $root;

    // Nomi colonne tabella Assistito
    
    const ID_ASSISTITO = "id_assistito";
    const DES_ASSISTITO = "des_assistito";
    const DAT_INSERIMENTO = "dat_inserimento";
    
    // dati presenza assistito

    private $idAssistito;
    private $desAssistito;
    private $datInserimento;

    // Query
    
    const CERCA_ASSISTITO_CON_NOME = "/strumenti/trovaAssistitoConNome.sql";
    const CREA_ASSISTITO = "/strumenti/creaAssistito.sql";
    
    // Metodi

    function __construct() {
        $this->setRoot(parent::getInfoFromServer('DOCUMENT_ROOT'));
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::ASSISTITO) === NULL) {
            parent::setIndexSession(self::ASSISTITO, serialize(new Assistito()));
        }
        return unserialize(parent::getIndexSession(self::ASSISTITO));
    }
    
    public function getIdAssistitoFromName($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array('%des_assistito%' => trim($this->getDesAssistito()));
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_ASSISTITO_CON_NOME;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        $this->setIdAssistito("");
        $this->setDatInserimento("");

        if ($result) {
            foreach (pg_fetch_all($result) as $row) {
                $this->setIdAssistito($row[Assistito::ID_ASSISTITO]);
                $this->setDesAssistito($row[Assistito::DES_ASSISTITO]);
                $this->setDatInserimento($row[Assistito::DAT_INSERIMENTO]);
            }
            parent::setIndexSession(self::ASSISTITO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }
    
    public function inserisci($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%des_assistito%' => $this->getDesAssistito()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_ASSISTITO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        if ($result) {
            $this->setIdAssistito($db->getLastIdUsed());
            parent::setIndexSession(self::ASSISTITO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        return $result;
    }
    
    /**
     * Getters & Setters
     */
    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }
    
    public function getIdAssistito() {
        return $this->idAssistito;
    }

    public function setIdAssistito($idAssistito) {
        $this->idAssistito = $idAssistito;
    }
    
    public function getDesAssistito() {
        return $this->desAssistito;
    }

    public function setDesAssistito($desAssistito) {
        $this->desAssistito = $desAssistito;
    }
    
    public function getDatInserimento() {
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento) {
        $this->datInserimento = $datInserimento;
    }
}
