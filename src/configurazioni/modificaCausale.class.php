<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class ModificaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_CAUSALE) === NULL) {
            parent::setIndexSession(self::MODIFICA_CAUSALE, serialize(new ModificaCausale()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_CAUSALE));
    }

    public function start() {
        $causale = Causale::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $db = Database::getInstance();

        $causale->leggi($db);
        parent::setIndexSession(self::CAUSALE, serialize($causale));

        $risultato_xml = $this->root . $array['template'] . self::XML_CAUSALE;

        $replace = array(
            '%codice%' => trim($causale->getCodCausale()),
            '%descrizione%' => trim($causale->getDesCausale()),
            '%categoria%' => trim($causale->getCatCausale()),
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $causale = Causale::getInstance();
        $db = Database::getInstance();
        $causale->aggiorna($db);

        parent::setIndexSession("Obj_configurazionicontroller", serialize(new ConfigurazioniController(RicercaCausale::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_configurazionicontroller"));
        $controller->start();
    }

}