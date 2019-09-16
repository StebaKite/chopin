<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaCausale.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'causale.class.php';

class ModificaCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::MODIFICA_CAUSALE])) {
            $_SESSION[self::MODIFICA_CAUSALE] = serialize(new ModificaCausale());
        }
        return unserialize($_SESSION[self::MODIFICA_CAUSALE]);
    }

    public function start() {
        $causale = Causale::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $db = Database::getInstance();

        $causale->leggi($db);
        $_SESSION[self::CAUSALE] = serialize($causale);

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

        $_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaCausale::getInstance()));
        $controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
        $controller->start();
    }

}