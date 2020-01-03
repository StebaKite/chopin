<?php

require_once 'nexus6.abstract.class.php';
require_once 'main.business.interface.php';
require_once 'main.template.php';
require_once 'database.class.php';
require_once 'utility.class.php';

class Main extends Nexus6Abstract implements MainBusinessInterface {

    public $messaggio;
    public $utility;
    public $array;

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');

        $this->testata = $this->root . $this->array['testataPagina'];
        $this->piede = $this->root . $this->array['piedePagina'];
        $this->messaggioErrore = $this->root . $this->array['messaggioErrore'];
        $this->messaggioInfo = $this->root . $this->array['messaggioInfo'];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MAIN) === NULL) {
            parent::setIndexSession(self::MAIN, serialize(new Main()));
        }
        return unserialize(parent::getIndexSession(self::MAIN));
    }

    public function start() {

	$utility = Utility::getInstance();
        $db = Database::getInstance();
        if ($db->getDBConnection() == null) {

            /**
             * Apertura della connessione col Database
             */
            if ($db->createDatabaseConnection($utility)) {

                $mainTemplate = MainTemplate::getInstance();
                $mainTemplate->displayPagina();
            } else {
                echo('Connessione al Database fallita!!');
            }
        }
    }

    public function go() {}
}