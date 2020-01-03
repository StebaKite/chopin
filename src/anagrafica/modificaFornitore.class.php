<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'anagrafica.controller.class.php';
require_once 'ricercaFornitore.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class ModificaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_FORNITORE) === NULL) {
            parent::setIndexSession(self::MODIFICA_FORNITORE, serialize(new ModificaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_FORNITORE));
    }

    public function start() {
        $fornitore = Fornitore::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fornitore->leggi($db);
        parent::setIndexSession(self::FORNITORE, serialize($fornitore));

        $risultato_xml = $this->root . $array['template'] . self::XML_FORNITORE;

        $replace = array(
            '%codice%' => trim($fornitore->getCodFornitore()),
            '%descrizione%' => trim($fornitore->getDesFornitore()),
            '%indirizzo%' => trim($fornitore->getDesIndirizzoFornitore()),
            '%citta%' => trim($fornitore->getDesCittaFornitore()),
            '%cap%' => trim($fornitore->getCapFornitore()),
            '%tipoAddebito%' => trim($fornitore->getTipAddebito()),
            '%giorniScadenzaFattura%' => trim($fornitore->getNumGgScadenzaFattura())
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $fornitore = Fornitore::getInstance();
        $db = Database::getInstance();
        $db->beginTransaction();

        if ($fornitore->update($db)) {
            $db->commitTransaction();
        } else {
            $db->rollbackTransaction();
        }

        parent::setIndexSession("Obj_anagraficacontroller", serialize(new AnagraficaController(RicercaFornitore::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_anagraficacontroller"));
        $controller->start();
    }

}