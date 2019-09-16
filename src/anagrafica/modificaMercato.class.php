<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'mercato.class.php';
require_once 'ricercaMercato.class.php';
require_once 'anagrafica.controller.class.php';

class ModificaMercato extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::MODIFICA_MERCATO])) {
            $_SESSION[self::MODIFICA_MERCATO] = serialize(new ModificaMercato());
        }
        return unserialize($_SESSION[self::MODIFICA_MERCATO]);
    }

    public function start() {
        $mercato = Mercato::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $mercato->leggi($db);
        $_SESSION[self::CLIENTE] = serialize($cliente);

        $risultato_xml = $this->root . $array['template'] . self::XML_MERCATO;

        $replace = array(
            '%codice%' => trim($mercato->getCodMercato()),
            '%descrizione%' => trim($mercato->getDesMercato()),
            '%citta%' => trim($mercato->getCittaMercato()),
            '%negozio%' => trim($mercato->getCodNegozio())
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $mercato = Mercato::getInstance();
        $db = Database::getInstance();
        $db->beginTransaction();

        if ($mercato->aggiorna($db)) {
            $db->commitTransaction();
        } else {
            $db->rollbackTransaction();
        }

        $_SESSION["Obj_anagraficacontroller"] = serialize(new AnagraficaController(RicercaMercato::getInstance()));

        $controller = unserialize($_SESSION["Obj_anagraficacontroller"]);
        $controller->setRequest("start");
        $controller->start();
    }

}