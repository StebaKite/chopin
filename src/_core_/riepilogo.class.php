<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Riepilogo extends CoreBase implements CoreInterface {

    private $root;
    private $dataregDa;
    private $dataregA;
    private $codnegSel;
    private $soloContoEconomico;
    private $saldiInclusi;
    private $costiComparati;
    private $numCostiComparatiTrovati;
    private $ricaviComparati;
    private $numRicaviComparatiTrovati;
    private $attivoComparati;
    private $numAttivoComparatiTrovati;
    private $passivoComparati;
    private $numPassivoComparatiTrovati;
    private $costoVariabileTrezzo;
    private $costoVariabileBrembate;
    private $costoVariabileVilla;
    private $costoFissoTrezzo;
    private $costoFissoBrembate;
    private $costoFissoVilla;
    private $ricavoVenditaProdottiTrezzo;
    private $ricavoVenditaProdottiBrembate;
    private $ricavoVenditaProdottiVilla;
    private $tableCostiComparati;
    private $tableRicaviComparati;
    private $tableAttivoComparati;
    private $tablePassivoComparati;
    private $tableMctComparati;
    private $tableBepComparati;
    private $tableAndamentoCosti;
    private $tableAndamentoRicavi;
    private $tableUtilePerdita;
    private $tableMargineContribuzione;
    private $totaleCostiBrembate;
    private $totaleCostiTrezzo;
    private $totaleCostiVilla;
    private $totaleCosti;
    private $totaleRicaviBrembate;
    private $totaleRicaviTrezzo;
    private $totaleRicaviVilla;
    private $totaleRicavi;
    private $totaleAttivoBrembate;
    private $totaleAttivoTrezzo;
    private $totaleAttivoVilla;
    private $totaleAttivo;
    private $totalePassivoBrembate;
    private $totalePassivoTrezzo;
    private $totalePassivoVilla;
    private $totalePassivo;
    private $utileBrembate;
    private $utileTrezzo;
    private $utileVilla;
    private $totaleUtile;
    private $datiMCT;
    private $costiAndamentoNegozio;
    private $numCostiAndamentoNegozio;
    private $costiAndamentoNegozioRiferimento;
    private $numCostiAndamentoNegozioRiferimento;
    private $ricaviAndamentoNegozio;
    private $ricaviAndamentoMercatoTrezzo;
    private $ricaviAndamentoMercatoBrembate;
    private $ricaviAndamentoMercatoVilla;
    private $numRicaviAndamentoNegozio;
    private $numRicaviAndamentoMercatoTrezzo;
    private $numRicaviAndamentoMercatoBrembate;
    private $numRicaviAndamentoMercatoVilla;
    private $ricaviAndamentoNegozioRiferimento;
    private $numRicaviAndamentoNegozioRiferimento;
    private $totaliAcquistiMesi;
    private $totaliComplessiviAcquistiMesi;
    private $totaliComplessiviRicaviMesi;
    private $totaliRicaviMesi;

    /**
     *  Queries
     */
//    public static $queryCreaRegistrazione = "/riepilogho/estraiRegistrazioniBilancio.sql";
    const COSTI_COMPARATI = "/riepiloghi/costiComparati.sql";
    const COSTI_COMPARATI_CON_SALDI = "/riepiloghi/costiComparatiConSaldi.sql";
    const RICAVI_COMPARATI = "/riepiloghi/ricaviComparati.sql";
    const RICAVI_COMPARATI_CON_SALDI = "/riepiloghi/ricaviComparatiConSaldi.sql";
    const ATTIVO_COMPARATI = "/riepiloghi/attivoComparati.sql";
    const PASSIVO_COMPARATI = "/riepiloghi/passivoComparati.sql";
    const COSTI_MARGINE_CONTRIBUZIONE = "/riepiloghi/costiMargineContribuzione.sql";
    const COSTI_MARGINE_CONTRIBUZIONE_CON_SALDI = "/riepiloghi/costiMargineContribuzioneConSaldi.sql";
    const RICAVI_MARGINE_CONTRIBUZIONE = "/riepiloghi/ricaviMargineContribuzione.sql";
    const RICAVI_MARGINE_CONTRIBUZIONE_CON_SALDI = "/riepiloghi/ricaviMargineContribuzioneConSaldi.sql";
    const COSTI_FISSI = "/riepiloghi/costiFissi.sql";
    const COSTI_FISSI_CON_SALDI = "/riepiloghi/costiFissiConSaldi.sql";
    const COSTI_ANDAMENTO_NEGOZIO = "/riepiloghi/andamentoCostiNegozio.sql";
    const RICAVI_ANDAMENTO_NEGOZIO = "/riepiloghi/andamentoRicaviNegozio.sql";
    const RICAVI_ANDAMENTO_MERCATO = "/riepiloghi/andamentoRicaviMercato.sql";

    /**
     *  Metodi
     */
    function __construct() {
        $this->setRoot(parent::getInfoFromServer('DOCUMENT_ROOT'));
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RIEPILOGO) === NULL) {
            parent::setIndexSession(self::RIEPILOGO, serialize(new Riepilogo()));
        }
        return unserialize(parent::getIndexSession(self::RIEPILOGO));
    }

    public function prepara() {

        if (parent::isEmpty($this->getDataregDa())) {
            $this->setDataregDa(date("d-m-Y"));
        }

        if (parent::isEmpty($this->getDataregA())) {
            $this->setDataregA(date("d-m-Y"));
        }

        if (parent::isEmpty($this->getSoloContoEconomico())) {
            $this->setSoloContoEconomico(self::TUTTI_CONTI);
        }

        if (parent::isEmpty($this->getSaldiInclusi())) {
            $this->setSaldiInclusi(self::SALDI_INCLUSI);
        }

        $this->setCostiComparati(self::EMPTYSTRING);
        $this->setNumCostiComparatiTrovati(self::ZERO_VALUE);
        $this->setNumCostiAndamentoNegozio(self::ZERO_VALUE);
        $this->setNumRicaviAndamentoNegozio(self::ZERO_VALUE);
        $this->setNumRicaviAndamentoMercatoTrezzo(self::ZERO_VALUE);
        $this->setNumRicaviAndamentoMercatoBrembate(self::ZERO_VALUE);
        $this->setNumRicaviAndamentoMercatoVilla(self::ZERO_VALUE);
        $this->setRicaviAndamentoMercatoTrezzo(self::EMPTYSTRING);
        $this->setRicaviAndamentoMercatoBrembate(self::EMPTYSTRING);
        $this->setRicaviAndamentoMercatoVilla(self::EMPTYSTRING);
        $this->setRicaviComparati(self::EMPTYSTRING);
        $this->setNumRicaviComparatiTrovati(self::ZERO_VALUE);
        $this->setAttivoComparati(self::EMPTYSTRING);
        $this->setNumAttivoComparatiTrovati(self::ZERO_VALUE);
        $this->setPassivoComparati(self::ZERO_VALUE);
        $this->setNumPassivoComparatiTrovati(self::ZERO_VALUE);
        $this->setCostoVariabileTrezzo(self::EMPTYSTRING);
        $this->setCostoVariabileBrembate(self::EMPTYSTRING);
        $this->setCostoVariabileVilla(self::EMPTYSTRING);
        $this->setCostoFissoTrezzo(self::EMPTYSTRING);
        $this->setCostoFissoBrembate(self::EMPTYSTRING);
        $this->setCostoFissoVilla(self::EMPTYSTRING);
        $this->setRicavoVenditaProdottiTrezzo(self::EMPTYSTRING);
        $this->setRicavoVenditaProdottiBrembate(self::EMPTYSTRING);
        $this->setRicavoVenditaProdottiVilla(self::EMPTYSTRING);
        $this->setTableCostiComparati(self::EMPTYSTRING);
        $this->setTableRicaviComparati(self::EMPTYSTRING);
        $this->setTableAttivoComparati(self::EMPTYSTRING);
        $this->setTablePassivoComparati(self::EMPTYSTRING);
        $this->setTableMctComparati(self::EMPTYSTRING);
        $this->setTableBepComparati(self::EMPTYSTRING);

        $this->setTableAndamentoCosti(self::EMPTYSTRING);
        $this->setTableAndamentoRicavi(self::EMPTYSTRING);
        $this->setTableUtilePerdita(self::EMPTYSTRING);
        $this->setTableMargineContribuzione(self::EMPTYSTRING);

        $this->setTotaleCostiTrezzo(self::ZERO_VALUE);
        $this->setTotaleCostiVilla(self::ZERO_VALUE);
        $this->setTotaleCostiBrembate(self::ZERO_VALUE);
        $this->setTotaleCosti(self::ZERO_VALUE);
        $this->setTotaleRicaviBrembate(self::ZERO_VALUE);
        $this->setTotaleRicaviTrezzo(self::ZERO_VALUE);
        $this->setTotaleRicaviVilla(self::ZERO_VALUE);
        $this->setTotaleRicavi(self::ZERO_VALUE);
        $this->setTotaleAttivoBrembate(self::ZERO_VALUE);
        $this->setTotaleAttivoTrezzo(self::ZERO_VALUE);
        $this->setTotaleAttivoVilla(self::ZERO_VALUE);
        $this->setTotaleAttivo(self::ZERO_VALUE);
        $this->setTotalePassivoBrembate(self::ZERO_VALUE);
        $this->setTotalePassivoTrezzo(self::ZERO_VALUE);
        $this->setTotalePassivoVilla(self::ZERO_VALUE);
        $this->setTotalePassivo(self::ZERO_VALUE);
        $this->setTotaliAcquistiMesi(self::ZERO_VALUE);
        $this->setTotaliRicaviMesi(self::ZERO_VALUE);

        $this->setRicaviAndamentoMercatoTrezzo(self::EMPTYSTRING);
        $this->setRicaviAndamentoMercatoBrembate(self::EMPTYSTRING);
        $this->setRicaviAndamentoMercatoVilla(self::EMPTYSTRING);
        $this->setNumRicaviAndamentoMercatoTrezzo(self::ZERO_VALUE);
        $this->setNumRicaviAndamentoMercatoBrembate(self::ZERO_VALUE);
        $this->setNumRicaviAndamentoMercatoVilla(self::ZERO_VALUE);

        parent::setIndexSession(self::RIEPILOGO, serialize($this));
    }

    /**
     * Questo metodo estrae i costi di tutti i negozi e mette il resultset in sessione
     * @param unknown $db
     */
    public function ricercaCostiComparati($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_COMPARATI_CON_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_COMPARATI;
        }

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA()
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setCostiComparati(pg_fetch_all($result));
                $this->setNumCostiComparatiTrovati(pg_num_rows($result));
            } else {
                $this->setCostiComparati(self::EMPTYSTRING);
                $this->setNumCostiComparatiTrovati(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
            return $result;            
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae i ricavi di tutti i negozi e mette il resultset in sessione
     * @param unknown $db
     */
    public function ricercaRicaviComparati($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($this->getSaldiInclusi() == "S") {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_COMPARATI_CON_SALDI;
        } else {
            $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_COMPARATI;
        }

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA()
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setRicaviComparati(pg_fetch_all($result));
                $this->setNumRicaviComparatiTrovati(pg_num_rows($result));
            } else {
                $this->setRicaviComparati(self::EMPTYSTRING);
                $this->setNumRicaviComparatiTrovati(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
            return $result;            
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae le attivita' di tutti i negozi e mette il resultset in sessione
     * @param unknown $db
     */
    public function ricercaAttivoComparati($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::ATTIVO_COMPARATI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setAttivoComparati(pg_fetch_all($result));
                $this->setNumAttivoComparatiTrovati(pg_num_rows($result));
            } else {
                $this->setAttivoComparati(self::EMPTYSTRING);
                $this->setNumAttivoComparatiTrovati(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
            return $result;            
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae le passività di tutti i negozi e mette il resultset in sessione
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     */
    public function ricercaPassivoComparati($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::PASSIVO_COMPARATI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setPassivoComparati(pg_fetch_all($result));
                $this->setNumPassivoComparatiTrovati(pg_num_rows($result));
            } else {
                $this->setPassivoComparati(self::EMPTYSTRING);
                $this->setNumPassivoComparatiTrovati(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
            return $result;            
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo preleva i costi variabili di ciascun negozio
     * @param unknown $utility
     * @param unknown $db
     */
    public function ricercaCostiVariabiliNegozi($db) {

        $this->ricercaCostiMargineContribuzione($db, self::ERBA);
        parent::setIndexSession(self::RIEPILOGO, serialize($this));
    }

    /**
     * Questo metodo preleva i ricavi di ciascun negozio
     * @param unknown $utility
     * @param unknown $db
     */
    public function ricercaRicaviNegozi($db) {

        $this->ricercaRicaviMargineContribuzione($db, self::ERBA);
        parent::setIndexSession(self::RIEPILOGO, serialize($this));
    }

    /**
     * Questo metodo preleva i costi fissi di ciascun negozio
     * @param unknown $utility
     * @param unknown $db
     */
    public function ricercaCostiFissiNegozi($db) {

        $this->ricercaCostiFissi($db, self::ERBA);
        parent::setIndexSession(self::RIEPILOGO, serialize($this));
    }

    public function ricercaCostiMargineContribuzione($db, $negozio) {

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
            '%codnegozio%' => parent::quotation($negozio)
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                if ($negozio == self::ERBA) {
                    $this->setCostoVariabileVilla(pg_fetch_all($result));
                }
            } else {
                if ($negozio == self::ERBA) {
                    $this->setCostoVariabileVilla(null);
                }
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae i ricavi della vendita prodotti
     * @param unknown $db
     * @return unknown
     */
    public function ricercaRicaviMargineContribuzione($db, $negozio) {

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
            '%codnegozio%' => parent::quotation($negozio)
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                if ($negozio == self::ERBA) {
                    $this->setRicavoVenditaProdottiVilla(pg_fetch_all($result));
                }
            } else {
                if ($negozio == self::ERBA) {
                    $this->setRicavoVenditaProdottiVilla(null);
                }
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae i costi fissi
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return unknown
     */
    public function ricercaCostiFissi($db, $negozio) {

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
            '%codnegozio%' => parent::quotation($negozio)
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                if ($negozio == self::ERBA) {
                    $this->setCostoFissoVilla(pg_fetch_all($result));
                }
            } else {
                if ($negozio == self::ERBA) {
                    $this->setCostoFissoVilla(null);
                }
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae un riepilogo di totali per conto in Dare per mese
     * @param unknown $db
     */
    public function ricercaVociAndamentoCostiNegozio($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $sqlTemplate = $this->getRoot() . $array['query'] . self::COSTI_ANDAMENTO_NEGOZIO;

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%codnegozio%' => ($this->getCodnegSel() == "") ? "'" . self::ERBA . "'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setCostiAndamentoNegozio(pg_fetch_all($result));
                $this->setNumCostiAndamentoNegozio(pg_num_rows($result));
            } else {
                $this->setCostiAndamentoNegozio(self::EMPTYSTRING);
                $this->setNumCostiAndamentoNegozio(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae un riepilogo di totali per conto in Avere per mese
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     */
    public function ricercaVociAndamentoRicaviNegozio($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_ANDAMENTO_NEGOZIO;

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%codnegozio%' => ($this->getCodnegSel() == "") ? "'" . self::ERBA . "'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setRicaviAndamentoNegozio(pg_fetch_all($result));
                $this->setNumRicaviAndamentoNegozio(pg_num_rows($result));
            } else {
                $this->setRicaviAndamentoNegozio(self::EMPTYSTRING);
                $this->setNumRicaviAndamentoNegozio(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo ottiene una totalizzazione di ricavi per mese per negozio
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return number|string
     */
    public function ricercaVociAndamentoRicaviMercato($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_ANDAMENTO_MERCATO;

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%codnegozio%' => $this->getCodnegSel()
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                switch ($this->getCodnegSel()) {
                    case self::ERBA:
                        $this->setRicaviAndamentoMercatoVilla(pg_fetch_all($result));
                        $this->setNumRicaviAndamentoMercatoVilla(pg_num_rows($result));
                        break;
                }
            } else {
                switch ($this->getCodnegSel()) {
                    case self::ERBA:
                        $this->setRicaviAndamentoMercatoVilla(self::EMPTYSTRING);
                        $this->setNumRicaviAndamentoMercatoVilla(self::ZERO_VALUE);
                        break;
                }
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));            
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrae un riepilogo di totali per conto in Dare per mese
     * @param unknown $utility
     * @param unknown $db
     * @param unknown $replace
     * @return unknown
     */
    public function ricercaVociAndamentoCostiNegozioRiferimento($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $sqlTemplate = self::$root . $array['query'] . self::COSTI_ANDAMENTO_NEGOZIO;

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%codnegozio%' => ($this->getCodnegSel() == "") ? "'" . self::ERBA . "'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setRicaviAndamentoNegozioRiferimento(pg_fetch_all($result));
                $this->setNumRicaviAndamentoNegozioRiferimento(pg_num_rows($result));
            } else {
                $this->setRicaviAndamentoNegozioRiferimento(self::EMPTYSTRING);
                $this->setNumRicaviAndamentoNegozioRiferimento(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    /**
     * Questo metodo estrai un riepilogo di totali per conto in Avere per mese
     * @param unknown $db
     */
    public function ricercaVociAndamentoRicaviNegozioRiferimento($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICAVI_ANDAMENTO_NEGOZIO;

        $replace = array(
            '%datareg_da%' => $this->getDataregDa(),
            '%datareg_a%' => $this->getDataregA(),
            '%codnegozio%' => ($this->getCodnegSel() == "") ? "'" . self::ERBA . "'" : "'" . $this->getCodnegSel() . "'"
        );

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $this->setRicaviAndamentoNegozioRiferimento(pg_fetch_all($result));
                $this->setNumRicaviAndamentoNegozioRiferimento(pg_num_rows($result));
            } else {
                $this->setRicaviAndamentoNegozioRiferimento(self::EMPTYSTRING);
                $this->setNumRicaviAndamentoNegozioRiferimento(self::ZERO_VALUE);
            }
            parent::setIndexSession(self::RIEPILOGO, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
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

    public function getSaldiInclusi() {
        return $this->saldiInclusi;
    }

    public function setSaldiInclusi($saldiInclusi) {
        $this->saldiInclusi = $saldiInclusi;
    }

    public function getSoloContoEconomico() {
        return $this->soloContoEconomico;
    }

    public function setSoloContoEconomico($soloContoEconomico) {
        $this->soloContoEconomico = $soloContoEconomico;
    }

    public function getCostiComparati() {
        return $this->costiComparati;
    }

    public function setCostiComparati($costiComparati) {
        $this->costiComparati = $costiComparati;
    }

    public function getNumCostiComparatiTrovati() {
        return $this->numCostiComparatiTrovati;
    }

    public function setNumCostiComparatiTrovati($numCostiComparatiTrovati) {
        $this->numCostiComparatiTrovati = $numCostiComparatiTrovati;
    }

    public function getRicaviComparati() {
        return $this->ricaviComparati;
    }

    public function setRicaviComparati($ricaviComparati) {
        $this->ricaviComparati = $ricaviComparati;
    }

    public function getNumRicaviComparatiTrovati() {
        return $this->numRicaviComparati;
    }

    public function setNumRicaviComparatiTrovati($numRicaviComparatiTrovati) {
        $this->numRicaviComparatiTrovati = $numRicaviComparatiTrovati;
    }

    public function getAttivoComparati() {
        return $this->attivoComparati;
    }

    public function setAttivoComparati($attivoComparati) {
        $this->attivoComparati = $attivoComparati;
    }

    public function getNumAttivoComparatiTrovati() {
        return $this->numAttivoComparati;
    }

    public function setNumAttivoComparatiTrovati($numAttivoComparatiTrovati) {
        $this->numAttivoComparatiTrovati = $numAttivoComparatiTrovati;
    }

    public function getPassivoComparati() {
        return $this->passivoComparati;
    }

    public function setPassivoComparati($passivoComparati) {
        $this->passivoComparati = $passivoComparati;
    }

    public function getNumPassivoComparatiTrovati() {
        return $this->numPassivoComparati;
    }

    public function setNumPassivoComparatiTrovati($numPassivoComparatiTrovati) {
        $this->numPassivoComparatiTrovati = $numPassivoComparatiTrovati;
    }

    public function getCostoVariabileTrezzo() {
        return $this->costoVariabileTrezzo;
    }

    public function setCostoVariabileTrezzo($costoVariabileTrezzo) {
        $this->costoVariabileTrezzo = $costoVariabileTrezzo;
    }

    public function getCostoVariabileBrembate() {
        return $this->costoVariabileBrembate;
    }

    public function setCostoVariabileBrembate($costoVariabileBrembate) {
        $this->costoVariabileBrembate = $costoVariabileBrembate;
    }

    public function getCostoVariabileVilla() {
        return $this->costoVariabileVilla;
    }

    public function setCostoVariabileVilla($costoVariabileVilla) {
        $this->costoVariabileVilla = $costoVariabileVilla;
    }

    public function getRicavoVenditaProdottiTrezzo() {
        return $this->ricavoVenditaProdottiTrezzo;
    }

    public function setRicavoVenditaProdottiTrezzo($ricavoVenditaProdottiTrezzo) {
        $this->ricavoVenditaProdottiTrezzo = $ricavoVenditaProdottiTrezzo;
    }

    public function getRicavoVenditaProdottiBrembate() {
        return $this->ricavoVenditaProdottiBrembate;
    }

    public function setRicavoVenditaProdottiBrembate($ricavoVenditaProdottiBrembate) {
        $this->ricavoVenditaProdottiBrembate = $ricavoVenditaProdottiBrembate;
    }

    public function getRicavoVenditaProdottiVilla() {
        return $this->ricavoVenditaProdottiVilla;
    }

    public function setRicavoVenditaProdottiVilla($ricavoVenditaProdottiVilla) {
        $this->ricavoVenditaProdottiVilla = $ricavoVenditaProdottiVilla;
    }

    public function getCostoFissoTrezzo() {
        return $this->costoFissoTrezzo;
    }

    public function setCostoFissoTrezzo($costoFissoTrezzo) {
        $this->costoFissoTrezzo = $costoFissoTrezzo;
    }

    public function getCostoFissoBrembate() {
        return $this->costoFissoBrembate;
    }

    public function setCostoFissoBrembate($costoFissoBrembate) {
        $this->costoFissoBrembate = $costoFissoBrembate;
    }

    public function getCostoFissoVilla() {
        return $this->costoFissoVilla;
    }

    public function setCostoFissoVilla($costoFissoVilla) {
        $this->costoFissoVilla = $costoFissoVilla;
    }

    public function getTableCostiComparati() {
        return $this->tableCostiComparati;
    }

    public function setTableCostiComparati($tableCostiComparati) {
        $this->tableCostiComparati = $tableCostiComparati;
    }

    public function getTableRicaviComparati() {
        return $this->tableRicaviComparati;
    }

    public function setTableRicaviComparati($tableRicaviComparati) {
        $this->tableRicaviComparati = $tableRicaviComparati;
    }

    public function getTableAttivoComparati() {
        return $this->tableAttivoComparati;
    }

    public function setTableAttivoComparati($tableAttivoComparati) {
        $this->tableAttivoComparati = $tableAttivoComparati;
    }

    public function getTablePassivoComparati() {
        return $this->tablePassivoComparati;
    }

    public function setTablePassivoComparati($tablePassivoComparati) {
        $this->tablePassivoComparati = $tablePassivoComparati;
    }

    public function getTableMctComparati() {
        return $this->tableMctComparati;
    }

    public function setTableMctComparati($tableMctComparati) {
        $this->tableMctComparati = $tableMctComparati;
    }

    public function getTableBepComparati() {
        return $this->tableBepComparati;
    }

    public function setTableBepComparati($tableBepComparati) {
        $this->tableBepComparati = $tableBepComparati;
    }

    public function getTotaleCostiBrembate() {
        return $this->totaleCostiBrembate;
    }

    public function setTotaleCostiBrembate($totaleCostiBrembate) {
        $this->totaleCostiBrembate = $totaleCostiBrembate;
    }

    public function getTotaleCostiTrezzo() {
        return $this->totaleCostiTrezzo;
    }

    public function setTotaleCostiTrezzo($totaleCostiTrezzo) {
        $this->totaleCostiTrezzo = $totaleCostiTrezzo;
    }

    public function getTotaleCostiVilla() {
        return $this->totaleCostiVilla;
    }

    public function setTotaleCostiVilla($totaleCostiVilla) {
        $this->totaleCostiVilla = $totaleCostiVilla;
    }

    public function getTotaleCosti() {
        return $this->totaleCosti;
    }

    public function setTotaleCosti($totaleCosti) {
        $this->totaleCosti = $totaleCosti;
    }

    public function getTotaleRicaviBrembate() {
        return $this->totaleRicaviBrembate;
    }

    public function setTotaleRicaviBrembate($totaleRicaviBrembate) {
        $this->totaleRicaviBrembate = $totaleRicaviBrembate;
    }

    public function getTotaleRicaviTrezzo() {
        return $this->totaleRicaviTrezzo;
    }

    public function setTotaleRicaviTrezzo($totaleRicaviTrezzo) {
        $this->totaleRicaviTrezzo = $totaleRicaviTrezzo;
    }

    public function getTotaleRicaviVilla() {
        return $this->totaleRicaviVilla;
    }

    public function setTotaleRicaviVilla($totaleRicaviVilla) {
        $this->totaleRicaviVilla = $totaleRicaviVilla;
    }

    public function getTotaleRicavi() {
        return $this->totaleRicavi;
    }

    public function setTotaleRicavi($totaleRicavi) {
        $this->totaleRicavi = $totaleRicavi;
    }

    public function getTotaleAttivoBrembate() {
        return $this->totaleAttivoBrembate;
    }

    public function setTotaleAttivoBrembate($totaleAttivoBrembate) {
        $this->totaleAttivoBrembate = $totaleAttivoBrembate;
    }

    public function getTotaleAttivoTrezzo() {
        return $this->totaleAttivoTrezzo;
    }

    public function setTotaleAttivoTrezzo($totaleAttivoTrezzo) {
        $this->totaleAttivoTrezzo = $totaleAttivoTrezzo;
    }

    public function getTotaleAttivoVilla() {
        return $this->totaleAttivoVilla;
    }

    public function setTotaleAttivoVilla($totaleAttivoVilla) {
        $this->totaleAttivoVilla = $totaleAttivoVilla;
    }

    public function getTotaleAttivo() {
        return $this->totaleAttivo;
    }

    public function setTotaleAttivo($totaleAttivo) {
        $this->totaleAttivo = $totaleAttivo;
    }

    public function getTotalePassivoBrembate() {
        return $this->totalePassivoBrembate;
    }

    public function setTotalePassivoBrembate($totalePassivoBrembate) {
        $this->totalePassivoBrembate = $totalePassivoBrembate;
    }

    public function getTotalePassivoTrezzo() {
        return $this->totalePassivoTrezzo;
    }

    public function setTotalePassivoTrezzo($totalePassivoTrezzo) {
        $this->totalePassivoTrezzo = $totalePassivoTrezzo;
    }

    public function getTotalePassivoVilla() {
        return $this->totalePassivoVilla;
    }

    public function setTotalePassivoVilla($totalePassivoVilla) {
        $this->totalePassivoVilla = $totalePassivoVilla;
    }

    public function getTotalePassivo() {
        return $this->totalePassivo;
    }

    public function setTotalePassivo($totalePassivo) {
        $this->totalePassivo = $totalePassivo;
    }

    public function getUtileBrembate() {
        return $this->utileBrembate;
    }

    public function setUtileBrembate($utileBrembate) {
        $this->utileBrembate = $utileBrembate;
    }

    public function getUtileTrezzo() {
        return $this->utileTrezzo;
    }

    public function setUtileTrezzo($utileTrezzo) {
        $this->utileTrezzo = $utileTrezzo;
    }

    public function getUtileVilla() {
        return $this->utileVilla;
    }

    public function setUtileVilla($utileVilla) {
        $this->utileVilla = $utileVilla;
    }

    public function getTotaleUtile() {
        return $this->totaleUtile;
    }

    public function setTotaleUtile($totaleUtile) {
        $this->totaleUtile = $totaleUtile;
    }

    public function getDatiMCT() {
        return $this->datiMCT;
    }

    public function setDatiMCT($datiMCT) {
        $this->datiMCT = $datiMCT;
    }

    public function getRicaviAndamentoNegozio() {
        return $this->ricaviAndamentoNegozio;
    }

    public function setRicaviAndamentoNegozio($ricaviAndamentoNegozio) {
        $this->ricaviAndamentoNegozio = $ricaviAndamentoNegozio;
    }

    public function getCostiAndamentoNegozio() {
        return $this->costiAndamentoNegozio;
    }

    public function setCostiAndamentoNegozio($costiAndamentoNegozio) {
        $this->costiAndamentoNegozio = $costiAndamentoNegozio;
    }

    public function getNumRicaviAndamentoNegozio() {
        return $this->numRicaviAndamentoNegozio;
    }

    public function setNumRicaviAndamentoNegozio($numRicaviAndamentoNegozio) {
        $this->numRicaviAndamentoNegozio = $numRicaviAndamentoNegozio;
    }

    public function getNumCostiAndamentoNegozio() {
        return $this->numCostiAndamentoNegozio;
    }

    public function setNumCostiAndamentoNegozio($numCostiAndamentoNegozio) {
        $this->numCostiAndamentoNegozio = $numCostiAndamentoNegozio;
    }

    public function getCostiAndamentoNegozioRiferimento() {
        return $this->costiAndamentoNegozioRiferimento;
    }

    public function setCostiAndamentoNegozioRiferimento($costiAndamentoNegozioRiferimento) {
        $this->costiAndamentoNegozioRiferimento = $costiAndamentoNegozioRiferimento;
    }

    public function getNumCostiAndamentoNegozioRiferimento() {
        return $this->numCostiAndamentoNegozioRiferimento;
    }

    public function setNumCostiAndamentoNegozioRiferimento($numCostiAndamentoNegozioRiferimento) {
        $this->numCostiAndamentoNegozioRiferimento = $numCostiAndamentoNegozioRiferimento;
    }

    public function getRicaviAndamentoNegozioRiferimento() {
        return $this->ricaviAndamentoNegozioRiferimento;
    }

    public function setRicaviAndamentoNegozioRiferimento($ricaviAndamentoNegozioRiferimento) {
        $this->numCostiAndamentoNegozioRiferimento = $ricaviAndamentoNegozioRiferimento;
    }

    public function getNumRicaviAndamentoNegozioRiferimento() {
        return $this->numRicaviAndamentoNegozioRiferimento;
    }

    public function setNumRicaviAndamentoNegozioRiferimento($numRicaviAndamentoNegozioRiferimento) {
        $this->numRicaviAndamentoNegozioRiferimento = $numRicaviAndamentoNegozioRiferimento;
    }

    public function getTotaliAcquistiMesi() {
        return $this->totaliAcquistiMesi;
    }

    public function setTotaliAcquistiMesi($totaliAcquistiMesi) {
        $this->totaliAcquistiMesi = $totaliAcquistiMesi;
    }

    public function getTotaliComplessiviAcquistiMesi() {
        return $this->totaliComplessiviAcquistiMesi;
    }

    public function setTotaliComplessiviAcquistiMesi($totaliComplessiviAcquistiMesi) {
        $this->totaliComplessiviAcquistiMesi = $totaliComplessiviAcquistiMesi;
    }

    public function getTableAndamentoCosti() {
        return $this->tableAndamentoCosti;
    }

    public function setTableAndamentoCosti($tableAndamentoCosti) {
        $this->tableAndamentoCosti = $tableAndamentoCosti;
    }

    public function getTableAndamentoRicavi() {
        return $this->tableAndamentoRicavi;
    }

    public function setTableAndamentoRicavi($tableAndamentoRicavi) {
        $this->tableAndamentoRicavi = $tableAndamentoRicavi;
    }

    public function getTotaliComplessiviRicaviMesi() {
        return $this->totaliComplessiviRicaviMesi;
    }

    public function setTotaliComplessiviRicaviMesi($totaliComplessiviRicaviMesi) {
        $this->totaliComplessiviRicaviMesi = $totaliComplessiviRicaviMesi;
    }

    public function getTotaliRicaviMesi() {
        return $this->totaliRicaviMesi;
    }

    public function setTotaliRicaviMesi($totaliRicaviMesi) {
        $this->totaliRicaviMesi = $totaliRicaviMesi;
    }

    public function getTableUtilePerdita() {
        return $this->tableUtilePerdita;
    }

    public function setTableUtilePerdita($tableUtilePerdita) {
        $this->tableUtilePerdita = $tableUtilePerdita;
    }

    public function getTableMargineContribuzione() {
        return $this->tableMargineContribuzione;
    }

    public function setTableMargineContribuzione($tableMargineContribuzione) {
        $this->tableMargineContribuzione = $tableMargineContribuzione;
    }

    public function getRicaviAndamentoMercatoTrezzo() {
        return $this->ricaviAndamentoMercatoTrezzo;
    }

    public function setRicaviAndamentoMercatoTrezzo($ricaviAndamentoMercato) {
        $this->ricaviAndamentoMercatoTrezzo = $ricaviAndamentoMercato;
    }

    public function getRicaviAndamentoMercatoBrembate() {
        return $this->ricaviAndamentoMercatoBrembate;
    }

    public function setRicaviAndamentoMercatoBrembate($ricaviAndamentoMercato) {
        $this->ricaviAndamentoMercatoBrembate = $ricaviAndamentoMercato;
    }

    public function getRicaviAndamentoMercatoVilla() {
        return $this->ricaviAndamentoMercatoVilla;
    }

    public function setRicaviAndamentoMercatoVilla($ricaviAndamentoMercato) {
        $this->ricaviAndamentoMercatoVilla = $ricaviAndamentoMercato;
    }

    public function getNumRicaviAndamentoMercatoTrezzo() {
        return $this->numRicaviAndamentoMercatoTrezzo;
    }

    public function setNumRicaviAndamentoMercatoTrezzo($numRicaviAndamentoMercato) {
        $this->numRicaviAndamentoMercatoTrezzo = $numRicaviAndamentoMercato;
    }

    public function getNumRicaviAndamentoMercatoBrembate() {
        return $this->numRicaviAndamentoMercatoBrembate;
    }

    public function setNumRicaviAndamentoMercatoBrembate($numRicaviAndamentoMercato) {
        $this->numRicaviAndamentoMercatoBrembate = $numRicaviAndamentoMercato;
    }

    public function getNumRicaviAndamentoMercatoVilla() {
        return $this->numRicaviAndamentoMercatoVilla;
    }

    public function setNumRicaviAndamentoMercatoVilla($numRicaviAndamentoMercato) {
        $this->numRicaviAndamentoMercatoVilla = $numRicaviAndamentoMercato;
    }

}