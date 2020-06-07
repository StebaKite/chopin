<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class Cliente extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Cliente

    const ID_CLIENTE = "id_cliente";
    const COD_CLIENTE = "cod_cliente";
    const DES_CLIENTE = "des_cliente";
    const DES_INDIRIZZO_CLIENTE = "des_indirizzo_cliente";
    const DES_CITTA_CLIENTE = "des_citta_cliente";
    const CAP_CLIENTE = "cap_cliente";
    const TIP_ADDEBITO = "tip_addebito";
    const DAT_CREAZIONE = "dat_creazione";
    const COD_PIVA = "cod_piva";
    const COD_FISC = "cod_fisc";
    const CAT_CLIENTE = "cat_cliente";
    const QTA_REGISTRAZIONI_CLIENTE = "tot_registrazioni_cliente";

    // dati Cliente

    private $id_cliente;
    private $cod_cliente;
    private $des_cliente;
    private $des_indirizzo_cliente;
    private $des_citta_cliente;
    private $cap_cliente;
    private $tip_addebito;
    private $dat_creazione;
    private $cod_piva;
    private $cod_fisc;
    private $cat_cliente;
    // Altri dati funzionali

    private $esitoPivaCliente;
    private $esitoCfisCliente;
    private $pivaEsistente;
    private $cfiscEsistente;
    private $clienti;
    private $qtaClienti;
    private $qtaRegistrazioniCliente;
    private $scadenzeDaIncassare;
    private $qtaScadenzeDaIncassare;

    // Queries

    const LEGGI_ULTIMO_CODICE_CLIENTE = "/anagrafica/leggiUltimoCodiceCliente.sql";
    const INSERISCI_CLIENTE = "/anagrafica/creaCliente.sql";
    const CERCA_CODICE_FISCALE = "/anagrafica/ricercaCfisCliente.sql";
    const CERCA_PARTITA_IVA = "/anagrafica/ricercaPivaCliente.sql";
    const CERCA_DESCRIZIONE = "/anagrafica/trovaDescCliente.sql";
    const QUERY_RICERCA_CLIENTE = "/anagrafica/ricercaCliente.sql";
    const LEGGI_CLIENTE_X_ID = "/anagrafica/leggiIdCliente.sql";
    const CANCELLA_CLIENTE = "/anagrafica/deleteCliente.sql";
    const AGGIORNA_CLIENTE = "/anagrafica/updateCliente.sql";
    const RICERCA_DATI_CLIENTE = "/fatture/ricercaDatiCliente.sql";

    // Metodi

    function __construct() {
        $this->setRoot(parent::getInfoFromServer('DOCUMENT_ROOT'));
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CLIENTE) === NULL) {
            parent::setIndexSession(self::CLIENTE, serialize(new Cliente()));
        }
        return unserialize(parent::getIndexSession(self::CLIENTE));
    }

    public function Prepara() {
        $db = Database::getInstance();
        $utility = Utility::getInstance();

        $this->setCodCliente($this->prelevaUltimoCodiceCliente($utility, $db) + 1);
        $this->setIdCliente(null);
        $this->setDesCliente(null);
        $this->setDesIndirizzoCliente(null);
        $this->setDesCittaCliente(null);
        $this->setCapCliente(null);
        $this->setCodPiva(null);
        $this->setCodFisc(null);
        $this->setCatCliente(null);

        parent::setIndexSession(self::CLIENTE, serialize($this));
    }

    public function prelevaUltimoCodiceCliente($utility, $db) {

        $array = $utility->getConfig();
        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_ULTIMO_CODICE_CLIENTE;
        $sql = $utility->getQueryTemplate($sqlTemplate);
        $rows = pg_fetch_all($db->getData($sql));

        foreach ($rows as $row) {
            $result = $row['cod_cliente_ult'];
        }
        return $result;
    }

    public function inserisci($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_cliente%' => trim($this->getCodCliente()),
            '%des_cliente%' => parent::isEmpty($this->getDesCliente()) ? parent::NULL_VALUE : str_replace("'", "''", $this->getDesCliente()),
            '%des_indirizzo_cliente%' => parent::isEmpty($this->getDesIndirizzoCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getDesIndirizzoCliente()) . "'",
            '%des_citta_cliente%' => parent::isEmpty($this->getDesCittaCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getDesCittaCliente()) . "'",
            '%cap_cliente%' => parent::isEmpty($this->getCapCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCapCliente()) . "'",
            '%tip_addebito%' => parent::isEmpty($this->getTipAddebito()) ? parent::NULL_VALUE : str_replace("'", "''", $this->getTipAddebito()),
            '%cod_piva%' => parent::isEmpty($this->getCodPiva()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCodPiva()) . "'",
            '%cod_fisc%' => parent::isEmpty($this->getCodFisc()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCodFisc()) . "'",
            '%cat_cliente%' => parent::isEmpty($this->getCatCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCatCliente()) . "'"
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::INSERISCI_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $this->load($db); // refresh dei clienti caricati
            parent::setIndexSession(self::CLIENTE, serialize($this));
        }

        /**
         * Creo anche il conto per il cliente
         */
        if ($result) {
            $sottoconto = Sottoconto::getInstance();
            $sottoconto->setCodConto($array["contiCliente"]);
            $sottoconto->setCodSottoconto($this->getCodCliente());
            $sottoconto->setDesSottoconto($this->getDesCliente());

            parent::setIndexSession(self::SOTTOCONTO, serialize($sottoconto));
            $result = $sottoconto->inserisci($db);

            $conto = Conto::getInstance();
            $conto->load($db);  // refresh dei conti caricati
            parent::setIndexSession(self::CONTO, serialize($conto));
        }
        return $result;
    }

    /**
     * Questo metodo cerca un cliente tramite il suo codice fiscale
     * @param $db
     */
    public function cercaCodiceFiscale($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_fisc%' => $this->getCodFisc(),
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_CODICE_FISCALE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);

        if (pg_num_rows($db->getData($sql)) > 0) {
            $this->setCfiscEsistente("true");
        }
    }

    public function cercaPartivaIva($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = ['%cod_piva%' => trim($this->getCodPiva())];
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_PARTITA_IVA;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);

        if (pg_num_rows($db->getData($sql)) > 0) {
            $this->setPivaEsistente("true");
        }
    }

    public function cercaConDescrizione($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%des_cliente%' => trim($this->getDesCliente())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_DESCRIZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if (pg_num_rows($result) > 0) {
            foreach (pg_fetch_all($result) as $row) {
                $this->setIdCliente($row[Cliente::ID_CLIENTE]);
                $this->setCodCliente($row[Cliente::COD_CLIENTE]);
                $this->setTipAddebito($row[Cliente::TIP_ADDEBITO]);
            }
        }
        
        parent::setIndexSession(self::CLIENTE, serialize($this));
    }

    public function load($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::QUERY_RICERCA_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setClienti(pg_fetch_all($result));
            $this->setQtaClienti(pg_num_rows($result));
        } else {
            $this->setClienti(null);
            $this->setQtaClienti(null);
        }
        return $result;
    }

    public function leggi($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_cliente%' => $this->getIdCliente()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_CLIENTE_X_ID;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if (pg_num_rows($result) > 0) {
            foreach (pg_fetch_all($result) as $row) {
                $this->setCodCliente($row[self::COD_CLIENTE]);
                $this->setDesCliente($row[self::DES_CLIENTE]);
                $this->setDesIndirizzoCliente($row[self::DES_INDIRIZZO_CLIENTE]);
                $this->setDesCittaCliente($row[self::DES_CITTA_CLIENTE]);
                $this->setCapCliente($row[self::CAP_CLIENTE]);
                $this->setTipAddebito($row[self::TIP_ADDEBITO]);
                $this->setDatCreazione($row[self::DAT_CREAZIONE]);
                $this->setCodPiva($row[self::COD_PIVA]);
                $this->setCodFisc($row[self::COD_FISC]);
                $this->setCatCliente($row[self::CAT_CLIENTE]);
            }
        }
        parent::setIndexSession(self::CLIENTE, serialize($this));
        return $result;
    }

    public function cancella($db) {

        if ($this->leggi($db)) {
            /**
             * Cancello il conto del cliente
             * @var array $conto
             */
            $utility = Utility::getInstance();
            $array = $utility->getConfig();

            $sottoconto = Sottoconto::getInstance();
            $conto = explode(",", $array["contiCliente"]);

            foreach ($conto as $contoClienti) {
                $sottoconto->setCodConto($contoClienti);
                $sottoconto->setCodSottoconto($this->getCodCliente());
                $sottoconto->cancella($db);
            }

            $replace = array(
                '%id_cliente%' => $this->getIdCliente()
            );

            $sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_CLIENTE;
            $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
            $result = $db->getData($sql);

            if ($result) {
                $this->load($db); // refresh dei clienti caricati
                parent::setIndexSession(self::CLIENTE, serialize($this));
            }
        }
        return $result;
    }

    public function update($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_cliente%' => $this->getIdCliente(),
            '%cod_cliente%' => $this->getCodCliente(),
            '%des_cliente%' => parent::isEmpty($this->getDesCliente()) ? parent::NULL_VALUE : str_replace("'", "''", $this->getDesCliente()),
            '%des_indirizzo_cliente%' => parent::isEmpty($this->getDesIndirizzoCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getDesIndirizzoCliente()) . "'",
            '%des_citta_cliente%' => parent::isEmpty($this->getDesCittaCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getDesCittaCliente()) . "'",
            '%cap_cliente%' => parent::isEmpty($this->getCapCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCapCliente()) . "'",
            '%tip_addebito%' => parent::isEmpty($this->getTipAddebito()) ? parent::NULL_VALUE : str_replace("'", "''", $this->getTipAddebito()),
            '%cod_piva%' => parent::isEmpty($this->getCodPiva()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCodPiva()) . "'",
            '%cod_fisc%' => parent::isEmpty($this->getCodFisc()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCodFisc()) . "'",
            '%cat_cliente%' => parent::isEmpty($this->getCatCliente()) ? parent::NULL_VALUE : "'" . str_replace("'", "''", $this->getCatCliente()) . "'"
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $this->load($db); // refresh dei clienti caricati
            parent::setIndexSession(self::CLIENTE, serialize($this));
        }

        return $result;
    }

    public function prelevaScadenzeAperteCliente($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_cliente%' => trim($this->getIdCliente())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::$queryLeggiScadenzeAperteCliente;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setClienti(pg_fetch_all($result));
            $this->setQtaClienti(pg_num_rows($result));
        } else {
            $this->setClienti(null);
            $this->setQtaClienti(null);
        }
        return $result;
    }

    /**
     * Questo metodo preleva il tipo addebito di un cliente
     *
     * @param unknown $db
     */
    public function caricaTipoAddebitoCliente($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_cliente%' => $this->getIdCliente()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_DATI_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            foreach (pg_fetch_all($result) as $row) {
                $this->setTipAddebito($row['tip_addebito']);
                $this->setDesCliente($row['des_cliente']);
                $this->setDesIndirizzoCliente($row['des_indirizzo_cliente']);
                $this->setDesCittaCliente($row['des_citta_cliente']);
                $this->setCapCliente($row['cap_cliente']);
                $this->setCodPiva($row['cod_piva']);
                $this->setCodFisc($row['cod_fisc']);
            }
            parent::setIndexSession(self::CLIENTE, serialize($this));
        }
    }

    /*     * *********************************************************************
     * Getters e setters
     */

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getIdCliente() {
        return $this->id_cliente;
    }

    public function setIdCliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    public function getCodCliente() {
        return $this->cod_cliente;
    }

    public function setCodCliente($cod_cliente) {
        $this->cod_cliente = $cod_cliente;
    }

    public function getDesCliente() {
        return $this->des_cliente;
    }

    public function setDesCliente($des_cliente) {
        $this->des_cliente = $des_cliente;
    }

    public function getDesIndirizzoCliente() {
        return $this->des_indirizzo_cliente;
    }

    public function setDesIndirizzoCliente($des_indirizzo_cliente) {
        $this->des_indirizzo_cliente = $des_indirizzo_cliente;
    }

    public function getDesCittaCliente() {
        return $this->des_citta_cliente;
    }

    public function setDesCittaCliente($des_citta_cliente) {
        $this->des_citta_cliente = $des_citta_cliente;
    }

    public function getCapCliente() {
        return $this->cap_cliente;
    }

    public function setCapCliente($cap_cliente) {
        $this->cap_cliente = $cap_cliente;
    }

    public function getTipAddebito() {
        return $this->tip_addebito;
    }

    public function setTipAddebito($tip_addebito) {
        $this->tip_addebito = $tip_addebito;
    }

    public function getDatCreazione() {
        return $this->dat_creazione;
    }

    public function setDatCreazione($dat_creazione) {
        $this->dat_creazione = $dat_creazione;
    }

    public function getCodPiva() {
        return $this->cod_piva;
    }

    public function setCodPiva($cod_piva) {
        $this->cod_piva = $cod_piva;
    }

    public function getCodFisc() {
        return $this->cod_fisc;
    }

    public function setCodFisc($cod_fisc) {
        $this->cod_fisc = $cod_fisc;
    }

    public function getCatCliente() {
        return $this->cat_cliente;
    }

    public function setCatCliente($cat_cliente) {
        $this->cat_cliente = $cat_cliente;
    }

    public function getEsitoPivaCliente() {
        return $this->esitoPivaCliente;
    }

    public function setEsitoPivaCliente($esitoPivaCliente) {
        $this->esitoPivaCliente = $esitoPivaCliente;
    }

    public function getEsitoCfisCliente() {
        return $this->esitoCfisCliente;
    }

    public function setEsitoCfisCliente($esitoCfisCliente) {
        $this->esitoCfisCliente = $esitoCfisCliente;
    }

    public function getPivaEsistente() {
        return $this->pivaEsistente;
    }

    public function setPivaEsistente($pivaEsistente) {
        $this->pivaEsistente = $pivaEsistente;
    }

    public function getCfiscEsistente() {
        return $this->cfiscEsistente;
    }

    public function setCfiscEsistente($cfiscEsistente) {
        $this->cfiscEsistente = $cfiscEsistente;
    }

    public function getClienti() {
        return $this->clienti;
    }

    public function setClienti($clienti) {
        $this->clienti = $clienti;
    }

    public function getQtaClienti() {
        return $this->qtaClienti;
    }

    public function setQtaClienti($qtaClienti) {
        $this->qtaClienti = $qtaClienti;
    }

    public function getQtaRegistrazioniCliente() {
        return $this->qtaRegistrazioniCliente;
    }

    public function setQtaRegistrazioniCliente($qtaRegistrazioniCliente) {
        $this->qtaRegistrazioniCliente = $qtaRegistrazioniCliente;
    }

    public function setQtaScadenzeDaIncassare($qtaScadenzeDaIncassare) {
        $this->qtaScadenzeDaIncassare = $qtaScadenzeDaIncassare;
    }

    public function getQtaScadenzeDaIncassare() {
        return $this->qtaScadenzeDaIncassare;
    }

    public function getScadenzeDaIncassare() {
        return $this->scadenzeDaIncassare;
    }

    public function setScadenzeDaIncassare($scadenzeDaIncassare) {
        $this->scadenzeDaIncassare = $scadenzeDaIncassare;
    }

}