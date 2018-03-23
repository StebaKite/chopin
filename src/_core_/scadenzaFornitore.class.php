<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class ScadenzaFornitore extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Scadenza

    const ID_SCADENZA = "id_scadenza";
    const ID_REGISTRAZIONE = "id_registrazione";
    const DAT_SCADENZA = "dat_scadenza";
    const IMP_IN_SCADENZA = "imp_in_scadenza";
    const NOTA_SCADENZA = "nota_scadenza";
    const TIP_ADDEBITO = "tip_addebito";
    const COD_NEGOZIO = "cod_negozio";
    const ID_FORNITORE = "id_fornitore";
    const NUM_FATTURA = "num_fattura";
    const STA_SCADENZA = "sta_scadenza";
    const ID_PAGAMENTO = "id_pagamento";
    // altri nomi generati

    const DAT_SCADENZA_YYYYMMDD = "dat_scadenza_yyyymmdd";

    // dati scadenza

    private $idScadenza;
    private $idRegistrazione;
    private $datScadenza;
    private $datScadenzaNuova;
    private $impInScadenza;
    private $notaScadenza;
    private $tipAddebito;
    private $codNegozio;
    private $idFornitore;
    private $numFattura;
    private $staScadenza;
    private $idPagamento;
    private $scadenzeDaPagare;
    private $qtaScadenzeDaPagare;
    private $scadenzePagate;
    private $qtaScadenzePagate;
    private $importoScadenza;
    private $idFornitoreOrig;
    private $numFatturaOrig;
    private $idTableScadenzeAperte;
    private $idTableScadenzeChiuse;
    // fitri di ricerca

    private $datScadenzaDa;
    private $datScadenzaA;
    private $codNegozioSel;
    private $staScadenzaSel;

    // Queries

    const CERCA_SCADENZE_FORNITORE = "/scadenze/ricercaScadenzeFornitore.sql";
    const CERCA_SCADENZE_REGISTRAZIONE = "/scadenze/ricercaScadenzeFornitoreRegistrazione.sql";
    const ELIMINA_SCADENZA_REGISTRAZIONE = "/scadenze/cancellaScadenzaFornitoreRegistrazione.sql";
    const CAMBIO_STATO_SCADENZA_FORNITORE = "/scadenze/updateStatoScadenzaFornitore.sql";
    const CREA_SCADENZA = "/scadenze/creaScadenzaFornitore.sql";
    const CANCELLA_SCADENZA = "/scadenze/cancellaScadenzaFornitore.sql";
    const AGGIORNA_SCADENZA = "/scadenze/aggiornaScadenzaFornitore.sql";
    const AGGIORNA_IMPORTO_SCADENZA_FORNITORE = "/scadenze/aggiornaImportoScadenzaFornitore.sql";
    const AGGIORNA_DATA_SCADENZA_FORNITORE = "/scadenze/aggiornaDataScadenzaFornitore.sql";
    const RICERCA_SCADENZE_DA_PAGARE = "/scadenze/ricercaScadenzeAperteFornitore.sql";
    const RICERCA_SCADENZE_PAGATE = "/scadenze/ricercaScadenzeChiuseFornitore.sql";
    const PAGA_SCADENZA = "/scadenze/pagaScadenzaFornitore.sql";
    const LEGGI_SCADENZA = "/scadenze/leggiScadenzaFornitore.sql";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public function getInstance() {

        if (!isset($_SESSION[self::SCADENZA_FORNITORE]))
            $_SESSION[self::SCADENZA_FORNITORE] = serialize(new ScadenzaFornitore());
        return unserialize($_SESSION[self::SCADENZA_FORNITORE]);
    }

    public function prepara() {
        $this->setDatScadenzaDa(date("d-m-Y"));
        $this->setDatScadenzaA(date("d-m-Y"));
        $this->setCodNegozioSel("VIL");
        $this->setQtaScadenzeDaPagare(0);
        $this->setScadenzeDaPagare("");
        $this->setQtaScadenzePagate(0);
        $this->setScadenzePagate("");
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
    }

    public function load($db) {
        /**
         * Colonne array scadenze
         *
         * 	id_scadenza,
         * 	id_registrazione,
         *  id_fornitore,
         * 	sta_registrazione,
         * 	des_fornitore,
         * 	dat_scadenza_yyyymmdd,
         * 	dat_scadenza,
         * 	dat_scadenza_originale,
         * 	imp_in_scadenza,
         * 	nota_scadenza,
         * 	tip_addebito,
         * 	num_fattura,
         * 	sta_scadenza,
         * 	id_pagamento
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $filtro = "";

        if (($this->getDatScadenzaDa() != "") & ($this->getDatScadenzaA() != "")) {
            $filtro = "AND scadenza.dat_scadenza between '" . $this->getDatScadenzaDa() . "' and '" . $this->getDatScadenzaA() . "'";
        }

        if ($this->getCodNegozioSel() != "") {
            $filtro .= " AND scadenza.cod_negozio = '" . $this->getCodNegozioSel() . "'";
        }

        if ($this->getStaScadenzaSel() != "") {
            $filtro .= " AND scadenza.sta_scadenza = '" . $this->getStaScadenzaSel() . "'";
        }

        $replace = array(
            '%filtro_date%' => $filtro
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_SCADENZE_FORNITORE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenzeDaPagare(pg_fetch_all($result));
            $this->setQtaScadenzeDaPagare(pg_num_rows($result));
        } else {
            $this->setScadenzeDaPagare(null);
            $this->setQtaScadenzeDaPagare(null);
        }
        return $result;
    }

    public function trovaScadenzeRegistrazione($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_SCADENZE_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenzeDaPagare(pg_fetch_all($result));
            $this->setQtaScadenzeDaPagare(pg_num_rows($result));
        } else {
            $this->setScadenzeDaPagare(null);
            $this->setQtaScadenzeDaPagare(0);
        }
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        return $result;
    }

    public function rimuoviScadenzeRegistrazione($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        foreach ($this->getScadenzeDaPagare() as $unaScadenza) {
            if ($unaScadenza[ScadenzaFornitore::STA_SCADENZA] == '00') {
                $this->setDatScadenza(strtotime(str_replace("/", "-", $unaScadenza[ScadenzaFornitore::DAT_SCADENZA])));
                $this->setIdFornitore($unaScadenza[ScadenzaFornitore::ID_FORNITORE]);
                $this->setNumFattura($unaScadenza[ScadenzaFornitore::NUM_FATTURA]);
                $this->cancella($db);
            }
        }
    }

    public function cambiaStato($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_pagamento%' => trim($this->getIdPagamento()),
            '%sta_scadenza%' => trim($this->getStaScadenza()),
            '%id_fornitore%' => trim($this->getIdFornitore()),
            '%num_fattura%' => trim($this->getNumFattura()),
            '%dat_scadenza%' => trim($this->getDatScadenza())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CAMBIO_STATO_SCADENZA_FORNITORE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        return $result;
    }

    public function aggiorna($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
            '%dat_scadenza%' => trim($this->getDatScadenza()),
            '%imp_in_scadenza%' => trim($this->getImpInScadenza()),
            '%nota_scadenza%' => trim($this->getNotaScadenza()),
            '%tip_addebito%' => trim($this->getTipAddebito()),
            '%cod_negozio%' => trim($this->getCodNegozio()),
            '%id_fornitore%' => trim($this->getIdFornitore()),
            '%id_fornitore_orig%' => trim($this->getIdFornitoreOrig()),
            '%num_fattura%' => trim($this->getNumFattura()),
            '%num_fattura_orig%' => trim($this->getNumFatturaOrig()),
            '%sta_scadenza%' => trim($this->getStaScadenza()),
            '%id_pagamento%' => trim($this->getIdPagamento())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_SCADENZA;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        return $result;
    }

    public function cambiaStatoScadenza($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_scadenza%' => trim($this->getIdScadenza()),
            '%sta_scadenza%' => trim($this->getStaScadenza()),
            '%id_pagamento%' => parent::isNotEmpty($this->getIdPagamento()) ? trim($this->getIdPagamento()) : parent::NULL_VALUE
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::PAGA_SCADENZA;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        return $result;
    }

    public function leggi($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_scadenza%' => trim($this->getIdScadenza())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_SCADENZA;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);


        if ($result) {
            foreach (pg_fetch_all($result) as $row) {
                $this->setIdRegistrazione($row[ScadenzaFornitore::ID_REGISTRAZIONE]);
                $this->setDatScadenza($row[ScadenzaFornitore::DAT_SCADENZA]);
                $this->setImpInScadenza($row[ScadenzaFornitore::IMP_IN_SCADENZA]);
                $this->setNotaScadenza($row[ScadenzaFornitore::NOTA_SCADENZA]);
                $this->setTipAddebito($row[ScadenzaFornitore::TIP_ADDEBITO]);
                $this->setNumFattura($row[ScadenzaFornitore::NUM_FATTURA]);
                $this->setStaScadenza($row[ScadenzaFornitore::STA_SCADENZA]);
                $this->setIdPagamento($row[ScadenzaFornitore::ID_PAGAMENTO]);
                $this->setCodNegozio($row[ScadenzaFornitore::COD_NEGOZIO]);
            }
        }
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        return $result;
    }

    public function aggiungiScadenzaPagata() {
        $item = array(
            ScadenzaFornitore::ID_FORNITORE => $this->getIdFornitore(),
            ScadenzaFornitore::DAT_SCADENZA => $this->getDatScadenza(),
            ScadenzaFornitore::IMP_IN_SCADENZA => $this->getImpInScadenza(),
            ScadenzaFornitore::NUM_FATTURA => $this->getNumFattura(),
            ScadenzaFornitore::NOTA_SCADENZA => $this->getNotaScadenza(),
            ScadenzaFornitore::ID_SCADENZA => $this->getIdScadenza()
        );

        if ($this->getQtaScadenzePagate() == 0) {
            $resultset = array();
            array_push($resultset, $item);
            $this->setScadenzePagate($resultset);
        } else {
            array_push($this->scadenzePagate, $item);
            sort($this->scadenzePagate);
        }
        $this->setQtaScadenzePagate($this->getQtaScadenzePagate() + 1);
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
    }

    public function rimuoviScadenzaPagata() {
        $scadenzePagateDiff = array();
        foreach ($this->getScadenzePagate() as $unaScadenza) {
            if (trim($unaScadenza[ScadenzaFornitore::NUM_FATTURA]) != trim($this->getNumFattura())
                    or trim($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]) != trim($this->getDatScadenza())) {
                array_push($scadenzePagateDiff, $unaScadenza);
            } else {
                $this->setQtaScadenzePagate($this->getQtaScadenzePagate() - 1);
            }
        }
        $this->setScadenzePagate($scadenzePagateDiff);
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
    }

    public function inserisci($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
            '%dat_scadenza%' => trim($this->getDatScadenza()),
            '%imp_in_scadenza%' => trim($this->getImpInScadenza()),
            '%nota_in_scadenza%' => trim($this->getNotaScadenza()),
            '%tip_addebito%' => trim($this->getTipAddebito()),
            '%cod_negozio%' => trim($this->getCodNegozio()),
            '%id_fornitore%' => parent::isNotEmpty($this->getIdFornitore()) ? trim($this->getIdFornitore()) : parent::NULL_VALUE,
            '%num_fattura%' => parent::isNotEmpty($this->getNumFattura()) ? "'" . trim($this->getNumFattura()) . "'" : parent::NULL_VALUE,
            '%sta_scadenza%' => trim($this->getStaScadenza())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_SCADENZA;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function dataScadenzaExist($datScadenza) {
        foreach ($this->getScadenzeDaPagare() as $unaScadenza) {
            if (trim($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]) == trim($datScadenza)) {
                return true;
            }
        }
        return false;
    }

    public function aggiungi() {
        if (!$this->dataScadenzaExist($this->getDatScadenza())) {
            $item = array(
                ScadenzaFornitore::ID_FORNITORE => $this->getIdFornitore(),
                ScadenzaFornitore::DAT_SCADENZA => $this->getDatScadenza(),
                ScadenzaFornitore::IMP_IN_SCADENZA => $this->getImpInScadenza(),
                ScadenzaFornitore::NUM_FATTURA => $this->getNumFattura(),
                ScadenzaFornitore::TIP_ADDEBITO => $this->getTipAddebito(),
                ScadenzaFornitore::STA_SCADENZA => self::SCADENZA_APERTA,
                ScadenzaFornitore::NOTA_SCADENZA => $this->getNotaScadenza()
            );

            if ($this->getQtaScadenzeDaPagare() == 0) {
                $resultset = array();
                array_push($resultset, $item);
                $this->setScadenzeDaPagare($resultset);
            } else {
                array_push($this->scadenzeDaPagare, $item);
                sort($this->scadenzeDaPagare);
            }
            $this->setQtaScadenzeDaPagare($this->getQtaScadenzeDaPagare() + 1);
            $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        }
    }

    public function cancella($db) {
        /**
         * Cancello la scadenza dalla tabella DB
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $dataScad = date("d-m-Y", trim($this->getDatScadenza()));

        $replace = array(
            '%dat_scadenza%' => $dataScad,
            '%id_fornitore%' => trim($this->getIdFornitore()),
            '%num_fattura%' => trim($this->getNumFattura())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_SCADENZA;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            /**
             * Elimino la scadenza dalla griglia in pagina
             */
            $scadenzeDiff = array();
            foreach ($this->getScadenzeDaPagare() as $unaScadenza) {
                if ((trim($unaScadenza[ScadenzaFornitore::ID_FORNITORE]) != trim($this->getIdFornitore()))
                        or ( trim($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]) != $dataScad)
                        or ( trim($unaScadenza[ScadenzaFornitore::NUM_FATTURA]) != trim($this->getNumFattura()))) {
                    array_push($scadenzeDiff, $unaScadenza);
                } else
                    $this->setQtaScadenzeDaPagare($this->getQtaScadenzeDaPagare() - 1);
            }
            $this->setScadenzeDaPagare($scadenzeDiff);
            $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        }
        return $result;
    }

    public function aggiornaImporto($db) {
        /**
         * Aggiorno l'importo in scadenza in scadenziario fornitori
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $dataScad = date("d-m-Y", trim($this->getDatScadenza()));

        $replace = array(
            '%imp_in_scadenza%' => $this->getImpInScadenza(),
            '%id_fornitore%' => trim($this->getIdFornitore()),
            '%dat_scadenza%' => $dataScad,
            '%num_fattura%' => $this->getNumFattura()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_IMPORTO_SCADENZA_FORNITORE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        /*
         * Se tutto ok aggiorno la scadenza nell'array delle scadenze
         */

        if ($result) {
            $scadenzeDiff = array();
            foreach ($this->getScadenzeDaPagare() as $unaScadenza) {

                if ($unaScadenza[ScadenzaFornitore::DAT_SCADENZA] != $dataScad)
                    array_push($scadenzeDiff, $unaScadenza);
                else {
                    $item = array(
                        ScadenzaFornitore::ID_SCADENZA => $unaScadenza[ScadenzaFornitore::ID_SCADENZA],
                        ScadenzaFornitore::DAT_SCADENZA => $unaScadenza[ScadenzaFornitore::DAT_SCADENZA],
                        ScadenzaFornitore::IMP_IN_SCADENZA => $this->getImpInScadenza(),
                        ScadenzaFornitore::NOTA_SCADENZA => $unaScadenza[ScadenzaFornitore::NOTA_SCADENZA],
                        ScadenzaFornitore::TIP_ADDEBITO => $unaScadenza[ScadenzaFornitore::TIP_ADDEBITO],
                        ScadenzaFornitore::COD_NEGOZIO => $unaScadenza[ScadenzaFornitore::COD_NEGOZIO],
                        ScadenzaFornitore::ID_FORNITORE => $unaScadenza[ScadenzaFornitore::ID_FORNITORE],
                        ScadenzaFornitore::NUM_FATTURA => $unaScadenza[ScadenzaFornitore::NUM_FATTURA],
                        ScadenzaFornitore::STA_SCADENZA => $unaScadenza[ScadenzaFornitore::STA_SCADENZA],
                        ScadenzaFornitore::ID_PAGAMENTO => $unaScadenza[ScadenzaFornitore::ID_PAGAMENTO]
                    );
                    array_push($scadenzeDiff, $item);
                }
            }
            $this->setScadenzeDaPagare($scadenzeDiff);
            $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        }
        return $result;
    }

    public function aggiornaData($db) {
        /**
         * Aggiorno la data di scadenza in scadenziario fornitori
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $dataScad = date("d-m-Y", trim($this->getDatScadenza()));

        $replace = array(
            '%dat_scadenza_nuova%' => $this->getDatScadenzaNuova(),
            '%id_fornitore%' => trim($this->getIdFornitore()),
            '%dat_scadenza%' => $dataScad,
            '%num_fattura%' => $this->getNumFattura()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_DATA_SCADENZA_FORNITORE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        /*
         * Se tutto ok aggiorno la scadenza nell'array delle scadenze
         */

        if ($result) {
            $scadenzeDiff = array();
            foreach ($this->getScadenzeDaPagare() as $unaScadenza) {

                if ($unaScadenza[ScadenzaFornitore::DAT_SCADENZA] != $dataScad)
                    array_push($scadenzeDiff, $unaScadenza);
                else {
                    $item = array(
                        ScadenzaFornitore::ID_SCADENZA => $unaScadenza[ScadenzaFornitore::ID_SCADENZA],
                        ScadenzaFornitore::DAT_SCADENZA => $this->getDatScadenzaNuova(),
                        ScadenzaFornitore::IMP_IN_SCADENZA => $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA],
                        ScadenzaFornitore::NOTA_SCADENZA => $unaScadenza[ScadenzaFornitore::NOTA_SCADENZA],
                        ScadenzaFornitore::TIP_ADDEBITO => $unaScadenza[ScadenzaFornitore::TIP_ADDEBITO],
                        ScadenzaFornitore::COD_NEGOZIO => $unaScadenza[ScadenzaFornitore::COD_NEGOZIO],
                        ScadenzaFornitore::ID_FORNITORE => $unaScadenza[ScadenzaFornitore::ID_FORNITORE],
                        ScadenzaFornitore::NUM_FATTURA => $unaScadenza[ScadenzaFornitore::NUM_FATTURA],
                        ScadenzaFornitore::STA_SCADENZA => $unaScadenza[ScadenzaFornitore::STA_SCADENZA],
                        ScadenzaFornitore::ID_PAGAMENTO => $unaScadenza[ScadenzaFornitore::ID_PAGAMENTO]
                    );
                    array_push($scadenzeDiff, $item);
                }
            }
            $this->setScadenzeDaPagare($scadenzeDiff);
            $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        }
        return $result;
    }

    public function trovaScadenzeDaPagare($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_fornitore%' => trim($this->getIdFornitore()),
            '%cod_negozio%' => trim($this->getCodNegozioSel())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_SCADENZE_DA_PAGARE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenzeDaPagare(pg_fetch_all($result));
            $this->setQtaScadenzeDaPagare(pg_num_rows($result));
        } else {
            $this->setScadenzeDaPagare(null);
            $this->setQtaScadenzeDaPagare(0);
        }
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        return $result;
    }

    public function trovaScadenzePagate($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_SCADENZE_PAGATE;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenzePagate(pg_fetch_all($result));
            $this->setQtaScadenzePagate(pg_num_rows($result));
        } else {
            $this->setScadenzePagate(null);
            $this->setQtaScadenzePagate(0);
        }
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($this);
        return $result;
    }

    /*     * **********************************************************************
     * Getters e setters
     */

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getIdScadenza() {
        return $this->idScadenza;
    }

    public function setIdScadenza($idScadenza) {
        $this->idScadenza = $idScadenza;
    }

    public function getIdRegistrazione() {
        return $this->idRegistrazione;
    }

    public function setIdRegistrazione($idRegistrazione) {
        $this->idRegistrazione = $idRegistrazione;
    }

    public function getDatScadenza() {
        return $this->datScadenza;
    }

    public function setDatScadenza($datScadenza) {
        $this->datScadenza = $datScadenza;
    }

    public function getImpInScadenza() {
        return $this->impInScadenza;
    }

    public function setImpInScadenza($impInScadenza) {
        $this->impInScadenza = $impInScadenza;
    }

    public function getNotaScadenza() {
        return $this->notaScadenza;
    }

    public function setNotaScadenza($notaScadenza) {
        $this->notaScadenza = $notaScadenza;
    }

    public function getTipAddebito() {
        return $this->tipAddebito;
    }

    public function setTipAddebito($tipAddebito) {
        $this->tipAddebito = $tipAddebito;
    }

    public function getCodNegozio() {
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio) {
        $this->codNegozio = $codNegozio;
    }

    public function getIdFornitore() {
        return $this->idFornitore;
    }

    public function setIdFornitore($idFornitore) {
        $this->idFornitore = $idFornitore;
    }

    public function getNumFattura() {
        return $this->numFattura;
    }

    public function setNumFattura($numFattura) {
        $this->numFattura = $numFattura;
    }

    public function getStaScadenza() {
        return $this->staScadenza;
    }

    public function setStaScadenza($staScadenza) {
        $this->staScadenza = $staScadenza;
    }

    public function getIdPagamento() {
        return $this->idPagamento;
    }

    public function setIdPagamento($idPagamento) {
        $this->idPagamento = $idPagamento;
    }

    public function getDatScadenzaDa() {
        return $this->datScadenzaDa;
    }

    public function setDatScadenzaDa($datScadenzaDa) {
        $this->datScadenzaDa = $datScadenzaDa;
    }

    public function getDatScadenzaA() {
        return $this->datScadenzaA;
    }

    public function setDatScadenzaA($datScadenzaA) {
        $this->datScadenzaA = $datScadenzaA;
    }

    public function getCodNegozioSel() {
        return $this->codNegozioSel;
    }

    public function setCodNegozioSel($codNegozioSel) {
        $this->codNegozioSel = $codNegozioSel;
    }

    public function getStaScadenzaSel() {
        return $this->staScadenzaSel;
    }

    public function setStaScadenzaSel($staScadenzaSel) {
        $this->staScadenzaSel = $staScadenzaSel;
    }

    public function getScadenzeDaPagare() {
        return $this->scadenzeDaPagare;
    }

    public function setScadenzeDaPagare($scadenze) {
        $this->scadenzeDaPagare = $scadenze;
    }

    public function getQtaScadenzeDaPagare() {
        return $this->qtaScadenzeDaPagare;
    }

    public function setQtaScadenzeDaPagare($qtaScadenze) {
        $this->qtaScadenzeDaPagare = $qtaScadenze;
    }

    public function getImportoScadenza() {
        return $this->importoScadenza;
    }

    public function setImportoScadenza($importoScadenza) {
        $this->importoScadenza = $importoScadenza;
    }

    public function getIdFornitoreOrig() {
        return $this->idFornitoreOrig;
    }

    public function setIdFornitoreOrig($idFornitoreOrig) {
        $this->idFornitoreOrig = $idFornitoreOrig;
    }

    public function getNumFatturaOrig() {
        return $this->numFatturaOrig;
    }

    public function setNumFatturaOrig($numFatturaOrig) {
        $this->numFatturaOrig = $numFatturaOrig;
    }

    public function getScadenzePagate() {
        return $this->scadenzePagate;
    }

    public function setScadenzePagate($scadenzePagate) {
        $this->scadenzePagate = $scadenzePagate;
        return $this;
    }

    public function getQtaScadenzePagate() {
        return $this->qtaScadenzePagate;
    }

    public function setQtaScadenzePagate($qtaScadenzePagate) {
        $this->qtaScadenzePagate = $qtaScadenzePagate;
        return $this;
    }

    public function getIdTableScadenzeAperte() {
        return $this->idTableScadenzeAperte;
    }

    public function setIdTableScadenzeAperte($idTableScadenzeAperte) {
        $this->idTableScadenzeAperte = $idTableScadenzeAperte;
        return $this;
    }

    public function getIdTableScadenzeChiuse() {
        return $this->idTableScadenzeChiuse;
    }

    public function setIdTableScadenzeChiuse($idTableScadenzeChiuse) {
        $this->idTableScadenzeChiuse = $idTableScadenzeChiuse;
        return $this;
    }

    public function getDatScadenzaNuova() {
        return $this->datScadenzaNuova;
    }

    public function setDatScadenzaNuova($datScadenzaNuova) {
        $this->datScadenzaNuova = $datScadenzaNuova;
        return $this;
    }

}

?>