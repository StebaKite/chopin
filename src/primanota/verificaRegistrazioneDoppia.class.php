<?php

/**
 * Description of controllaRegistrazioneDoppia
 *
 * @author BarbieriStefano
 */

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'registrazione.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class VerificaRegistrazioneDoppia extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }    
    
    public static function getInstance() {
        if (parent::getIndexSession(self::VERIFICA_REGISTRAZIONE_DOPPIA) === NULL) {
            parent::setIndexSession(self::VERIFICA_REGISTRAZIONE_DOPPIA, serialize(new VerificaRegistrazioneDoppia()));
        }
        return unserialize(parent::getIndexSession(self::VERIFICA_REGISTRAZIONE_DOPPIA));        
    }

    public function start() {

        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();      
        if ($registrazione->cercaRegistrazioneDoppia($db)) {
            echo "Operazione doppia";
        } else {
            echo self::EMPTYSTRING;
        }
    }

    public function go() {
        
    }

}
