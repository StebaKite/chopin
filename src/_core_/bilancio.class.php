<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Bilancio extends CoreBase implements CoreInterface {

    private $root;
    private $dataregDa;
    private $dataregA;
    private $annoEsercizioSel;
    private $codnegSel;
    private $catconto;
    private $soloContoEconomico;
    private $saldiInclusi;
    private $costiBilancio;
    private $ricaviBilancio;
    private $attivoBilancio;
    private $passivoBilancio;
    private $numCostiTrovati;
    private $totaleCosti;
    private $totaleRicavi;
    private $totaleAttivo;
    private $totalePassivo;
    private $numRicaviTrovati;
    private $costoVariabile;
    private $numAttivoTrovati;
    private $numPassivoTrovati;
    private $ricavoVenditaProdotti;
    private $costoFisso;
    private $tabellaCosti;
    private $tabellaRicavi;
    private $tabellaAttivo;
    private $tabellaPassivo;
    private $tipoBilancio;

    /**
     *  Queries
     */
    const COSTI = "/riepiloghi/costi.sql";
    const COSTI_CON_SALDI = "/riepiloghi/costiConSaldi.sql";
    const RICAVI = "/riepiloghi/ricavi.sql";
    const RICAVI_CON_SALDI = "/riepiloghi/ricaviConSaldi.sql";
    const COSTI_MARGINE_CONTRIBUZIONE = "/riepiloghi/costiMargineContribuzione.sql";
    const COSTI_MARGINE_CONTRIBUZIONE_CON_SALDI = "/riepiloghi/costiMargineContribuzioneConSaldi.sql";
    const RICAVI_MARGINE_CONTRIBUZIONE = "/riepiloghi/ricaviMargineContribuzione.sql";
    const RICAVI_MARGINE_CONTRIBUZIONE_CON_SALDI = "/riepiloghi/ricaviMargineContribuzioneConSaldi.sql";
    const ATTIVO = "/riepiloghi/attivo.sql";
    const PASSIVO = "/riepiloghi/passivo.sql";
    const COSTI_FISSI = "/riepiloghi/costiFissi.sql";
    const COSTI_FISSI_CON_SALDI = "/riepiloghi/costiFissiConSaldi.sql";

    /**
     *  Metodi
     */
    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::BILANCIO])) {
            $_SESSION[self::BILANCIO] = serialize(new Bilancio());
        }
        return unserialize($_SESSION[self::BILANCIO]);
    }

    public function prepara() {

        if (parent::isEmpty($this->getDataregDa())) {
            $this->setDataregDa(date("d/m/Y"));
        }

        if (parent::isEmpty($this->getDataregA())) {
            $this->setDataregA(date("d/m/Y"));
        }

        if (parent::isEmpty($this->getCodnegSel())) {
            $this->setCodnegSel(self::EMPTYSTRING);
        }

        if (parent::isEmpty($this->getSoloContoEconomico())) {
            $this->setSoloContoEconomico(self::TUTTI_CONTI);
        }

        if (parent::isEmpty($this->getCatconto())) {
            $this->setCatconto(self::TUTTI_CONTI);
        }

        if (parent::isEmpty($this->getSaldiInclusi())) {
            $this->setSaldiInclusi(self::SALDI_INCLUSI);
        }

        $this->setCostiBilancio(self::EMPTYSTRING);
        $this->setRicaviBilancio(self::EMPTYSTRING);
        $this->setAttivoBilancio(self::EMPTYSTRING);
        $this->setPassivoBilancio(self::EMPTYSTRING);

        $this->setNumCostiTrovati(self::ZERO_VALUE);
        $this->setTotaleCosti(self::ZERO_VALUE);
        $this->setTotaleRicavi(self::ZERO_VALUE);
        $this->setTotaleAttivo(self::ZERO_VALUE);
        $this->setTotalePassivo(self::ZERO_VALUE);
        $this->setNumRicaviTrovati(self::ZERO_VALUE);
        $this->setCostoVariabile(self::EMPTYSTRING);
        $this->setNumAttivoTrovati(self::EMPTYSTRING);
        $this->setNumPassivoTrovati(self::EMPTYSTRING);
        $this->setRicavoVenditaProdotti(self::EMPTYSTRING);
        $this->setCostoFisso(self::EMPTYSTRING);
        $this->setTabellaCosti(self::EMPTYSTRING);
        $this->setTabellaRicavi(self::EMPTYSTRING);
        $this->setTabellaAttivo(self::EMPTYSTRING);
        $this->setTabellaPassivo(self::EMPTYSTRING);

        $_SESSION[self::BILANCIO] = serialize($this);
    }

    /**
     * Questo metodo estrae i costi
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return unknown
     */
    public function ricercaCosti($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_CON_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI;
        }

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%catconto%' => $this->getCatconto(),
            '%codnegozio%' => parent::isEmpty($this->getCodnegSel()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setCostiBilancio(pg_fetch_all($result));
                $this->setNumCostiTrovati(pg_num_rows($result));
            } else {
                $this->setCostiBilancio(null);
                $this->setNumCostiTrovati(0);
            }
        }
        $_SESSION[self::BILANCIO] = serialize($this);
    }

    /**
     * Questo metodo estrai i ricavi
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return unknown
     */
    public function ricercaRicavi($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_CON_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI;
        }

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%catconto%' => $this->getCatconto(),
            '%codnegozio%' => parent::isEmpty($this->getCodnegSel()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setRicaviBilancio(pg_fetch_all($result));
                $this->setNumRicaviTrovati(pg_num_rows($result));
            } else {
                $this->setRicaviBilancio(null);
                $this->setNumRicaviTrovati(0);
            }
        }
        $_SESSION[self::BILANCIO] = serialize($this);
    }

    /**
     * Questo metodo estrae le attività
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return unknown
     */
    public function ricercaAttivo($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::ATTIVO;

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%catconto%' => $this->getCatconto(),
            '%codnegozio%' => parent::isEmpty($this->getCodnegSel()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setAttivoBilancio(pg_fetch_all($result));
                $this->setNumAttivoTrovati(pg_num_rows($result));
            } else {
                $this->setAttivoBilancio(null);
                $this->setNumAttivoTrovati(0);
            }
        }
        $_SESSION[self::BILANCIO] = serialize($this);
    }

    /**
     * Questo metodo estrae le passività
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return unknown
     */
    public function ricercaPassivo($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::PASSIVO;

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%catconto%' => $this->getCatconto(),
            '%codnegozio%' => parent::isEmpty($this->getCodnegSel()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setPassivoBilancio(pg_fetch_all($result));
                $this->setNumPassivoTrovati(pg_num_rows($result));
            } else {
                $this->setPassivoBilancio(null);
                $this->setNumPassivoTrovati(0);
            }
        }
        $_SESSION[self::BILANCIO] = serialize($this);
    }

    /**
     * Questo metodo estrae i ricavi della vendita prodotti
     * @param unknown $db
     * @return unknown
     */
    public function ricercaRicaviMargineContribuzione($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_MARGINE_CONTRIBUZIONE_CON_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_MARGINE_CONTRIBUZIONE;
        }

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%catconto%' => $this->getCatconto(),
            '%codnegozio%' => parent::isEmpty($this->getCodnegSel()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setRicavoVenditaProdotti(pg_fetch_all($result));
            } else {
                $this->setRicavoVenditaProdotti(null);
            }
        }
        $_SESSION[self::BILANCIO] = serialize($this);
    }

    /**
     * Questo metodo estrae i costi variabili
     * @param unknown $db
     */
    public function ricercaCostiMargineContribuzione($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_MARGINE_CONTRIBUZIONE_CON_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_MARGINE_CONTRIBUZIONE;
        }

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%catconto%' => $this->getCatconto(),
            '%codnegozio%' => parent::isEmpty($this->getCodnegSel()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setCostoVariabile(pg_fetch_all($result));
            } else {
                $this->setCostoVariabile(null);
            }
        }
    }

    /**
     * Questo metodo estrae i costi fissi
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return unknown
     */
    public function ricercaCostiFissi($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_FISSI_CON_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_FISSI;
        }

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%codnegozio%' => parent::isEmpty($this->getCodnegSel()) ? "'VIL','TRE','BRE'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setCostoFisso(pg_fetch_all($result));
            } else {
                $this->setCostoFisso(null);
            }
        }
        $_SESSION[self::BILANCIO] = serialize($this);
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

    public function getCodnegSel() {
        return $this->codnegSel;
    }

    public function setCodnegSel($codnegSel) {
        $this->codnegSel = $codnegSel;
    }

    public function getCatconto() {
        return $this->catconto;
    }

    public function setCatconto($catconto) {
        $this->catconto = $catconto;
    }

    public function getSoloContoEconomico() {
        return $this->soloContoEconomico;
    }

    public function setSoloContoEconomico($soloContoEconomico) {
        $this->soloContoEconomico = $soloContoEconomico;
    }

    public function getCostiBilancio() {
        return $this->costiBilancio;
    }

    public function setCostiBilancio($costiBilancio) {
        $this->costiBilancio = $costiBilancio;
    }

    public function getRicaviBilancio() {
        return $this->ricaviBilancio;
    }

    public function setRicaviBilancio($ricaviBilancio) {
        $this->ricaviBilancio = $ricaviBilancio;
    }

    public function getAttivoBilancio() {
        return $this->attivoBilancio;
    }

    public function setAttivoBilancio($attivoBilancio) {
        $this->attivoBilancio = $attivoBilancio;
    }

    public function getPassivoBilancio() {
        return $this->passivoBilancio;
    }

    public function setPassivoBilancio($passivoBilancio) {
        $this->passivoBilancio = $passivoBilancio;
    }

    public function getNumCostiTrovati() {
        return $this->numCostiTrovati;
    }

    public function setNumCostiTrovati($numCostiTrovati) {
        $this->numCostiTrovati = $numCostiTrovati;
    }

    public function getSaldiInclusi() {
        return $this->saldiInclusi;
    }

    public function setSaldiInclusi($saldiInclusi) {
        $this->saldiInclusi = $saldiInclusi;
    }

    public function getNumRicaviTrovati() {
        return $this->numRicaviTrovati;
    }

    public function setNumRicaviTrovati($numRicaviTrovati) {
        $this->numRicaviTrovati = $numRicaviTrovati;
    }

    public function getCostoVariabile() {
        return $this->costoVariabile;
    }

    public function setCostoVariabile($costoVariabile) {
        $this->costoVariabile = $costoVariabile;
    }

    public function getNumAttivoTrovati() {
        return $this->numAttivoTrovati;
    }

    public function setNumAttivoTrovati($numAttivoTrovati) {
        $this->numAttivoTrovati = $numAttivoTrovati;
    }

    public function getNumPassivoTrovati() {
        return $this->numPassivoTrovati;
    }

    public function setNumPassivoTrovati($numPassivoTrovati) {
        $this->numPassivoTrovati = $numPassivoTrovati;
    }

    public function getRicavoVenditaProdotti() {
        return $this->ricavoVenditaProdotti;
    }

    public function setRicavoVenditaProdotti($ricavoVenditaProdotti) {
        $this->ricavoVenditaProdotti = $ricavoVenditaProdotti;
    }

    public function getCostoFisso() {
        return $this->costoFisso;
    }

    public function setCostoFisso($costoFisso) {
        $this->costoFisso = $costoFisso;
    }

    public function getTotaleCosti() {
        return $this->totaleCosti;
    }

    public function setTotaleCosti($totaleCosti) {
        $this->totaleCosti = $totaleCosti;
    }

    public function getTotaleRicavi() {
        return $this->totaleRicavi;
    }

    public function setTotaleRicavi($totaleRicavi) {
        $this->totaleRicavi = $totaleRicavi;
    }

    public function getTotaleAttivo() {
        return $this->totaleAttivo;
    }

    public function setTotaleAttivo($totaleAttivo) {
        $this->totaleAttivo = $totaleAttivo;
    }

    public function getTotalePassivo() {
        return $this->totalePassivo;
    }

    public function setTotalePassivo($totalePassivo) {
        $this->totalePassivo = $totalePassivo;
    }

    public function getTabellaCosti() {
        return $this->tabellaCosti;
    }

    public function setTabellaCosti($tabellaCosti) {
        $this->tabellaCosti = $tabellaCosti;
    }

    public function getTabellaRicavi() {
        return $this->tabellaRicavi;
    }

    public function setTabellaRicavi($tabellaRicavi) {
        $this->tabellaRicavi = $tabellaRicavi;
    }

    public function getTabellaAttivo() {
        return $this->tabellaAttivo;
    }

    public function setTabellaAttivo($tabellaAttivo) {
        $this->tabellaAttivo = $tabellaAttivo;
    }

    public function getTabellaPassivo() {
        return $this->tabellaPassivo;
    }

    public function setTabellaPassivo($tabellaPassivo) {
        $this->tabellaPassivo = $tabellaPassivo;
    }

    public function getTipoBilancio() {
        return $this->tipoBilancio;
    }

    public function setTipoBilancio($tipoBilancio) {
        $this->tipoBilancio = $tipoBilancio;
    }

    public function getAnnoEsercizioSel() {
        return $this->annoEsercizioSel;
    }

    public function setAnnoEsercizioSel($annoEsercizioSel) {
        $this->annoEsercizioSel = $annoEsercizioSel;
    }

}