<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Registrazione extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Registrazione

    const ID_REGISTRAZIONE = "id_registrazione";
    const DAT_SCADENZA = "dat_scadenza";
    const DES_REGISTRAZIONE = "des_registrazione";
    const ID_FORNITORE = "id_fornitore";
    const ID_CLIENTE = "id_cliente";
    const COD_CAUSALE = "cod_causale";
    const NUM_FATTURA = "num_fattura";
    const DAT_REGISTRAZIONE = "dat_registrazione";
    const DAT_INSERIMENTO = "dat_inserimento";
    const STA_REGISTRAZIONE = "sta_registrazione";
    const COD_NEGOZIO = "cod_negozio";
    const ID_MERCATO = "id_mercato";
    // altri nomi generati

    const DAT_REGISTRAZIONE_YYYYMMDD = "dat_registrazione_yyyymmdd";
    const TIPO_RIGA_REGISTRAZIONE = "tipo";
    const RIGA_REGISTRAZIONE = "R";
    const RIGA_DETTAGLIO_REGISTRAZIONE = "D";
    const ID_PAGAMENTO_CORRELATO = "id_pagamento";
    const ID_INCASSO_CORRELATO = "id_incasso";

    // dati registrazione

    private $idRegistrazione;
    private $datScadenza;
    private $desRegistrazione;
    private $idFornitore;
    private $idCliente;
    private $codCausale;
    private $numFattura;
    private $numFatturaOrig;
    private $numFatturePagate;
    private $numFattureDaPagare;
    private $numFattureIncassate;
    private $numFattureDaIncassare;
    private $datRegistrazione;
    private $datInserimento;
    private $staRegistrazione;
    private $codNegozio;
    private $idMercato;
    private $registrazioni;
    private $qtaRegistrazioni;
    private $desCliente;
    private $desFornitore;
    // Dati filtri di ricerca

    private $datRegistrazioneDa;
    private $datRegistrazioneA;
    private $codNegozioSel;
    private $codCausaleSel;
    private $codContoSel;

    // Queries

    const LEGGI_REGISTRAZIONE = "/primanota/leggiRegistrazione.sql";
    const LEGGI_REGISTRAZIONI_CONTO = "/strumenti/ricercaRegistrazioniConto.sql";
    const CANCELLA_REGISTRAZIONE = "/primanota/deleteRegistrazione.sql";
    const RICERCA_REGISTRAZIONE = "/primanota/ricercaRegistrazione.sql";
    const CREA_REGISTRAZIONE = "/primanota/creaRegistrazione.sql";
    const AGGIORNA_REGISTRAZIONE = "/primanota/updateRegistrazione.sql";
    const CERCA_FATTURA_FORNITORE = "/primanota/ricercaFatturaFornitore.sql";
    const CERCA_FATTURA_CLIENTE = "/primanota/ricercaFatturaCliente.sql";
    const CERCA_REGISTRAZIONE_DOPPIA = "/primanota/cercaRegistrazioneDoppia.sql";

    // Metodi

    function __construct() {
        $this->setRoot(parent::getInfoFromServer('DOCUMENT_ROOT'));
    }

    public static function getInstance() {

        if (parent::getIndexSession(self::REGISTRAZIONE) === NULL) {
            parent::setIndexSession(self::REGISTRAZIONE, serialize(new Registrazione()));
        }
        return unserialize(parent::getIndexSession(self::REGISTRAZIONE));
    }

    public function prepara() {
        $this->setDatScadenza(null);
        $this->setDesRegistrazione(null);
        $this->setIdFornitore(null);
        $this->setIdCliente(null);
        $this->setCodCausale(null);
        $this->setNumFattura(null);
        $this->setNumFattura(null);
        $this->setNumFattureDaPagare(null);
        $this->setDatRegistrazione(null);
        $this->setDatInserimento(null);
        $this->setStaRegistrazione(null);
        $this->setCodNegozio(null);
        $this->setIdMercato(null);
        $this->setRegistrazioni(null);
        $this->setQtaRegistrazioni(null);
        $this->setDesCliente(null);
        $this->setDesFornitore(null);

        parent::setIndexSession(self::REGISTRAZIONE, serialize($this));
    }

    public function leggi($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione())
        );

        $sqlTemplate = $this->root . $array['query'] . self::LEGGI_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                foreach (pg_fetch_all($result) as $row) {
                    $this->setIdRegistrazione($row[self::ID_REGISTRAZIONE]);
                    $this->setDatScadenza(trim($row[self::DAT_SCADENZA]));
                    $this->setDesRegistrazione(trim($row[self::DES_REGISTRAZIONE]));
                    $this->setIdFornitore($row[self::ID_FORNITORE]);
                    $this->setIdCliente($row[self::ID_CLIENTE]);
                    $this->setCodCausale($row[self::COD_CAUSALE]);
                    $this->setNumFattura($row[self::NUM_FATTURA]);
                    $this->setNumFatturaOrig($row[self::NUM_FATTURA]);
                    $this->setDatRegistrazione(trim($row[self::DAT_REGISTRAZIONE]));
                    $this->setDatInserimento($row[self::DAT_INSERIMENTO]);
                    $this->setStaRegistrazione($row[self::STA_REGISTRAZIONE]);
                    $this->setCodNegozio($row[self::COD_NEGOZIO]);
                    $this->setIdMercato($row[self::ID_MERCATO]);
                }
                parent::setIndexSession(self::REGISTRAZIONE, serialize($this));
            }
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        return $result;
    }

    /**
     * Questo metodo carica tutte le registrazioni che hanno almeno un dettaglio su un sottoconto specifico
     * @param unknown $db
     */
    public function leggiRegistrazioniConto($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $filtriRegistrazione = "";
        $filtriDettaglio = "";

        if (parent::isNotEmpty($this->getCodNegozioSel())) {
            $filtriRegistrazione .= "and reg.cod_negozio = '" . $this->getCodNegozioSel() . "'";
        }

        if (parent::isNotEmpty($this->getCodContoSel())) {
            $conto = explode(".", $this->getCodContoSel());

            $filtriDettaglio .= "and detreg.cod_conto = '" . $conto[0] . "'";
            $filtriDettaglio .= "and detreg.cod_sottoconto = '" . $conto[1] . "'";
        }

        $replace = array(
            '%datareg_da%' => $this->getDatRegistrazioneDa(),
            '%datareg_a%' => $this->getDatRegistrazioneA(),
            '%filtri-registrazione%' => $filtriRegistrazione,
            '%filtri-dettaglio%' => $filtriDettaglio,
        );

        $sqlTemplate = $this->root . $array['query'] . self::LEGGI_REGISTRAZIONI_CONTO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        
        if ($result) {
            $this->setRegistrazioni(pg_fetch_all($result));
            $this->setQtaRegistrazioni(pg_num_rows($result));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        parent::setIndexSession(self::REGISTRAZIONE, serialize($this));
        return $result;
    }    
    
    public function cancella($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione())
        );

        $sqlTemplate = $this->root . $array['query'] . self::CANCELLA_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        
        if ($result) {
            return $result;  
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    public function preparaFiltri() {
        if (parent::isEmpty($this->getDatRegistrazioneDa())) {
            $this->setDatRegistrazioneDa(date("d-m-Y"));
        }
        if (parent::isEmpty($this->getDatRegistrazioneA())) {
            $this->setDatRegistrazioneA(date("d-m-Y"));
        }
        if (parent::isEmpty($this->getCodNegozioSel())) {
            $this->setCodNegozioSel("");
        }

        parent::setIndexSession(self::REGISTRAZIONE, serialize($this));
    }

    public function load($db) {
        $utility = Utility::getInstance();

        $filtriRegistrazione = "";
        $filtriDettaglio = "";

        if ($this->getCodCausaleSel() != "") {
            $filtriRegistrazione .= "and reg.cod_causale = '" . trim($this->getCodCausaleSel()) . "'";
        }
        if ($this->getCodNegozioSel() != "") {
            $filtriRegistrazione .= "and reg.cod_negozio = '" . trim($this->getCodNegozioSel()) . "'";
        }

        $replace = array(
            '%datareg_da%' => $this->getDatRegistrazioneDa(),
            '%datareg_a%' => $this->getDatRegistrazioneA(),
            '%filtri-registrazione%' => $filtriRegistrazione,
            '%filtri-dettaglio%' => $filtriDettaglio
        );

        $array = $utility->getConfig();
        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_REGISTRAZIONE;

        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);

        // esegue la query

        $result = $db->getData($sql);

        if ($result) {
            $this->setRegistrazioni(pg_fetch_all($result));
            $this->setQtaRegistrazioni(pg_num_rows($result));

            $numReg = 0;
            if ($this->getQtaRegistrazioni() > 0) {
                foreach ($this->getRegistrazioni() as $unaRegistrazione) {
                    if ($unaRegistrazione[self::TIPO_RIGA_REGISTRAZIONE] == "R") {
                        $numReg ++;
                    }
                }                
            }
            $this->setQtaRegistrazioni($numReg);
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        return $result;
    }

    public function inserisci($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%des_registrazione%' => parent::isNotEmpty($this->getDesRegistrazione()) ? parent::quotation($this->getDesRegistrazione()) : parent::NULL_VALUE,
            '%dat_scadenza%' => parent::isNotEmpty($this->getDatScadenza()) ? parent::quotation($this->getDatScadenza()) : parent::NULL_VALUE,
            '%dat_registrazione%' => parent::isNotEmpty($this->getDatRegistrazione()) ? parent::quotation($this->getDatRegistrazione()) : parent::NULL_VALUE,
            '%dat_inserimento%' => parent::quotation(date("Y-m-d H:i:s")),
            '%num_fattura%' => parent::isNotEmpty($this->getNumFattura()) ? parent::quotation($this->getNumFattura()) : parent::NULL_VALUE,
            '%cod_causale%' => parent::isNotEmpty($this->getCodCausale()) ? parent::quotation($this->getCodCausale()) : parent::NULL_VALUE,
            '%id_fornitore%' => parent::isNotEmpty($this->getIdFornitore()) ? parent::quotation($this->getIdFornitore()) : parent::NULL_VALUE,
            '%id_cliente%' => parent::isNotEmpty($this->getIdCliente()) ? parent::quotation($this->getIdCliente()) : parent::NULL_VALUE,
            '%sta_registrazione%' => parent::isNotEmpty($this->getStaRegistrazione()) ? parent::quotation($this->getStaRegistrazione()) : parent::NULL_VALUE,
            '%cod_negozio%' => parent::isNotEmpty($this->getCodNegozio()) ? parent::quotation($this->getCodNegozio()) : parent::NULL_VALUE,
            '%id_mercato%' => parent::isNotEmpty($this->getIdMercato()) ? parent::quotation($this->getIdMercato()) : parent::NULL_VALUE
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $this->setIdRegistrazione($db->getLastIdUsed());  // l'id generato dall'inserimento
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        parent::setIndexSession(self::REGISTRAZIONE, serialize($this));
        return $result;
    }

    public function aggiorna($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_registrazione%' => trim($this->getIdRegistrazione()),
            '%des_registrazione%' => parent::isNotEmpty($this->getDesRegistrazione()) ? parent::quotation($this->getDesRegistrazione()) : parent::NULL_VALUE,
            '%dat_scadenza%' => parent::isNotEmpty($this->getDatScadenza()) ? parent::quotation($this->getDatScadenza()) : parent::NULL_VALUE,
            '%dat_registrazione%' => parent::isNotEmpty($this->getDatRegistrazione()) ? parent::quotation($this->getDatRegistrazione()) : parent::NULL_VALUE,
            '%dat_inserimento%' => parent::quotation(date("Y-m-d H:i:s")),
            '%num_fattura%' => parent::isNotEmpty($this->getNumFattura()) ? parent::quotation($this->getNumFattura()) : parent::NULL_VALUE,
            '%cod_causale%' => parent::isNotEmpty($this->getCodCausale()) ? parent::quotation($this->getCodCausale()) : parent::NULL_VALUE,
            '%id_fornitore%' => parent::isNotEmpty($this->getIdFornitore()) ? parent::quotation($this->getIdFornitore()) : parent::NULL_VALUE,
            '%id_cliente%' => parent::isNotEmpty($this->getIdCliente()) ? parent::quotation($this->getIdCliente()) : parent::NULL_VALUE,
            '%sta_registrazione%' => parent::isNotEmpty($this->getStaRegistrazione()) ? parent::quotation($this->getStaRegistrazione()) : parent::NULL_VALUE,
            '%cod_negozio%' => parent::isNotEmpty($this->getCodNegozio()) ? parent::quotation($this->getCodNegozio()) : parent::NULL_VALUE,
            '%id_mercato%' => parent::isNotEmpty($this->getIdMercato()) ? parent::quotation($this->getIdMercato()) : parent::NULL_VALUE
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_REGISTRAZIONE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            parent::setIndexSession(self::REGISTRAZIONE, serialize($this));
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        return $result;
    }

    public function cercaFatturaFornitore($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_fornitore%' => trim($this->getIdFornitore()),
            '%num_fattura%' => trim($this->getNumFattura()),
            '%dat_registrazione%' => trim($this->getDatRegistrazione())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_FATTURA_FORNITORE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        return false;
    }

    public function cercaFatturaCliente($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_cliente%' => trim($this->getIdCliente()),
            '%num_fattura%' => trim($this->getNumFattura()),
            '%dat_registrazione%' => trim($this->getDatRegistrazione())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_FATTURA_CLIENTE;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        return false;
    }

    public function cercaRegistrazioneDoppia($db) {
        
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%dat_registrazione%' => trim($this->getDatRegistrazione()),
            '%cau_registrazione%' => trim($this->getCodCausale()),
            '%des_registrazione%' => trim($this->getDesRegistrazione())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_REGISTRAZIONE_DOPPIA;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
        return false;
    }
    
    // ------ Getters & Setters ----------------------------------------------
    
    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
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

    public function getDesRegistrazione() {
        return $this->desRegistrazione;
    }

    public function setDesRegistrazione($desRegistrazione) {
        $this->desRegistrazione = $desRegistrazione;
    }

    public function getIdFornitore() {
        return $this->idFornitore;
    }

    public function setIdFornitore($idFornitore) {
        $this->idFornitore = $idFornitore;
    }

    public function getIdCliente() {
        return $this->idCliente;
    }

    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
    }

    public function getCodCausale() {
        return $this->codCausale;
    }

    public function setCodCausale($codCausale) {
        $this->codCausale = $codCausale;
    }

    public function getNumFattura() {
        return $this->numFattura;
    }

    public function setNumFattura($numFattura) {
        $this->numFattura = $numFattura;
    }

    public function getDatRegistrazione() {
        return $this->datRegistrazione;
    }

    public function setDatRegistrazione($datRegistrazione) {
        $this->datRegistrazione = $datRegistrazione;
    }

    public function getDatInserimento() {
        return $this->datInserimento;
    }

    public function setDatInserimento($datInserimento) {
        $this->datInserimento = $datInserimento;
    }

    public function getStaRegistrazione() {
        return $this->staRegistrazione;
    }

    public function setStaRegistrazione($staRegistrazione) {
        $this->staRegistrazione = $staRegistrazione;
    }

    public function getCodNegozio() {
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio) {
        $this->codNegozio = $codNegozio;
    }

    public function getIdMercato() {
        return $this->idMercato;
    }

    public function setIdMercato($idMercato) {
        $this->idMercato = $idMercato;
    }

    public function getDatRegistrazioneDa() {
        return $this->datRegistrazioneDa;
    }

    public function setDatRegistrazioneDa($datRegistrazioneDa) {
        $this->datRegistrazioneDa = $datRegistrazioneDa;
    }

    public function getDatRegistrazioneA() {
        return $this->datRegistrazioneA;
    }

    public function setDatRegistrazioneA($datRegistrazioneA) {
        $this->datRegistrazioneA = $datRegistrazioneA;
    }

    public function getCodNegozioSel() {
        return $this->codNegozioSel;
    }

    public function setCodNegozioSel($codNegozioSel) {
        $this->codNegozioSel = $codNegozioSel;
    }

    public function getCodCausaleSel() {
        return $this->codCausaleSel;
    }

    public function setCodCausaleSel($codCausaleSel) {
        $this->codCausaleSel = $codCausaleSel;
    }

    public function getCodContoSel() {
        return $this->codContoSel;
    }

    public function setCodContoSel($codContoSel) {
        $this->codContoSel = $codContoSel;
    }

    public function getRegistrazioni() {
        return $this->registrazioni;
    }

    public function setRegistrazioni($registrazioni) {
        $this->registrazioni = $registrazioni;
    }

    public function getQtaRegistrazioni() {
        return $this->qtaRegistrazioni;
    }

    public function setQtaRegistrazioni($qtaRegistrazioni) {
        $this->qtaRegistrazioni = $qtaRegistrazioni;
    }

    public function getDesCliente() {
        return $this->desCliente;
    }

    public function setDesCliente($desCliente) {
        $this->desCliente = $desCliente;
    }

    public function getDesFornitore() {
        return $this->desFornitore;
    }

    public function setDesFornitore($desFornitore) {
        $this->desFornitore = $desFornitore;
    }

    public function getNumFatturePagate() {
        return $this->numFatturePagate;
    }

    public function setNumFatturePagate($numFatturePagate) {
        $this->numFatturePagate = $numFatturePagate;
        return $this;
    }

    public function getNumFattureDaPagare() {
        return $this->numFattureDaPagare;
    }

    public function setNumFattureDaPagare($numFattureDaPagare) {
        $this->numFattureDaPagare = $numFattureDaPagare;
        return $this;
    }

    public function getNumFatturaOrig() {
        return $this->numFatturaOrig;
    }

    public function setNumFatturaOrig($numFatturaOrig) {
        $this->numFatturaOrig = $numFatturaOrig;
        return $this;
    }

    public function getNumFattureIncassate() {
        return $this->numFattureIncassate;
    }

    public function setNumFattureIncassate($numFattureIncassate) {
        $this->numFattureIncassate = $numFattureIncassate;
        return $this;
    }

    public function getNumFattureDaIncassare() {
        return $this->numFattureDaIncassare;
    }

    public function setNumFattureDaIncassare($numFattureDaIncassare) {
        $this->numFattureDaIncassare = $numFattureDaIncassare;
        return $this;
    }

}