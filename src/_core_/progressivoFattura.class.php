<?php

require_once 'core.interface.php';
require_once 'coreBase.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class ProgressivoFattura extends CoreBase implements CoreInterface {

    private $root;

    // Nomi colonne tabella ProgressivoFattura

    const CAT_CLIENTE = "cat_cliente";
    const NEG_PROGR = "neg_progr";
    const NUM_FATTURA_ULTIMO = "num_fattura_ultimo";
    const NOTA_TESTA_FATTURA = "nota_testa_fattura";
    const NOTA_PIEDE_FATTURA = "nota_piede_fattura";

    // dati ProgressivoFattura

    private $catCliente;
    private $negProgr;
    private $numFatturaUltimo;
    private $notaTestaFattura;
    private $notaPiedeFattura;
    private $progressiviFattura;
    private $qtaProgressiviFattura;

    // Queries

    const LEGGI_PROGRESSIVI = "/configurazioni/ricercaProgressivoFattura.sql";
    const AGGIORNA_PROGRESSIVI = "/configurazioni/updateProgressivoFattura.sql";
    const CERCA_PROGRESSIVO = "/configurazioni/leggiProgressivoFattura.sql";

    // Metodi

    function __construct() {
        $this->setRoot(parent::getInfoFromServer('DOCUMENT_ROOT'));
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::PROGRESSIVO_FATTURA) === NULL) {
            parent::setIndexSession(self::PROGRESSIVO_FATTURA, serialize(new ProgressivoFattura()));
        }
        return unserialize(parent::getIndexSession(self::PROGRESSIVO_FATTURA));
    }

    public function load($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array();

        $sqlTemplate = $this->getRoot() . $array['query'] . self::LEGGI_PROGRESSIVI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            $this->setProgressiviFattura(pg_fetch_all($result));
            $this->setQtaProgressiviFattura(pg_num_rows($result));
            parent::setIndexSession(self::PROGRESSIVO_FATTURA, serialize($this));
            return $result;
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }        
    }

    public function leggi($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cat_cliente%' => $this->getCatCliente(),
            '%neg_progr%' => $this->getNegProgr()
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::CERCA_PROGRESSIVO;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        if ($result) {
            foreach (pg_fetch_all($result) as $row) {
                $this->setNumFatturaUltimo($row[self::NUM_FATTURA_ULTIMO]);
                $this->setNotaTestaFattura($row[self::NOTA_TESTA_FATTURA]);
                $this->setNotaPiedeFattura($row[self::NOTA_PIEDE_FATTURA]);
            }
            return $result;
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    public function update($db) {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $replace = array(
            '%cat_cliente%' => trim($this->getCatcliente()),
            '%neg_progr%' => trim($this->getNegProgr()),
            '%num_fattura_ultimo%' => trim($this->getNumFatturaUltimo()),
            '%nota_testa_fattura%' => trim($this->getNotaTestaFattura()),
            '%nota_piede_fattura%' => trim($this->getNotaPiedeFattura())
        );

        $sqlTemplate = $this->getRoot() . $array['query'] . self::AGGIORNA_PROGRESSIVI;
        $sql = $utility->tailFile($utility->getQueryTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        if ($result) {
            $db->commitTransaction();
            return TRUE;
        } else {
            throw new Exception("Ooooops, c'è un problema tecnico!");
        }
    }

    // Getters & Setters

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($root) {
        $this->root = $root;
    }

    public function getCatCliente() {
        return $this->catCliente;
    }

    public function setCatCliente($catCliente) {
        $this->catCliente = $catCliente;
    }

    public function getNegProgr() {
        return $this->negProgr;
    }

    public function setNegProgr($negProgr) {
        $this->negProgr = $negProgr;
    }

    public function getNumFatturaUltimo() {
        return $this->numFatturaUltimo;
    }

    public function setNumFatturaUltimo($numFatturaUltimo) {
        $this->numFatturaUltimo = $numFatturaUltimo;
    }

    public function getNotaTestaFattura() {
        return $this->notaTestaFattura;
    }

    public function setNotaTestaFattura($notaTestaFattura) {
        $this->notaTestaFattura = $notaTestaFattura;
    }

    public function getNotaPiedeFattura() {
        return $this->notaPiedeFattura;
    }

    public function setNotaPiedeFattura($notaPiedeFattura) {
        $this->notaPiedeFattura = $notaPiedeFattura;
    }

    public function getProgressiviFattura() {
        return $this->progressiviFattura;
    }

    public function setProgressiviFattura($progressiviFattura) {
        $this->progressiviFattura = $progressiviFattura;
    }

    public function getQtaProgressiviFattura() {
        return $this->qtaProgressiviFattura;
    }

    public function setQtaProgressiviFattura($qtaProgressiviFattura) {
        $this->qtaProgressiviFattura = $qtaProgressiviFattura;
    }

}