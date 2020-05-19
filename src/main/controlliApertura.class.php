<?php

require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'nexus6.abstract.class.php';
require_once 'main.business.interface.php';

class ControlliApertura extends Nexus6Abstract implements MainBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CONTROLLI_APERTURA) === NULL) {
            parent::setIndexSession(self::CONTROLLI_APERTURA, serialize(new ControlliApertura()));
        }
        return unserialize(parent::getIndexSession(self::CONTROLLI_APERTURA));
    }

    public function start() {
        $this->go();
    }

    public function go() {
        
        if (parent::getIndexSession('notificaEffettuata') === NULL) {
            $db = Database::getInstance();
            $utility = Utility::getInstance();
            $array = $utility->getConfig();
            
            if ($db->getDBConnection() == null) {

                if ($db->createDatabaseConnection($utility)) {

                    if (!isset($_SESSION['notificaEffettuata'])) {

                        $risultato_xml = $this->root . $array['template'] . self::XML_CONTROLLI_APERTURA;

                        $replace = array(
                            '%errori%' => $this->controllaRegistrazioniInErrore($utility, $db),
                            '%regSenzaNeg%' => $this->controllaRegistrazioniSenzaNegozio($utility, $db),
                            '%regSenzaDet%' => $this->controllaRegistrazioniSenzaDettagli($utility, $db),
                            '%scadenzefor%' => $this->controllaScadenzeFornitoriSuperate($utility, $db),
                            '%scadenzecli%' => $this->controllaScadenzeClientiSuperate($utility, $db),
                        );
                        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
                        $_SESSION['notificaEffettuata'] = "SI";
                        
                        echo $utility->tailTemplate($template);
                    }
                }
            }
        } else {
            echo "";
        }
    }    
}