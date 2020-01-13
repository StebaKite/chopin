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
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_MERCATO) === NULL) {
            parent::setIndexSession(self::MODIFICA_MERCATO, serialize(new ModificaMercato()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_MERCATO));
    }

    public function start() {
        $mercato = Mercato::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $mercato->leggi($db);
        parent::setIndexSession(self::CLIENTE, serialize($mercato));

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

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaMercato::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->setRequest("start");
        $controller->start();
    }

}