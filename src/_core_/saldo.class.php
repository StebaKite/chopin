<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Saldo extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Saldo

    const COD_NEGOZIO = "cod_negozio";
    const COD_CONTO = "cod_conto";
    const COD_SOTTOCONTO = "cod_sottoconto";
    const DAT_SALDO = "dat_saldo";
    const DAT_SALDO_SEL = "dat_saldo_selected";
    const DES_SALDO = "des_saldo";
    const IMP_SALDO = "imp_saldo";
    const IND_DAREAVERE = "ind_dareavere";

    // dati saldo

    private $dataregDa;
    private $dataregA;
    private $codNegozio;
    private $codConto;
    private $codSottoconto;
    private $datSaldo;
    private $datSaldoSel;
    private $desSaldo;
    private $impSaldo;
    private $indDareavere;

    // fitri di ricerca
    // Queries
    const LEGGI_SALDO = "/saldi/leggiSaldo.sql";
    const CANCELLA_SALDO = "/saldi/cancellaSaldo.sql";
    const AGGIORNA_SALDO = "/saldi/aggiornaSaldo.sql";
    const CREA_SALDO = "/saldi/creaSaldo.sql";
    const SALDO_CONTO = "/saldi/saldoConto.sql";

    // Metodi

    function __construct() {
        $this->setRoot(parent::getInfoFromServer('DOCUMENT_ROOT'));
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::SALDO) === NULL) {
            parent::setIndexSession(self::SALDO, serialize(new Saldo()));
        }
        return unserialize(parent::getIndexSession(self::SALDO));
    }

    public function leggiSaldo($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_negozio%' => $this->getCodNegozio(),
            '%cod_conto%' => $this->getCodConto(),
            '%cod_sottoconto%' => $this->getCodSottoconto(),
            '%dat_saldo%' => $this->getDatSaldo()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_SALDO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) === 0) {
                return FALSE;
            }
            return TRUE;
        }
        return $result;
    }

    public function leggiSaldoConto($db, $project_root) {

        if (parent::isNotEmpty($project_root)) {
            $this->setRoot($project_root);
        }

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%codnegozio%' => $this->getCodNegozio(),
            '%codconto%' => $this->getCodConto(),
            '%codsottoconto%' => $this->getCodSottoconto()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::SALDO_CONTO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        return $result;
    }

    public function cancellaSaldo($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_negozio%' => $this->getCodNegozio(),
            '%cod_conto%' => $this->getCodConto(),
            '%cod_sottoconto%' => $this->getCodSottoconto(),
            '%dat_saldo%' => $this->getDatSaldo()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_SALDO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function aggiornaSaldo($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_negozio%' => $this->getCodNegozio(),
            '%cod_conto%' => $this->getCodConto(),
            '%cod_sottoconto%' => $this->getCodSottoconto(),
            '%dat_saldo%' => $this->getDatSaldo(),
            '%des_saldo%' => $this->getDesSaldo(),
            '%imp_saldo%' => $this->getImpSaldo(),
            '%ind_dareavere%' => $this->getIndDareavere()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_SALDO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function creaSaldo($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_negozio%' => $this->getCodNegozio(),
            '%cod_conto%' => $this->getCodConto(),
            '%cod_sottoconto%' => $this->getCodSottoconto(),
            '%dat_saldo%' => $this->getDatSaldo(),
            '%des_saldo%' => $this->getDesSaldo(),
            '%imp_saldo%' => $this->getImpSaldo(),
            '%ind_dareavere%' => $this->getIndDareavere()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_SALDO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    // Getters e Setters

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getCodNegozio() {
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio) {
        $this->codNegozio = $codNegozio;
    }

    public function getCodConto() {
        return $this->codConto;
    }

    public function setCodConto($codConto) {
        $this->codConto = $codConto;
    }

    public function getCodSottoconto() {
        return $this->codSottoconto;
    }

    public function setCodSottoconto($codSottoconto) {
        $this->codSottoconto = $codSottoconto;
    }

    public function getDatSaldo() {
        return $this->datSaldo;
    }

    public function setDatSaldo($datSaldo) {
        $this->datSaldo = $datSaldo;
    }

    public function getDesSaldo() {
        return $this->desSaldo;
    }

    public function setDesSaldo($desSaldo) {
        $this->desSaldo = $desSaldo;
    }

    public function getImpSaldo() {
        return $this->impSaldo;
    }

    public function setImpSaldo($impSaldo) {
        $this->impSaldo = $impSaldo;
    }

    public function getIndDareavere() {
        return $this->indDareavere;
    }

    public function setIndDareavere($indDareavere) {
        $this->indDareavere = $indDareavere;
    }

    public function getDatSaldoSel() {
        return $this->datSaldoSel;
    }

    public function setDatSaldoSel($datSaldoSel) {
        $this->datSaldoSel = $datSaldoSel;
    }

    public function getDataregDa() {
        return $this->dataregDa;
    }

    public function setDataregDa($dataregDa) {
        $this->dataregDa = $dataregDa;
    }

    public function getDataregA() {
        return $this->dataregA;
    }

    public function setDataregA($dataregA) {
        $this->dataregA = $dataregA;
    }

}