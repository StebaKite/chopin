<?php

class Negozio extends CoreBase implements CoreInterface {

    private $root;

    /*
     * Dati Negozio
     */
    private $idNegozio;
    private $codNegozio;
    private $desNegozio;
    private $cittaNegozio;

    /*
     * Altri dati funzionali
     */
    private $file;
    private $mese;
    private $anno;
    private $datada;
    private $dataa;
    private $contocassa;
    private $corrispettiviTrovati;
    private $corrispettiviIncompleti;
    private $messaggioImportFileOk;
    private $messaggioImportFileErr;

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public function getInstance() {
        if (!isset($_SESSION[self::NEGOZIO]))
            $_SESSION[self::NEGOZIO] = serialize(new Negozio());
        return unserialize($_SESSION[self::NEGOZIO]);
    }

    public function prepara() {
        $this->setFile(self::NULL_VALUE);
        $this->setMese(self::NULL_VALUE);
        $this->setAnno(self::NULL_VALUE);
        $this->setDatada(self::NULL_VALUE);
        $this->setDataa(self::NULL_VALUE);
        $this->setIdNegozio(self::NULL_VALUE);
        $this->setCodNegozio(self::NULL_VALUE);
        $this->setDesNegozio(self::NULL_VALUE);
        $this->setCittaNegozio(self::NULL_VALUE);
        $this->setCorrispettiviTrovati(self::NULL_VALUE);
        $this->setCorrispettiviIncompleti(self::NULL_VALUE);
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

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
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

    public function getIdNegozio() {
        return $this->idNegozio;
    }

    public function setIdNegozio($idNegozio) {
        $this->idNegozio = $idNegozio;
    }

    public function getCodNegozio() {
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio) {
        $this->codNegozio = $codNegozio;
    }

    public function getDesNegozio() {
        return $this->desNegozio;
    }

    public function setDesNegozio($desNegozio) {
        $this->desNegozio = $desNegozio;
    }

    public function getCittaNegozio() {
        return $this->cittaNegozio;
    }

    public function setCittaNegozio($cittaNegozio) {
        $this->cittaNegozio = $cittaNegozio;
    }

    public function getContocassa() {
        return $this->contocassa;
    }

    public function setContocassa($contocassa) {
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

    public function getMessaggioImportFileOk() {
        return $this->messaggioImportFileOk;
    }

    public function setMessaggioImportFileOk($messaggioImportFileOk) {
        $this->messaggioImportFileOk = $messaggioImportFileOk;
    }

    public function getMessaggioImportFileErr() {
        return $this->messaggioImportFileErr;
    }

    public function setMessaggioImportFileErr($messaggioImportFileErr) {
        $this->messaggioImportFileErr = $messaggioImportFileErr;
    }

}
