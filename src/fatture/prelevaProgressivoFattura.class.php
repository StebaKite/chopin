<?php

require_once 'fattura.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fattura.class.php';
require_once 'fatture.business.interface.php';

class PrelevaProgressivoFattura extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {

        if (parent::getIndexSession(self::PRELEVA_PROGRESSIVO_FATTURA) === NULL) {
            parent::setIndexSession(self::PRELEVA_PROGRESSIVO_FATTURA, serialize(new PrelevaProgressivoFattura()));
        }
        return unserialize(parent::getIndexSession(self::PRELEVA_PROGRESSIVO_FATTURA));
    }

    public function start() {

        $fattura = Fattura::getInstance();
        $db = Database::getInstance();
        $numeroFatturaUltimo = $fattura->caricaNumeroFattura($db);
        echo $fattura->getNumfatturaUltimo() + 1;
    }

    public function go() {

    }

}

?>