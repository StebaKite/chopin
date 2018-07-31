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
    private $saldi;
    private $qtaSaldi;
    private $dateRiportoSaldi;
    private $qtaDateRiportoSaldi;

    // fitri di ricerca
    // Queries
    const LEGGI_SALDO = "/saldi/leggiSaldo.sql";
    const CANCELLA_SALDO = "/saldi/cancellaSaldo.sql";
    const AGGIORNA_SALDO = "/saldi/aggiornaSaldo.sql";
    const CREA_SALDO = "/saldi/creaSaldo.sql";
    const LEGGI_DATE_RIPORTO = "/saldi/ricercaDateRiportoSaldi.sql";
    const CARICA_SALDI = "/saldi/ricercaSaldi.sql";
    const SALDO_CONTO = "/saldi/saldoConto.sql";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public function getInstance() {
        if (!isset($_SESSION[self::SALDO]))
            $_SESSION[self::SALDO] = serialize(new Saldo());
        return unserialize($_SESSION[self::SALDO]);
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
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $saldo = pg_fetch_all($result);
            foreach ($saldo as $row) {
                $this->setDesSaldo(trim($row[self::DES_SALDO]));
                $this->setImpSaldo(trim($row[self::IMP_SALDO]));
                $this->setIndDareavere(trim($row[self::IND_DAREAVERE]));
            }
        }
        $_SESSION[self::SALDO] = serialize($this);
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
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
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
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
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
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function caricaDateRiporto($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_DATE_RIPORTO;
        $sql = $utility->getTemplate($sqlTemplate);
        $result = $db->getData($sql);

        if ($result) {
            $this->setDateRiportoSaldi(pg_fetch_all($result));
            $this->setQtaDateRiportoSaldi(pg_num_rows($result));
        } else {
            $this->setDateRiportoSaldi(self::NULL_VALUE);
            $this->setQtaDateRiportoSaldi(self::ZERO_VALUE);
        }
        $_SESSION[self::SALDO] = serialize($this);
        return $result;
    }

    public function caricaSaldi($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_negozio%' => $this->getCodNegozio(),
            '%dat_saldo%' => $this->getDatSaldo()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CARICA_SALDI;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setSaldi(pg_fetch_all($result));
            $this->setQtaSaldi(pg_num_rows($result));
        } else {
            $this->setSaldi(self::NULL_VALUE);
            $this->setQtaSaldi(self::ZERO_VALUE);
        }
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

    public function getSaldi() {
        return $this->saldi;
    }

    public function setSaldi($saldi) {
        $this->saldi = $saldi;
    }

    public function getQtaSaldi() {
        return $this->qtaSaldi;
    }

    public function setQtaSaldi($qtaSaldi) {
        $this->qtaSaldi = $qtaSaldi;
    }

    public function getDateRiportoSaldi() {
        return $this->dateRiportoSaldi;
    }

    public function setDateRiportoSaldi($dateRiportoSaldi) {
        $this->dateRiportoSaldi = $dateRiportoSaldi;
    }

    public function getQtaDateRiportoSaldi() {
        return $this->qtaDateRiportoSaldi;
    }

    public function setQtaDateRiportoSaldi($qtaDateRiportoSaldi) {
        $this->qtaDateRiportoSaldi = $qtaDateRiportoSaldi;
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

?>