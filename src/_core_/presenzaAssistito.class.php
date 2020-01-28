<?php

/**
 * Description of presenza assistito
 *
 * @author BarbieriStefano
 */

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class PresenzaAssistito extends CoreBase implements CoreInterface {


    private $root;

    // dari presenza assistito
    
    
    private $idPresenza;
    private $datPresenza;
    private $idAssistito;
    
    // Altri dati funzionali
    
    private $mese;
    private $nomeMese;
    private $anno;
    private $codNeg;
    private $file;
    private $presenzeTrovate;
    private $presenzeIncomplete;
    private $statoStep1;
    private $statoStep2;
    private $statoStep3;

    private $numPresenze;
    private $presenze;
    
    // Queries
    
    const CERCA_DATA_PRESENZA_ASSISTITO = "/strumenti/trovaDataPresenzaAssistito.sql";
    const CREA_PRESENZA_ASSISTITO = "/strumenti/creaPresenzaAssistito.sql";
    const RICERCA_PRESENZE_ASSISTITO = "/riepiloghi/ricercaPresenzeAssistito.sql";
    
    // Metodi

    function __construct() {
        $this->setRoot(parent::getInfoFromServer('DOCUMENT_ROOT'));
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::PRESENZA_ASSISTITO) === NULL) {
            parent::setIndexSession(self::PRESENZA_ASSISTITO, serialize(new PresenzaAssistito()));
        }
        return unserialize(parent::getIndexSession(self::PRESENZA_ASSISTITO));
    }
    
    public function prepara() {

    }
    
    public function isNew($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%dat_presenza%' => $this->getDatPresenza(),
            '%id_assistito%' => $this->getIdAssistito()
        );
        
        $sqlTemplate = $this->root . $array['query'] . self::CERCA_DATA_PRESENZA_ASSISTITO;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        
        if ($result) {
            if (pg_num_rows($result) > 0) {
                return false;
            }
        }
        return true;
    }

    public function inserisci($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%dat_presenza%' => $this->getDatPresenza(),
            '%id_assistito%' => $this->getIdAssistito(),
            '%cod_negozio%' => $this->getCodNeg()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_PRESENZA_ASSISTITO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        if ($result) {
            $this->setIdAssistito($db->getLastIdUsed());
            parent::setIndexSession(self::PRESENZA_ASSISTITO, serialize($this));
        }
        return $result;
    }

    public function ricercaPresenze($db) {
        
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_PRESENZE_ASSISTITO;

        $replace = array(
            '%anno%' => $this->getAnno(),
            '%mese%' => parent::isEmpty($this->getMese()) ? "'01','02','03','04','05','06','07','08','09','10','11','12'" : "'" . $this->getMese() . "'",
            '%codnegozio%' => parent::isEmpty($this->getCodNeg()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodNeg() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setPresenze(pg_fetch_all($result));
                $this->setNumPresenze(pg_num_rows($result));
            } else {
                $this->setPresenze(null);
                $this->setNumPresenze(0);
            }
        }
        parent::setIndexSession(self::PRESENZA_ASSISTITO, serialize($this));
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
    
    public function getIdPresenza() {
        return $this->idPresenza;
    }
    
    public function getMese() {
        return $this->mese;
    }

    public function setMese($mese) {
        $this->mese = $mese;
    }
    
    public function getNomeMese() {
        return $this->nomeMese;
    }

    public function setNomeMese($nomeMese) {
        $this->nomeMese = $nomeMese;
    }
    
    public function getAnno() {
        return $this->anno;
    }

    public function setAnno($anno) {
        $this->anno = $anno;
    }
    
    public function getCodNeg() {
        return $this->codNeg;
    }

    public function setCodNeg($codNeg) {
        $this->codNeg = $codNeg;
    }
    
    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
    }

    public function setIdPresenza($idPresenza) {
        $this->idPresenza = $idPresenza;
    }
    
    public function getDatPresenza() {
        return $this->datPresenza;
    }

    public function setDatPresenza($datPresenza) {
        $this->datPresenza = $datPresenza;
    }
    
    public function getIdAssistito() {
        return $this->idAssistito;
    }

    public function setIdAssistito($idAssistito) {
        $this->idAssistito = $idAssistito;
    }
    
    public function getPresenzeTrovate() {
        return $this->presenzeTrovate;
    }

    public function setPresenzeTrovate($presenzeTrovate) {
        $this->presenzeTrovate = $presenzeTrovate;
    }
    
    public function getPresenzeIncomplete() {
        return $this->presenzeIncomplete;
    }

    public function setPresenzeIncomplete($presenzeIncomplete) {
        $this->presenzeIncomplete = $presenzeIncomplete;
    }
    
    public function getStatoStep1() {
        return $this->statoStep1;
    }

    public function setStatoStep1($statoStep1) {
        $this->statoStep1 = $statoStep1;
    }
    
    public function getStatoStep2() {
        return $this->statoStep2;
    }

    public function setStatoStep2($statoStep2) {
        $this->statoStep2 = $statoStep2;
    }
    
    public function getStatoStep3() {
        return $this->statoStep3;
    }

    public function setStatoStep3($statoStep3) {
        $this->statoStep3 = $statoStep3;
    }
    
    public function getNumPresenze() {
        return $this->numPresenze;
    }

    public function setNumPresenze($numPresenze) {
        $this->numPresenze = $numPresenze;
    }
    
    public function getPresenze() {
        return $this->presenze;
    }

    public function setPresenze($presenze) {
        $this->presenze = $presenze;
    }
    
}
