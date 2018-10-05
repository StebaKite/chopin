<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'sottoconto.class.php';

class Causale extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Causale

    const COD_CAUSALE = "cod_causale";
    const DES_CAUSALE = "des_causale";
    const DAT_INSERIMENTO = "dat_inserimento";
    const CAT_CAUSALE = "cat_causale";
    // Nomi colonne aggiunte o ricavate

    const QTA_REGISTRAZIONI_CAUSALE = "tot_registrazioni_causale";
    const QTA_CONTI_CAUSALE = "tot_conti_causale";

    // dati causale

    private $codCausale;
    private $desCausale;
    private $datInserimento;
    private $catCausale;
    private $causali;
    private $qtaCausali;
    private $qtaRegistrazioniCausale;
    private $qtaContiCausale;
    private $contiCausale;

    // Queries

    const RICERCA_CAUSALE = "/configurazioni/ricercaCausale.sql";
    const CREA_CAUSALE = "/configurazioni/creaCausale.sql";
    const CANCELLA_CAUSALE = "/configurazioni/deleteCausale.sql";
    const LEGGI_CAUSALE = "/configurazioni/leggiCausale.sql";
    const AGGIORNA_CAUSALE = "/configurazioni/updateCausale.sql";
    const RICERCA_CONTI_CAUSALE = "/configurazioni/ricercaContiCausale.sql";

    //	Colonne dell'array dalla query di lettura
    //
	// 	cod_causale,
    // 	des_causale,
    // 	cat_causale,
    // 	dat_inserimento,
    // 	tot_registrazioni_causale,
    // 	tot_conti_causale
    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::CAUSALE]))
            $_SESSION[self::CAUSALE] = serialize(new Causale());
        return unserialize($_SESSION[self::CAUSALE]);
    }

    public function load($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setCausali(pg_fetch_all($result));
            $this->setQtaCausali(pg_num_rows($result));
        } else {
            $this->setCausali(null);
            $this->setQtaCausali(null);
        }
        return $result;
    }

    public function caricaCausali($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array();
        $elencoCausali = "";

        if (sizeof($this->getQtaCausali()) == 0) {
            $this->load($db);
            $_SESSION[self::CAUSALE] = serialize($this);
        }

        foreach ($this->getCausali() as $unaCausale) {
            if (trim($unaCausale[Causale::COD_CAUSALE]) == trim($this->getCodCausale())) {
                $elencoCausali .= "<option value='" . trim($unaCausale[Causale::COD_CAUSALE]) . "' selected >" . trim($unaCausale[Causale::DES_CAUSALE]) . " (" . trim($unaCausale[Causale::COD_CAUSALE]) . ")" . "</option>";
            } else {
                $elencoCausali .= "<option value='" . trim($unaCausale[Causale::COD_CAUSALE]) . "'>" . trim($unaCausale[Causale::DES_CAUSALE]) . " (" . trim($unaCausale[Causale::COD_CAUSALE]) . ")" . "</option>";
            }
        }
        return $elencoCausali;
    }

    public function prepara() {
        $this->setCodCausale("");
        $this->setDesCausale("");
        $this->setCatCausale("");
    }

    public function inserisci($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale()),
            '%des_causale%' => trim($this->getDesCausale()),
            '%cat_causale%' => trim($this->getCatCausale())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $item = array(
                self::COD_CAUSALE => $this->getCodCausale(),
                self::DES_CAUSALE => $this->getDesCausale(),
                self::CAT_CAUSALE => $this->getCatCausale(),
                self::DAT_INSERIMENTO => $this->getDatInserimento(),
                self::QTA_REGISTRAZIONI_CAUSALE => 0,
                self::QTA_CONTI_CAUSALE => 0
            );
            array_push($this->causali, $item);
            sort($this->causali);
            $_SESSION[self::CAUSALE] = serialize($this);
        }
        return $result;
    }

    public function cancella($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);

        if ($db->getData($sql)) {
            $causaliDiff = array();
            foreach ($this->getCausali() as $unaCausale) {
                if (trim($unaCausale[self::COD_CAUSALE]) != trim($this->getCodCausale())) {
                    array_push($causaliDiff, $unaCausale);
                }
            }
            $this->setCausali($causaliDiff);
            $_SESSION[self::CAUSALE] = serialize($this);
        }
    }

    public function aggiorna($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $this->setDesCausale(str_replace("'", "''", $this->getDesCausale()));

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale()),
            '%des_causale%' => trim($this->getDesCausale()),
            '%cat_causale%' => trim($this->getCatCausale())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $causaliDiff = array();
            foreach ($this->getCausali() as $unaCausale) {
                if (trim($unaCausale[self::COD_CAUSALE]) != trim($this->getCodCausale())) {
                    array_push($causaliDiff, $unaCausale);
                } else {
                    $item = array(
                        self::COD_CAUSALE => $this->getCodCausale(),
                        self::DES_CAUSALE => $this->getDesCausale(),
                        self::CAT_CAUSALE => $this->getCatCausale(),
                        self::DAT_INSERIMENTO => $this->getDatInserimento(),
                        self::QTA_REGISTRAZIONI_CAUSALE => $this->getQtaRegistrazioniCausale(),
                        self::QTA_CONTI_CAUSALE => $this->getQtaContiCausale()
                    );
                    array_push($causaliDiff, $item);
                }
            }
            $this->setCausali($causaliDiff);
            $_SESSION[self::CAUSALE] = serialize($this);
        }
        return $result;
    }

    public function leggi($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $causale = pg_fetch_all($result);
            foreach ($causale as $row) {
                $this->setDesCausale(trim($row[self::DES_CAUSALE]));
                $this->setCatCausale(trim($row[self::CAT_CAUSALE]));
                $this->setQtaRegistrazioniCausale(trim($row[self::QTA_REGISTRAZIONI_CAUSALE]));
                $this->setQtaContiCausale(trim($row[self::QTA_CONTI_CAUSALE]));
            }
        }
        return $result;
    }

    public function aggiornaQuantitaConti($quantita) {
        $causaliDiff = array();
        foreach ($this->getCausali() as $unaCausale) {
            if (trim($unaCausale[self::COD_CAUSALE]) != trim($this->getCodCausale())) {
                array_push($causaliDiff, $unaCausale);
            } else {
                $nuovaQtaConti = $unaCausale[self::QTA_CONTI_CAUSALE] + $quantita;
                $item = array(
                    self::COD_CAUSALE => $unaCausale[self::COD_CAUSALE],
                    self::DES_CAUSALE => $unaCausale[self::DES_CAUSALE],
                    self::CAT_CAUSALE => $unaCausale[self::CAT_CAUSALE],
                    self::DAT_INSERIMENTO => $unaCausale[self::DAT_INSERIMENTO],
                    self::QTA_REGISTRAZIONI_CAUSALE => $unaCausale[self::QTA_REGISTRAZIONI_CAUSALE],
                    self::QTA_CONTI_CAUSALE => $nuovaQtaConti
                );
                array_push($causaliDiff, $item);
            }
        }
        $this->setCausali($causaliDiff);
        $_SESSION[self::CAUSALE] = serialize($this);
    }

    public function loadContiConfigurati($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array('%cod_causale%' => $this->getCodCausale());

        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_CONTI_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        $conti = "<option value=''></option>";
        $qtaContiCausale = pg_num_rows($result);
        $contiCausale = pg_fetch_all($result);
        $this->setQtaContiCausale(0);

        if ($qtaContiCausale > 0) {
            $this->setQtaContiCausale($qtaContiCausale);
            $this->setContiCausale($contiCausale);
            foreach ($contiCausale as $unConto) {
                $conti .= "<option value='" . trim($unConto[Sottoconto::COD_CONTO]) . "." . trim($unConto[Sottoconto::COD_SOTTOCONTO]) . " - " . trim($unConto[Sottoconto::DES_SOTTOCONTO]) . "'>" . trim($unConto[Sottoconto::DES_SOTTOCONTO]) . "</option>";
            }
        }
        $conti .= "</select>";
        $this->setContiCausale($conti);
        return $conti;
    }

    // Getters & Setters

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getCodCausale() {
        return $this->codCausale;
    }

    public function setCodCausale($codCausale) {
        $this->codCausale = $codCausale;
    }

    public function getDesCausale() {
        return $this->desCausale;
    }

    public function setDesCausale($desCausale) {
        $this->desCausale = $desCausale;
    }

    public function getDatInserimento() {
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento) {
        $this->datInserimento = $datInserimento;
    }

    public function getCatCausale() {
        return $this->catCausale;
    }

    public function setCatCausale($catCausale) {
        $this->catCausale = $catCausale;
    }

    public function getCausali() {
        return $this->causali;
    }

    public function setCausali($causali) {
        $this->causali = $causali;
    }

    public function getQtaCausali() {
        return $this->qtaCausali;
    }

    public function setQtaCausali($qtaCausali) {
        $this->qtaCausali = $qtaCausali;
    }

    public function getQtaRegistrazioniCausale() {
        return $this->qtaRegistrazioniCausale;
    }

    public function setQtaRegistrazioniCausale($qtaRegistrazioniCausale) {
        $this->qtaRegistrazioniCausale = $qtaRegistrazioniCausale;
    }

    public function getQtaContiCausale() {
        return $this->qtaContiCausale;
    }

    public function setQtaContiCausale($qtaContiCausale) {
        $this->qtaContiCausale = $qtaContiCausale;
    }

    public function getContiCausale() {
        return $this->contiCausale;
    }

    public function setContiCausale($contiCausale) {
        $this->contiCausale = $contiCausale;
    }

}

?>