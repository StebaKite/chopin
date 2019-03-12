<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class ScadenzaCliente extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella ScadenzaCliente

    const ID_SCADENZA = 'id_scadenza';
    const ID_REGISTRAZIONE = 'id_registrazione';
    const DAT_REGISTRAZIONE = 'dat_registrazione';
    const IMP_REGISTRAZIONE = 'imp_registrazione';
    const NOTA = 'nota';
    const TIP_ADDEBITO = 'tip_addebito';
    const COD_NEGOZIO = 'cod_negozio';
    const ID_CLIENTE = 'id_cliente';
    const NUM_FATTURA = 'num_fattura';
    const STA_SCADENZA = 'sta_scadenza';
    const ID_INCASSO = 'id_incasso';
    // altri nomi generati

    const DAT_SCADENZA_YYYYMMDD = "dat_scadenza_yyyymmdd";

    // dati scadenzaCliente

    private $idScadenza;
    private $idRegistrazione;
    private $datRegistrazione;
    private $datScadenzaNuova;
    private $impRegistrazione;
    private $nota;
    private $tipAddebito;
    private $codNegozio;
    private $idCliente;
    private $numFattura;
    private $staScadenza;
    private $idIncasso;
    private $scadenze;
    private $qtaScadenze;
    private $importoScadenza;
    private $idClienteOrig;
    private $numFatturaOrig;
    private $idTableScadenzeAperte;
    private $idTableScadenzeChiuse;
    private $scadenzeDaIncassare;
    private $qtaScadenzeDaIncassare;
    private $scadenzeIncassate;
    private $qtaScadenzeIncassate;
    // fitri di ricerca

    private $datScadenzaDa;
    private $datScadenzaA;
    private $codNegozioSel;
    private $staScadenzaSel;

    // Queries

    const CERCA_SCADENZE_CLIENTE = "/scadenze/ricercaScadenzeCliente.sql";
    const CERCA_SCADENZE_REGISTRAZIONE = "/scadenze/ricercaScadenzeClienteRegistrazione.sql";
    const CREA_SCADENZA = "/scadenze/creaScadenzaCliente.sql";
    const CANCELLA_SCADENZA = "/scadenze/cancellaScadenzaCliente.sql";
    const AGGIORNA_SCADENZA = "/scadenze/aggiornaScadenzaCliente.sql";
    const AGGIORNA_IMPORTO_SCADENZA_CLIENTE = "/scadenze/aggiornaImportoScadenzaCliente.sql";
    const AGGIORNA_DATA_SCADENZA_CLIENTE = "/scadenze/aggiornaDataScadenzaCliente.sql";
    const RICERCA_SCADENZE_DA_INCASSARE = "/scadenze/ricercaScadenzeAperteCliente.sql";
    const RICERCA_SCADENZE_INCASSATE = "/scadenze/ricercaScadenzeChiuseCliente.sql";
    const CAMBIO_STATO_SCADENZA_CLIENTE = "/scadenze/updateStatoScadenzaCliente.sql";
    const LEGGI_SCADENZA = "/scadenze/leggiScadenzaCliente.sql";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::SCADENZA_CLIENTE])) {
            $_SESSION[self::SCADENZA_CLIENTE] = serialize(new ScadenzaCliente());
        }
        return unserialize($_SESSION[self::SCADENZA_CLIENTE]);
    }

    public function prepara() {
        $this->setDatScadenzaDa(date("d-m-Y"));
        $this->setDatScadenzaA(date("d-m-Y"));
        $this->setCodNegozioSel("VIL");
        $this->setQtaScadenze(0);
        $this->setScadenze("");
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
    }

    public function load($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $filtro = "";

        if (($this->getDatScadenzaDa() != "") & ($this->getDatScadenzaA() != "")) {
            $filtro = "AND scadenza_cliente.dat_registrazione between '" . $this->getDatScadenzaDa() . "' and '" . $this->getDatScadenzaA() . "'";
        }

        if ($this->getCodNegozioSel() != "") {
            $filtro .= " AND scadenza_cliente.cod_negozio = '" . $this->getCodNegozioSel() . "'";
        }

        if ($this->getStaScadenzaSel() != "") {
            $filtro .= " AND scadenza_cliente.sta_scadenza = '" . $this->getStaScadenzaSel() . "'";
        }

        $replace = array(
            '%filtro_date%' => $filtro
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_SCADENZE_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenze(pg_fetch_all($result));
            $this->setQtaScadenze(pg_num_rows($result));
        } else {
            $this->setScadenze(null);
            $this->setQtaScadenze(null);
        }
        return $result;
    }

    public function leggi($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_scadenza%' => trim($this->getIdScadenza())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_SCADENZA;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);


        if ($result) {
            foreach (pg_fetch_all($result) as $row) {
                $this->setIdRegistrazione($row[ScadenzaCliente::ID_REGISTRAZIONE]);
                $this->setDatRegistrazione($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
                $this->setImpRegistrazione($row[ScadenzaCliente::IMP_REGISTRAZIONE]);
                $this->setNota($row[ScadenzaCliente::NOTA]);
                $this->setCodNegozio($row[ScadenzaCliente::COD_NEGOZIO]);
                $this->setIdCliente($row[ScadenzaCliente::ID_CLIENTE]);
                $this->setTipAddebito($row[ScadenzaCliente::TIP_ADDEBITO]);
                $this->setNumFattura($row[ScadenzaCliente::NUM_FATTURA]);
                $this->setStaScadenza($row[ScadenzaCliente::STA_SCADENZA]);
                $this->setIdIncasso($row[ScadenzaCliente::ID_INCASSO]);
            }
        }

        $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
        return $result;
    }

    public function aggiungiScadenzaIncassata() {
        $item = array(
            ScadenzaCliente::ID_CLIENTE => $this->getIdCliente(),
            ScadenzaCliente::DAT_REGISTRAZIONE => $this->getDatRegistrazione(),
            ScadenzaCliente::IMP_REGISTRAZIONE => $this->getImpRegistrazione(),
            ScadenzaCliente::NUM_FATTURA => $this->getNumFattura(),
            ScadenzaCliente::NOTA => $this->getNota(),
            ScadenzaCliente::ID_SCADENZA => $this->getIdScadenza()
        );

        if ($this->getQtaScadenzeIncassate() == 0) {
            $resultset = array();
            array_push($resultset, $item);
            $this->setScadenzeIncassate($resultset);
        } else {
            array_push($this->scadenzeIncassate, $item);
            sort($this->scadenzeIncassate);
        }
        $this->setQtaScadenzeIncassate($this->getQtaScadenzeIncassate() + 1);
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
    }

    public function trovaScadenzeRegistrazione($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_SCADENZE_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenzeDaIncassare(pg_fetch_all($result));
            $this->setQtaScadenzeDaIncassare(pg_num_rows($result));
        } else {
            $this->setScadenzeDaIncassare(null);
            $this->setQtaScadenzeDaIncassare(0);
        }
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
        return $result;
    }

    public function dataScadenzaExist($datScadenza) {
        foreach ($this->getQtaScadenzeDaIncassare() as $unaScadenza) {
            if (trim($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]) == trim($datScadenza)) {
                return true;
            }
        }
        return false;
    }

    public function getSommaImportiScadenzeIncassate() {
        
        $importoTotaleScadenze = 0;

        foreach ($this->getScadenzeIncassate() as $unaScadenza) {
            $importoTotaleScadenze += $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE];
        }
        return $importoTotaleScadenze;
    }    

    public function aggiungi() {
        if (!$this->dataScadenzaExist($this->getDatRegistrazione())) {
            $item = array(
                ScadenzaCliente::ID_CLIENTE => $this->getIdCliente(),
                ScadenzaCliente::ID_SCADENZA => 0,
                ScadenzaCliente::DAT_REGISTRAZIONE => $this->getDatRegistrazione(),
                ScadenzaCliente::IMP_REGISTRAZIONE => $this->getImpRegistrazione(),
                ScadenzaCliente::NUM_FATTURA => $this->getNumFattura(),
                ScadenzaCliente::TIP_ADDEBITO => $this->getTipAddebito(),
            );

            if ($this->getQtaScadenzeDaIncassare() == 0) {
                $resultset = array();
                array_push($resultset, $item);
                $this->setScadenzeDaIncassare($resultset);
            } else {
                array_push($this->scadenzeDaIncassare, $item);
                sort($this->scadenzeDaIncassare);
            }
            $this->setQtaScadenzeDaIncassare($this->getQtaScadenzeDaIncassare() + 1);
            $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
        }
    }

    public function cambiaStato($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_incasso%' => parent::isEmpty($this->getIdIncasso()) ? parent::NULL_VALUE : trim($this->getIdIncasso()),
            '%sta_scadenza%' => trim($this->getStaScadenza()),
            '%id_scadenza%' => trim($this->getIdScadenza())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CAMBIO_STATO_SCADENZA_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function aggiorna($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
            '%dat_registrazione%' => trim($this->getDatRegistrazione()),
            '%imp_registrazione%' => trim($this->getImpRegistrazione()),
            '%nota%' => trim($this->getNota()),
            '%tip_addebito%' => trim($this->getTipAddebito()),
            '%cod_negozio%' => trim($this->getCodNegozio()),
            '%id_cliente%' => trim($this->getIdCliente()),
            '%id_cliente_orig%' => trim($this->getIdCliente()),
            '%num_fattura%' => trim($this->getNumFatturaOrig()),
            '%num_fattura_orig%' => trim($this->getNumFatturaOrig()),
            '%sta_scadenza%' => trim($this->getStaScadenza()),
            '%id_incasso%' => trim($this->getIdIncasso())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_SCADENZA;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        return $result;
    }

    public function inserisci($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
            '%dat_registrazione%' => trim($this->getDatRegistrazione()),
            '%imp_registrazione%' => trim($this->getImpRegistrazione()),
            '%nota%' => trim($this->getNota()),
            '%tip_addebito%' => trim($this->getTipAddebito()),
            '%cod_negozio%' => trim($this->getCodNegozio()),
            '%id_cliente%' => trim($this->getIdCliente()),
            '%num_fattura%' => trim($this->getNumFattura()),
            '%sta_scadenza%' => trim($this->getStaScadenza())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_SCADENZA;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function getSommaImportiScadenze() {
        
        $importoTotaleScadenze = 0;
        
        foreach ($this->getScadenzeDaIncassare() as $unaScadenza) {
            $importoTotaleScadenze += $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE];
        }        
        foreach ($this->getScadenzeIncassate() as $unaScadenza) {
            $importoTotaleScadenze += $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE];
        }
        return $importoTotaleScadenze;
    }

    public function getSommaImportiScadenzeDaIncassare() {
        
        $importoTotaleScadenze = 0;
        
        foreach ($this->getScadenzeDaIncassare() as $unaScadenza) {
            $importoTotaleScadenze += $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE];
        }        
        return $importoTotaleScadenze;
    }

    public function rimuoviScadenzaIncassata() {
        $scadenzeIncassateDiff = array();
        foreach ($this->getScadenzeIncassate() as $unaScadenza) {
            if (trim($unaScadenza[ScadenzaCliente::NUM_FATTURA]) != trim($this->getNumFattura())
                    or trim($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]) != trim($this->getDatRegistrazione())) {
                array_push($scadenzeIncassateDiff, $unaScadenza);
            } else {
                $this->setQtaScadenzeIncassate($this->getQtaScadenzeIncassate() - 1);
            }
        }
        $this->setScadenzeIncassate($scadenzeIncassateDiff);
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
    }

    public function ripartisciImporto() {
    
        /*
         * Alcune note:
         *
         * La ripartizione dell'importo di una registrazione viene eseguito all'uscita del oampo importo del primo dettaglio.
         * Una volta ripartito l'importo su tutte le scadenze presenti che hanno importo = 0, le sucessive modifiche agli 
         * importi dei dettagli non avranno effetti sugli importi delle scadenze.
         *   
         * - Se l'importo da suddividere = 0 non fa niente
         * - Conteggio scadenze con importo = 0, se non vi sono scadenze con importo = 0 non fa niente
         * 
         */

         if ($this->getImportoScadenza() != 0) {
             
            // conto le scadenze presenti con importo = 0
             
            $numScadenze = 0;
            foreach ($this->getScadenzeDaIncassare() as $unaScadenza) {
                if ($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE] == self::ZERO_VALUE) {
                    $numScadenze ++;
                }
            }
            
            // ripartisco l'importo scadenza calcolato e valorizzo gli importi delle scadenze a zero
            
            if ($numScadenze > 0) {
                $nuoveScadenze = array();
                $impDaRipartire = round($this->getImportoScadenza() / $numScadenze, 2);
                foreach ($this->getScadenzeDaIncassare() as $unaScadenza) {
                    
                    if ($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE] == self::ZERO_VALUE) {
                        $this->setImportoScadenza($impDaRipartire); 
                    } else {
                        $this->setImportoScadenza($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE]);
                    }
                    
                    $item = array(
                        ScadenzaCliente::ID_CLIENTE => $unaScadenza[ScadenzaCliente::ID_CLIENTE],
                        ScadenzaCliente::DAT_REGISTRAZIONE => $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE],
                        ScadenzaCliente::IMP_REGISTRAZIONE => $this->getImportoScadenza(),
                        ScadenzaCliente::NUM_FATTURA => $unaScadenza[ScadenzaCliente::NUM_FATTURA],
                        ScadenzaCliente::TIP_ADDEBITO => $unaScadenza[ScadenzaCliente::TIP_ADDEBITO],
                        ScadenzaCliente::STA_SCADENZA => $unaScadenza[ScadenzaCliente::STA_SCADENZA],
                        ScadenzaCliente::NOTA => $unaScadenza[ScadenzaCliente::NOTA]
                    );
                    array_push($nuoveScadenze, $item);
                }
                $this->setScadenzeDaIncassare($nuoveScadenze);
                $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
            }
        }
    }

    public function cancella($db) {
        /**
         * Cancello la scadenza dalla tabella DB
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $dataReg = date("d-m-Y", trim($this->getDatRegistrazione()));

        $replace = array(
            '%dat_registrazione%' => $dataReg,
            '%id_cliente%' => trim($this->getIdCliente()),
            '%num_fattura%' => trim($this->getNumFattura())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_SCADENZA;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            /**
             * Elimino la scadenza dalla griglia in pagina
             */
            $scadenzeDiff = array();
            foreach ($this->getScadenzeDaIncassare() as $unaScadenza) {
                if ((trim($unaScadenza[ScadenzaCliente::ID_CLIENTE]) != trim($this->getIdCliente()))
                        or ( trim($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]) != $dataReg)
                        or ( trim($unaScadenza[ScadenzaCliente::NUM_FATTURA]) != trim($this->getNumFattura()))) {
                    array_push($scadenzeDiff, $unaScadenza);
                } else
                    $this->setQtaScadenzeDaIncassare($this->getQtaScadenzeDaIncassare() - 1);
            }
            $this->setScadenzeDaIncassare($scadenzeDiff);
            $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
        }
        return $result;
    }

    public function aggiornaImporto($db) {
        /**
         * Aggiorno l'importo in scadenza sulla tabella DB
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $dataScad = date("d-m-Y", trim($this->getDatRegistrazione()));

        $replace = array(
            '%imp_registrazione%' => $this->getImpRegistrazione(),
            '%id_cliente%' => trim($this->getIdCliente()),
            '%dat_registrazione%' => $dataScad,
            '%num_fattura%' => $this->getNumFattura()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $scadenzeDiff = array();
            foreach ($this->getScadenzeDaIncassare() as $unaScadenza) {

                if ($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] != $dataScad)
                    array_push($scadenzeDiff, $unaScadenza);
                else {
                    $item = array(
                        ScadenzaCliente::ID_CLIENTE => $unaScadenza[ScadenzaCliente::ID_CLIENTE],
                        ScadenzaCliente::ID_SCADENZA => $unaScadenza[ScadenzaCliente::ID_SCADENZA],
                        ScadenzaCliente::DAT_REGISTRAZIONE => $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE],
                        ScadenzaCliente::IMP_REGISTRAZIONE => $this->getImpRegistrazione(),
                        ScadenzaCliente::NUM_FATTURA => $unaScadenza[ScadenzaCliente::NUM_FATTURA]
                    );
                    array_push($scadenzeDiff, $item);
                }
            }
            $this->setScadenzeDaIncassare($scadenzeDiff);
            $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
        }
        return $result;
    }

    public function aggiornaData($db) {
        /**
         * Aggiorno la data di scadenza in scadenziario clienti
         */
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $dataScad = date("d-m-Y", trim($this->getDatRegistrazione()));

        $replace = array(
            '%dat_scadenza_nuova%' => $this->getDatScadenzaNuova(),
            '%id_cliente%' => trim($this->getIdCliente()),
            '%dat_scadenza%' => $dataScad,
            '%num_fattura%' => $this->getNumFattura()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_DATA_SCADENZA_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        /*
         * Se tutto ok aggiorno la scadenza nell'array delle scadenze
         */

        if ($result) {
            $scadenzeDiff = array();
            foreach ($this->getScadenzeDaIncassare() as $unaScadenza) {

                if ($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] != $dataScad)
                    array_push($scadenzeDiff, $unaScadenza);
                else {
                    $item = array(
                        ScadenzaCliente::ID_SCADENZA => $unaScadenza[ScadenzaCliente::ID_SCADENZA],
                        ScadenzaCliente::DAT_REGISTRAZIONE => $this->getDatScadenzaNuova(),
                        ScadenzaCliente::IMP_REGISTRAZIONE => $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE],
                        ScadenzaCliente::NOTA => $unaScadenza[ScadenzaCliente::NOTA],
                        ScadenzaCliente::TIP_ADDEBITO => $unaScadenza[ScadenzaCliente::TIP_ADDEBITO],
                        ScadenzaCliente::COD_NEGOZIO => $unaScadenza[ScadenzaCliente::COD_NEGOZIO],
                        ScadenzaCliente::ID_CLIENTE => $unaScadenza[ScadenzaCliente::ID_CLIENTE],
                        ScadenzaCliente::NUM_FATTURA => $unaScadenza[ScadenzaCliente::NUM_FATTURA],
                        ScadenzaCliente::STA_SCADENZA => $unaScadenza[ScadenzaCliente::STA_SCADENZA],
                        ScadenzaCliente::ID_INCASSO => $unaScadenza[ScadenzaCliente::ID_INCASSO]
                    );
                    array_push($scadenzeDiff, $item);
                }
            }
            $this->setScadenzeDaIncassare($scadenzeDiff);
            $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
        }
        return $result;
    }

    public function trovaScadenzeDaIncassare($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_cliente%' => trim($this->getIdCliente()),
            '%cod_negozio%' => trim($this->getCodNegozioSel())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_SCADENZE_DA_INCASSARE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenzeDaIncassare(pg_fetch_all($result));
            $this->setQtaScadenzeDaIncassare(pg_num_rows($result));
        } else {
            $this->setScadenzeDaIncassare(null);
            $this->setQtaScadenzeDaIncassare(0);
        }
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
        return $result;
    }

    public function trovaScadenzeIncassate($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_SCADENZE_INCASSATE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setScadenzeIncassate(pg_fetch_all($result));
            $this->setQtaScadenzeIncassate(pg_num_rows($result));
        } else {
            $this->setScadenzeIncassate(null);
            $this->setQtaScadenzeIncassate(0);
        }
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($this);
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

    public function getDatRegistrazione() {
        return $this->datRegistrazione;
    }

    public function setDatRegistrazione($datRegistrazione) {
        $this->datRegistrazione = $datRegistrazione;
    }

    public function getImpRegistrazione() {
        return $this->impRegistrazione;
    }

    public function setImpRegistrazione($impRegistrazione) {
        $this->impRegistrazione = $impRegistrazione;
    }

    public function getNota() {
        return $this->nota;
    }

    public function setNota($nota) {
        $this->nota = $nota;
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

    public function getIdCliente() {
        return $this->idCliente;
    }

    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
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

    public function getIdIncasso() {
        return $this->idIncasso;
    }

    public function setIdIncasso($idIncasso) {
        $this->idIncasso = $idIncasso;
    }

    public function getScadenze() {
        return $this->scadenze;
    }

    public function setScadenze($scadenze) {
        $this->scadenze = $scadenze;
    }

    public function getQtaScadenze() {
        return $this->qtaScadenze;
    }

    public function setQtaScadenze($qtaScadenze) {
        $this->qtaScadenze = $qtaScadenze;
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

    public function getImportoScadenza() {
        return $this->importoScadenza;
    }

    public function setImportoScadenza($importoScadenza) {
        $this->importoScadenza = $importoScadenza;
    }

    public function getScadenzeDaIncassare() {
        return $this->scadenzeDaIncassare;
    }

    public function setScadenzeDaIncassare($scadenzeDaIncassare) {
        $this->scadenzeDaIncassare = $scadenzeDaIncassare;
    }

    public function getQtaScadenzeDaIncassare() {
        return $this->qtaScadenzeDaIncassare;
    }

    public function setQtaScadenzeDaIncassare($qtaScadenzeDaIncassare) {
        $this->qtaScadenzeDaIncassare = $qtaScadenzeDaIncassare;
    }

    public function getIdClienteOrig() {
        return $this->idClienteOrig;
    }

    public function setIdClienteOrig($idClienteOrig) {
        $this->idClienteOrig = $idClienteOrig;
    }

    public function getNumFatturaOrig() {
        return $this->numFatturaOrig;
    }

    public function setNumFatturaOrig($numFatturaOrig) {
        $this->numFatturaOrig = $numFatturaOrig;
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

    public function getScadenzeIncassate() {
        return $this->scadenzeIncassate;
    }

    public function setScadenzeIncassate($scadenzeIncassate) {
        $this->scadenzeIncassate = $scadenzeIncassate;
        return $this;
    }

    public function getQtaScadenzeIncassate() {
        return $this->qtaScadenzeIncassate;
    }

    public function setQtaScadenzeIncassate($qtaScadenzeIncassate) {
        $this->qtaScadenzeIncassate = $qtaScadenzeIncassate;
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