<?php

/**
 * Description of corrispettivo
 *
 * @author BarbieriStefano
 */

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Corrispettivo extends CoreBase implements CoreInterface {


    private $root;

    // Nomi colonne Corrispettivo

    // dati Corrispettivo

    private $mese;
    private $anno;
    private $codNeg;
    private $file;
    private $datada;
    private $dataa;
    private $contocassa;
    private $corrispettiviTrovati;
    private $corrispettiviIncompleti;

    // Altri dati funzionali
    
    private $statoStep1;
    private $statoStep2;
    private $statoStep3;
    
    // Queries
    
    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CORRISPETTIVO]))
            $_SESSION[self::CORRISPETTIVO] = serialize(new Corrispettivo());
        return unserialize($_SESSION[self::CORRISPETTIVO]);
    }
    
   public function prepara() {
        $this->setMese(null);
        $this->setAnno(null);
        $this->setCodNeg(null);
        $this->setFile(null);
        $this->setDatada(null);
        $this->setDataa(null);
        $this->setContoCassa(null);
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
    
    public function getMese() {
        return $this->mese;
    }

    public function setMese($mese) {
        $this->mese = $mese;
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
    
    public function getDatada() {
        return $this->datada;
    }

    public function setDatada($datada) {
        $this->datada = $datada;
    }
    
    public function getDataa() {
        return $this->dataa;
    }

    public function setDataa($dataa) {
        $this->dataa = $dataa;
    }
    
    public function getContoCassa() {
        return $this->contocassa;
    }

    public function setContoCassa($contocassa) {
        $this->contocassa = $contocassa;
    }
    
    public function getCorrispettiviTrovati() {
        return $this->corrispettiviTrovati;
    }

    public function setCorrispettiviTrovati($corrispettiviTrovati) {
        $this->corrispettiviTrovati = $corrispettiviTrovati;
    }
    
    public function getCorrispettiviIncompleti() {
        return $this->corrispettiviIncompleti;
    }

    public function setCorrispettiviIncompleti($corrispettiviIncompleti) {
        $this->corrispettiviIncompleti = $corrispettiviIncompleti;
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
}
