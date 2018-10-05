<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'causale.class.php';

class ConfigurazioneCausale extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Configurazione_Causale

    const COD_CAUSALE = "cod_causale";
    const COD_CONTO = "cod_conto";
    const DAT_CREAZIONE_CONFIGURAZIONE = "dat_creazione_configurazione";

    // dati configurazione_causale

    private $codCausale;
    private $desCausale;
    private $codConto;
    private $datCreazioneConfigurazione;
    private $contiConfigurati;
    private $qtaContiConfigurati;
    private $contiConfigurabili;
    private $qtaContiConfigurabili;

    // Queries

    const CERCA_CONTI_CONFIGURATI = "/configurazioni/leggiContiCausale.sql";
    const CERCA_CONTI_CONFIGURABILI = "/configurazioni/leggiContiDisponibili.sql";
    const INCLUDI_CONTO_CAUSALE = "/configurazioni/creaConfigurazioneCausale.sql";
    const ESCLUDI_CONTO_CAUSALE = "/configurazioni/deleteConfigurazioneCausale.sql";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CONFIGURAZIONE_CAUSALE]))
            $_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize(new ConfigurazioneCausale());
        return unserialize($_SESSION[self::CONFIGURAZIONE_CAUSALE]);
    }

    public function loadContiConfigurati($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_CONTI_CONFIGURATI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setContiConfigurati(pg_fetch_all($result));
            $this->setQtaContiConfigurati(pg_num_rows($result));
        } else {
            $this->setContiConfigurati(null);
            $this->setQtaContiConfigurati(0);
        }
        return $result;
    }

    public function loadContiConfigurabili($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_CONTI_CONFIGURABILI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setContiConfigurabili(pg_fetch_all($result));
            $this->setQtaContiConfigurabili(pg_num_rows($result));
        } else {
            $this->setContiConfigurabili(null);
            $this->setQtaContiConfigurabili(0);
        }
        return $result;
    }

    public function inserisciConto($db) {
        $causale = Causale::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale()),
            '%cod_conto%' => trim($this->getCodConto())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::INCLUDI_CONTO_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $this->loadContiConfigurati($db);  // refresh dei conti configurati sulla causale
            $this->loadContiConfigurabili($db);  // refresh dei conti configurabili
            $_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($this);

            $causale->setCodCausale(trim($this->getCodCausale()));
            $causale->aggiornaQuantitaConti(+1);
        }
        return $result;
    }

    public function cancellaConto($db) {
        $causale = Causale::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_causale%' => trim($this->getCodCausale()),
            '%cod_conto%' => trim($this->getCodConto())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::ESCLUDI_CONTO_CAUSALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $this->loadContiConfigurati($db);  // refresh dei conti configurati sulla causale
            $this->loadContiConfigurabili($db);  // refresh dei conti configurabili
            $_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($this);

            $causale->setCodCausale(trim($this->getCodCausale()));
            $causale->aggiornaQuantitaConti(-1);
        }
        return $result;
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

    public function getCodConto() {
        return $this->codConto;
    }

    public function setCodConto($codConto) {
        $this->codConto = $codConto;
    }

    public function getDatCreazioneConfigurazione() {
        return $this->datCreazioneConfigurazione;
    }

    public function setDatCreazioneConfigurazione($datCreazioneConfigurazione) {
        $this->datCreazioneConfigurazione = $datCreazioneConfigurazione;
    }

    public function getContiConfigurati() {
        return $this->contiConfigurati;
    }

    public function setContiConfigurati($contiConfigurati) {
        $this->contiConfigurati = $contiConfigurati;
    }

    public function getQtaContiConfigurati() {
        return $this->qtaContiConfigurati;
    }

    public function setQtaContiConfigurati($qtaContiConfigurati) {
        $this->qtaContiConfigurati = $qtaContiConfigurati;
    }

    public function getContiConfigurabili() {
        return $this->contiConfigurabili;
    }

    public function setContiConfigurabili($contiConfigurabili) {
        $this->contiConfigurabili = $contiConfigurabili;
    }

    public function getQtaContiConfigurabili() {
        return $this->qtaContiConfigurabili;
    }

    public function setQtaContiConfigurabili($qtaContiConfigurabili) {
        $this->qtaContiConfigurabili = $qtaContiConfigurabili;
    }

    public function getDesCausale() {
        return $this->desCausale;
    }

    public function setDesCausale($desCausale) {
        $this->desCausale = $desCausale;
    }

}

?>