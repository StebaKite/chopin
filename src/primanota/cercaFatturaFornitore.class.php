<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';

class CercaFatturaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CERCA_FATTURA_FORNITORE])) {
            $_SESSION[self::CERCA_FATTURA_FORNITORE] = serialize(new CercaFatturaFornitore());
        }
        return unserialize($_SESSION[self::CERCA_FATTURA_FORNITORE]);
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($registrazione->getCodCausale() != $array['pagamentoFornitori']) {
            if ($registrazione->cercaFatturaFornitore($db))
                echo "esistente";
            else
                echo " ";
        } else
            echo "     ";
    }

    public function go() {
        $this->start();
    }
}