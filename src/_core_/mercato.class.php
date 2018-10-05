<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Mercato extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella Mercato

    const ID_MERCATO = "id_mercato";
    const COD_MERCATO = "cod_mercato";
    const DES_MERCATO = "des_mercato";
    const CITTA_MERCATO = "citta_mercato";
    const COD_NEGOZIO = "cod_negozio";
    const QTA_REGISTRAZIONI_MERCATO = "tot_registrazioni_mercato";

    // dati Mercato

    private $idMercato;
    private $codMercato;
    private $desMercato;
    private $cittaMercato;
    private $codNegozio;
    private $mercati;
    private $qtaMercati;

    // Altri dati funzionali
    // Queries

    const RICERCA_MERCATI = "/anagrafica/ricercaMercato.sql";
    const CREA_MERCATO = "/anagrafica/creaMercato.sql";
    const AGGIORNA_MERCATO = "/anagrafica/updateMercato.sql";
    const CANCELLA_MERCATO = "/anagrafica/deleteMercato.sql";
    const LEGGI_MERCATO = "/anagrafica/leggiIdMercato.sql";
    const RICERCA_MERCATI_NEGOZIO = "/anagrafica/ricercaMercatiNegozio.sql";

    // Metodi

    function __construct() {
        $this->setRoot($_SERVER['DOCUMENT_ROOT']);
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::MERCATO]))
            $_SESSION[self::MERCATO] = serialize(new Mercato());
        return unserialize($_SESSION[self::MERCATO]);
    }

    public function prepara() {
        $this->setIdMercato(null);
        $this->setCodMercato(null);
        $this->setDesMercato(null);
        $this->setCittaMercato(null);
        $this->setCodNegozio(null);
        $this->setMercati(null);
        $this->setQtaMercati(0);
    }

    public function load($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_MERCATI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setMercati(pg_fetch_all($result));
            $this->setQtaMercati(pg_num_rows($result));
        } else {
            $this->setMercati(null);
            $this->setQtaMercati(null);
        }
        return $result;
    }

    public function nuovo($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_mercato%' => $this->getCodMercato(),
            '%des_mercato%' => str_replace("'", "''", $this->getDesMercato()),
            '%citta_mercato%' => str_replace("'", "''", $this->getCittaMercato()),
            '%cod_negozio%' => $this->getCodNegozio()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CREA_MERCATO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result)
            $this->load($db);  // refresh dei mercati caricati
        return $result;
    }

    public function aggiorna($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_mercato%' => trim($this->getIdMercato()),
            '%cod_mercato%' => trim($this->getCodMercato()),
            '%des_mercato%' => trim(str_replace("'", "''", $this->getDesMercato())),
            '%citta_mercato%' => trim(str_replace("'", "''", $this->getCittaMercato())),
            '%cod_negozio%' => trim($this->getCodNegozio()),
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_MERCATO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);
        return $result;
    }

    public function cancella($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%id_mercato%' => $this->getIdMercato()
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::CANCELLA_MERCATO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

// 		if ($result) $this->load($db);		// refresh dei mercati caricati
        return $result;
    }

    public function leggi($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $replace = array(
            '%id_mercato%' => trim($this->getIdMercato())
        );
        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_MERCATO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        foreach (pg_fetch_all($result) as $row) {
            $this->setCodMercato($row[self::COD_MERCATO]);
            $this->setDesMercato($row[self::DES_MERCATO]);
            $this->setCittaMercato($row[self::CITTA_MERCATO]);
            $this->setCodNegozio($row[self::COD_NEGOZIO]);
        }
        return $result;
    }

    public function cercaMercatiNegozio($db) {
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cod_negozio%' => $this->getCodNegozio()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::RICERCA_MERCATI_NEGOZIO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setMercati(pg_fetch_all($result));
            $this->setQtaMercati(pg_num_rows($result));
        } else {
            $this->setMercati(null);
            $this->setQtaMercati(null);
        }
        return $result;
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

    public function getIdMercato() {
        return $this->idMercato;
    }

    public function setIdMercato($idMercato) {
        $this->idMercato = $idMercato;
    }

    public function getCodMercato() {
        return $this->codMercato;
    }

    public function setCodMercato($codMercato) {
        $this->codMercato = $codMercato;
    }

    public function getDesMercato() {
        return $this->desMercato;
    }

    public function setDesMercato($desMercato) {
        $this->desMercato = $desMercato;
    }

    public function getCittaMercato() {
        return $this->cittaMercato;
    }

    public function setCittaMercato($cittaMercato) {
        $this->cittaMercato = $cittaMercato;
    }

    public function getCodNegozio() {
        return $this->codNegozio;
    }

    public function setCodNegozio($codNegozio) {
        $this->codNegozio = $codNegozio;
    }

    public function getMercati() {
        return $this->mercati;
    }

    public function setMercati($mercati) {
        $this->mercati = $mercati;
    }

    public function getQtaMercati() {
        return $this->qtaMercati;
    }

    public function setQtaMercati($qtaMercati) {
        $this->qtaMercati = $qtaMercati;
    }

}

?>