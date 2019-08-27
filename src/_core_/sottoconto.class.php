<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Sottoconto extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Sottoconto

    const COD_CONTO = "cod_conto";
    const COD_SOTTOCONTO = "cod_sottoconto";
    const DES_SOTTOCONTO = "des_sottoconto";
    const DAT_CREAZIONE_SOTTOCONTO = "dat_creazione_sottoconto";
    const IND_GRUPPO = "ind_gruppo";
    // altre colonne derivate

    const NUM_REG_SOTTOCONTO = "totale_registrazioni_sottoconto";

    // dati sottoconto

    private $cod_conto;
    private $cod_sottoconto;
    private $des_sottoconto;
    private $dat_creazione_sottoconto;
    private $ind_gruppo;
    private $sottoconti;
    private $qtaSottoconti;
    private $sottocontiInseriti;  // utilizzato nella modifica per accumulare i sottoconti aggiunti
    private $dataRegistrazioneDa;  // dati di filtro per la generazione del mastrino
    private $dataRegistrazioneA;
    private $codNegozio;
    private $saldiInclusi;
    private $registrazioniTrovate;
    private $qtaRegistrazioniTrovate;
    private $nuoviSottoconti = array();  // utilizzata per accumulare i sottoconti durante la creazione del conto

    // Queries

    const CREA_SOTTOCONTO = "/configurazioni/creaSottoconto.sql";
    const CANCELLA_SOTTOCONTO = "/configurazioni/deleteSottoconto.sql";
    const LEGGI_SOTTOCONTI = "/configurazioni/leggiSottoconti.sql";
    const RICERCA_REGISTRAZIONI_CONTO = "/configurazioni/ricercaRegistrazioniConto.sql";
    const RICERCA_REGISTRAZIONI_CONTO_SALDI = "/configurazioni/ricercaRegistrazioniContoConSaldi.sql";
    const AGGIORNA_SOTTOCONTO = "/configurazioni/updateSottoconto.sql";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::SOTTOCONTO])) {
            $_SESSION[self::SOTTOCONTO] = serialize(new Sottoconto());
        }
        return unserialize($_SESSION[self::SOTTOCONTO]);
    }

    public function inserisci($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_conto%' => $this->getCodConto(),
            '%cod_sottoconto%' => $this->getCodSottoconto(),
            '%des_sottoconto%' => $this->getDesSottoconto(),
            '%ind_gruppo%' => $this->getIndGruppo()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_SOTTOCONTO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        return $result;
    }

    public function cancella($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_conto%' => $this->getCodConto(),
            '%cod_sottoconto%' => $this->getCodSottoconto()
        );
        $sqlTemplate = $this->root . $array['query'] . self::CANCELLA_SOTTOCONTO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);

        if ($db->getData($sql)) {
            $sottocontiDiff = array();
            foreach ($this->getSottoconti() as $unSottoconto) {
                if (trim($unSottoconto[self::COD_SOTTOCONTO]) != trim($this->getCodSottoconto())) {
                    array_push($sottocontiDiff, $unSottoconto);
                } else {
                    $this->setQtaSottoconti($this->getQtaSottoconti() - 1);
                }
            }
            $this->setSottoconti($sottocontiDiff);
            $_SESSION[self::SOTTOCONTO] = serialize($this);
        }
    }

    public function leggi($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_conto%' => $this->getCodConto()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_SOTTOCONTI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setSottoconti(pg_fetch_all($result));
            $this->setQtaSottoconti(pg_num_rows($result));
        } else {
            $this->setSottoconti(array());
            $this->setQtaSottoconti(0);
        }
    }

    public function cercaRegistrazioni($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $filtro = "";
        $filtroSaldo = "";

        if (($this->getDataRegistrazioneDa() != "") & ($this->getDataRegistrazioneA() != "")) {
            $filtro .= "and registrazione.dat_registrazione between '" . $this->getDataRegistrazioneDa() . "' and '" . $this->getDataRegistrazioneA() . "'";
            $filtroSaldo .= "and saldo.dat_saldo = '" . $this->getDataRegistrazioneDa() . "'";
        }

        if ($this->getCodNegozio() != "") {
            $filtro .= " and registrazione.cod_negozio = '" . $this->getCodNegozio() . "'";
            $filtroSaldo .= " and saldo.cod_negozio = '" . $this->getCodNegozio() . "'";
        }

        $replace = array(
            '%cod_conto%' => trim($this->getCodConto()),
            '%cod_sottoconto%' => trim($this->getCodSottoconto()),
            '%filtro_date%' => $filtro,
            '%filtro_date_saldo%' => $filtroSaldo
        );

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . Sottoconto::RICERCA_REGISTRAZIONI_CONTO_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . Sottoconto::RICERCA_REGISTRAZIONI_CONTO;
        }

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);

        $result = $db->getData($sql);

        if (pg_num_rows($result) > 0) {
            $this->setRegistrazioniTrovate(pg_fetch_all($result));
            $this->setQtaRegistrazioniTrovate(pg_num_rows($result));
        } else {
            $this->setRegistrazioniTrovate(null);
            $this->setQtaRegistrazioniTrovate(0);
        }

        $_SESSION[self::SOTTOCONTO] = serialize($this);
        return $result;
    }

    public function aggiungi() {
        $item = array(
            Sottoconto::COD_CONTO => trim($this->getCodConto()),
            Sottoconto::COD_SOTTOCONTO => trim($this->getCodSottoconto()),
            Sottoconto::DES_SOTTOCONTO => trim($this->getDesSottoconto()),
            Sottoconto::DAT_CREAZIONE_SOTTOCONTO => null, // è il valore che permetterà di inserire il sottoconto
            Sottoconto::IND_GRUPPO => "NS",
            Sottoconto::NUM_REG_SOTTOCONTO => 0
        );

        if ($this->getQtaSottoconti() == 0) {
            $resultset = array();
            array_push($resultset, $item);
            $this->setSottoconti($resultset);
        } else {
            array_push($this->sottoconti, $item);
            sort($this->sottoconti);
        }
        $this->setQtaSottoconti($this->getQtaSottoconti() + 1);
    }

    public function preparaNuoviSottoconti() {
        $this->setQtaSottoconti(0);
        $this->setSottoconti(array());

        $_SESSION[self::SOTTOCONTO] = serialize($this);
    }

    public function aggiorna($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_conto%' => trim($this->getCodConto()),
            '%cod_sottoconto%' => trim($this->getCodSottoconto()),
            '%ind_gruppo%' => trim($this->getIndGruppo())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . Sottoconto::AGGIORNA_SOTTOCONTO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $sottocontiDiff = array();
            foreach ($this->getSottoconti() as $unSottoconto) {
                if (trim($unSottoconto[Sottoconto::COD_SOTTOCONTO]) != trim($this->getCodSottoconto())) {
                    array_push($sottocontiDiff, $unSottoconto);
                } else {
                    $item = array(
                        Sottoconto::COD_CONTO => $this->getCodConto(),
                        Sottoconto::COD_SOTTOCONTO => $this->getCodSottoconto(),
                        Sottoconto::DES_SOTTOCONTO => $unSottoconto[Sottoconto::DES_SOTTOCONTO],
                        Sottoconto::DAT_CREAZIONE_SOTTOCONTO => $unSottoconto[Sottoconto::DAT_CREAZIONE_SOTTOCONTO],
                        Sottoconto::IND_GRUPPO => $this->getIndGruppo(),
                        Sottoconto::NUM_REG_SOTTOCONTO => $unSottoconto[Sottoconto::NUM_REG_SOTTOCONTO]
                    );
                    array_push($sottocontiDiff, $item);
                }
            }
            $this->setSottoconti($sottocontiDiff);
            $_SESSION[Sottoconto::SOTTOCONTO] = serialize($this);
        }
        return $result;
    }

    public function searchSottoconto($sottoconto) {
        foreach ($this->getSottoconti() as $unSottoconto) {
            if (trim($unSottoconto[Sottoconto::COD_SOTTOCONTO]) == trim($sottoconto)) {
                $this->setCodSottoconto($unSottoconto[Sottoconto::COD_SOTTOCONTO]);
                $this->setDesSottoconto($unSottoconto[Sottoconto::DES_SOTTOCONTO]);
                $this->setDatCreazioneSottoconto($unSottoconto[Sottoconto::DAT_CREAZIONE_SOTTOCONTO]);
                $this->setQtaRegistrazioniTrovate($unSottoconto[Sottoconto::NUM_REG_SOTTOCONTO]);
                break;
            }
        }
        $_SESSION[Sottoconto::SOTTOCONTO] = serialize($this);
    }


    /*     * **********************************************************************
     * Getters e setters
     */

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getRoot() {
        return $this->root;
    }

    public function setCodConto($cod_conto) {
        $this->cod_conto = $cod_conto;
    }

    public function getCodConto() {
        return $this->cod_conto;
    }

    public function setCodSottoconto($cod_sottoconto) {
        $this->cod_sottoconto = $cod_sottoconto;
    }

    public function getCodSottoconto() {
        return $this->cod_sottoconto;
    }

    public function setDesSottoconto($des_sottoconto) {
        $this->des_sottoconto = $des_sottoconto;
    }

    public function getDesSottoconto() {
        return $this->des_sottoconto;
    }

    public function setDatCreazioneSottoconto($dat_creazione_sottoconto) {
        $this->dat_creazione_sottoconto = $dat_creazione_sottoconto;
    }

    public function getDatCreazioneSottoconto() {
        return $this->dat_creazione_sottoconto;
    }

    public function setIndGruppo($ind_gruppo) {
        $this->ind_gruppo = $ind_gruppo;
    }

    public function getIndGruppo() {
        return $this->ind_gruppo;
    }

    public function getSottoconti() {
        return $this->sottoconti;
    }

    public function setSottoconti($sottoconti) {
        sort($sottoconti);
        $this->sottoconti = $sottoconti;
    }

    public function getQtaSottoconti() {
        return $this->qtaSottoconti;
    }

    public function setQtaSottoconti($qtaSottoconti) {
        $this->qtaSottoconti = $qtaSottoconti;
    }

    public function getSottocontiInseriti() {
        return $this->sottocontiInseriti;
    }

    public function setSottocontiInseriti($sottocontiInseriti) {
        $this->sottocontiInseriti = $sottocontiInseriti;
    }

    public function getDataRegistrazioneDa() {
        return $this->dataRegistrazioneDa;
    }

    public function setDataRegistrazioneDa($dataRegistrazioneDa) {
        $this->dataRegistrazioneDa = $dataRegistrazioneDa;
    }

    public function getDataRegistrazioneA() {
        return $this->dataRegistrazioneA;
    }

    public function setDataRegistrazioneA($dataRegistrazioneA) {
        $this->dataRegistrazioneA = $dataRegistrazioneA;
    }

    public function getCodNegozio() {
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio) {
        $this->codNegozio = $codNegozio;
    }

    public function getSaldiInclusi() {
        return $this->saldiInclusi;
    }

    public function setSaldiInclusi($saldiInclusi) {
        $this->saldiInclusi = $saldiInclusi;
    }

    public function getRegistrazioniTrovate() {
        return $this->registrazioniTrovate;
    }

    public function setRegistrazioniTrovate($registrazioniTrovate) {
        $this->registrazioniTrovate = $registrazioniTrovate;
    }

    public function getQtaRegistrazioniTrovate() {
        return $this->qtaRegistrazioniTrovate;
    }

    public function setQtaRegistrazioniTrovate($qtaRegistrazioniTrovate) {
        $this->qtaRegistrazioniTrovate = $qtaRegistrazioniTrovate;
    }

    public function getNuoviSottoconti() {
        return $this->nuoviSottoconti;
    }

    public function setNuoviSottoconti($nuoviSottoconti) {
        $this->nuoviSottoconti = $nuoviSottoconti;
    }

}