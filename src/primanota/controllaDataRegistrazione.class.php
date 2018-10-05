<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'lavoroPianificato.class.php';

class ControllaDataRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CONTROLLA_DATA_REGISTRAZIONE]))
            $_SESSION[self::CONTROLLA_DATA_REGISTRAZIONE] = serialize(new ControllaDataRegistrazione());
        return unserialize($_SESSION[self::CONTROLLA_DATA_REGISTRAZIONE]);
    }

    public function start() {
        $dataOk = false;

        $registrazione = Registrazione::getInstance();
        $lavoroPianificato = LavoroPianificato::getInstance();
        $db = Database::getInstance();

        $lavoroPianificato->load($db, $this->root);

        if ($lavoroPianificato->getQtaLavoriPianificati() > 0) {

            foreach ($lavoroPianificato->getLavoriPianificati() as $unLavoro) {

                /**
                 * Se la registrazione ha una data di registrazione che cade all'interno di un mese in linea è ok.
                 * Salto tutti gli eventuali lavori pianificati che cadono in giorni diversi dal primo del mese
                 */
                if (date("d", strtotime($unLavoro[LavoroPianificato::DAT_LAVORO])) == "01") {

                    $dataRegistrazione = strtotime(str_replace('/', '-', $registrazione->getDatRegistrazione()));

                    if ($dataRegistrazione >= strtotime($unLavoro[LavoroPianificato::DAT_LAVORO])) {
                        $dataOk = true;
                        break;
                    }
                }
            }
        }

        if ($dataOk)
            echo "";
        else
            echo "Data non ammessa";
    }

    public function go() {
        $this->start();
    }

}

?>