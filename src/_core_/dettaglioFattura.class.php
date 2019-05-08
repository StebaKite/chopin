<?php

require_once 'core.interface.php';
require_once 'fattura.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class DettaglioFattura extends Fattura implements CoreInterface {

    private $root;
    private $dettagliFattura;
    private $qtaDettagliFattura;
    private $idArticolo;
    private $qtaArticolo;
    private $desArticolo;
    private $impArticolo;
    private $codAliquota;
    private $impTotale;
    private $impImponibile;
    private $impIva;

    // Nomi colonne dettaglio fattura

    const ID_ARTICOLO = "id_articolo";
    const QTA_ARTICOLO = "qta_articolo";
    const DES_ARTICOLO = "des_articolo";
    const IMP_ARTICOLO = "imp_articolo";
    const COD_ALIQUOTA = "cod_aliquota";
    const IMP_TOTALE = "imp_totale";
    const IMP_IMPONIBILE = "imp_imponibile";
    const IMP_IVA = "imp_iva";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::DETTAGLIO_FATTURA]))
            $_SESSION[self::DETTAGLIO_FATTURA] = serialize(new DettaglioFattura());
        return unserialize($_SESSION[self::DETTAGLIO_FATTURA]);
    }

    public function prepara() {
        $this->setDettagliFattura(null);
        $this->setQtaDettagliFattura(0);
        $_SESSION[self::DETTAGLIO_FATTURA] = serialize($this);
    }

    public function aggiungi() {
        $item = array(
            DettaglioFattura::ID_ARTICOLO => $this->getIdArticolo(),
            DettaglioFattura::QTA_ARTICOLO => $this->getQtaArticolo(),
            DettaglioFattura::DES_ARTICOLO => trim($this->getDesArticolo()),
            DettaglioFattura::IMP_ARTICOLO => trim($this->getImpArticolo()),
            DettaglioFattura::COD_ALIQUOTA => trim($this->getCodAliquota()),
            DettaglioFattura::IMP_TOTALE => trim($this->getImpTotale()),
            DettaglioFattura::IMP_IMPONIBILE => trim($this->getImpImponibile()),
            DettaglioFattura::IMP_IVA => trim($this->getImpIva())
        );

        if ($this->getQtaDettagliFattura() == 0) {
            $resultset = array();
            array_push($resultset, $item);
            $this->setDettagliFattura($resultset);
        } else {
            array_push($this->dettagliFattura, $item);
        }
        $this->setQtaDettagliFattura($this->getQtaDettagliFattura() + 1);
        $_SESSION[self::DETTAGLIO_FATTURA] = serialize($this);
    }

    public function cancella($db) {
        $dettagliDiff = array();
        foreach ($this->getDettagliFattura() as $unDettaglio) {
            if (trim($unDettaglio[DettaglioFattura::ID_ARTICOLO]) != trim($this->getIdArticolo())) {
                array_push($dettagliDiff, $unDettaglio);
            } else {
                $this->setQtaDettagliFattura($this->getQtaDettagliFattura() - 1);
            }
        }
        $this->setDettagliFattura($dettagliDiff);
        $_SESSION[self::DETTAGLIO_FATTURA] = serialize($this);
    }

    /*
     * Getters e Setters
     */

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getDettagliFattura() {
        return $this->dettagliFattura;
    }

    public function setDettagliFattura($dettagliFattura) {
        $this->dettagliFattura = $dettagliFattura;
    }

    public function getQtaDettagliFattura() {
        return $this->qtaDettagliFattura;
    }

    public function setQtaDettagliFattura($qtaDettagliFattura) {
        $this->qtaDettagliFattura = $qtaDettagliFattura;
    }

    public function getQtaArticolo() {
        return $this->qtaArticolo;
    }

    public function setQtaArticolo($qtaArticolo) {
        $this->qtaArticolo = $qtaArticolo;
    }

    public function getDesArticolo() {
        return $this->desArticolo;
    }

    public function setDesArticolo($desArticolo) {
        $this->desArticolo = $desArticolo;
    }

    public function getImpArticolo() {
        return $this->impArticolo;
    }

    public function setImpArticolo($impArticolo) {
        $this->impArticolo = $impArticolo;
    }

    public function getCodAliquota() {
        return $this->codAliquota;
    }

    public function setCodAliquota($codAliquota) {
        $this->codAliquota = $codAliquota;
    }

    public function getImpTotale() {
        return $this->impTotale;
    }

    public function setImpTotale($impTotale) {
        $this->impTotale = $impTotale;
    }

    public function getImpImponibile() {
        return $this->impImponibile;
    }

    public function setImpImponibile($impImponibile) {
        $this->impImponibile = $impImponibile;
    }

    public function getImpIva() {
        return $this->impIva;
    }

    public function setImpIva($impIva) {
        $this->impIva = $impIva;
    }

    public function getIdArticolo() {
        return $this->idArticolo;
    }

    public function setIdArticolo($idArticolo) {
        $this->idArticolo = $idArticolo;
    }

}
