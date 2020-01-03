<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaProgressivoFattura.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';

class ModificaProgressivoFattura extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::MODIFICA_PROGRESSIVO_FATTURA) === NULL) {
            parent::setIndexSession(self::MODIFICA_PROGRESSIVO_FATTURA, serialize(new ModificaProgressivoFattura()));
        }
        return unserialize(parent::getIndexSession(self::MODIFICA_PROGRESSIVO_FATTURA));
    }

    public function start() {

        $progressivoFattura = ProgressivoFattura::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $progressivoFattura->leggi($db);
        parent::setIndexSession(self::PROGRESIVO_FATTURA, serialize($progressivoFattura));

        $risultato_xml = $this->root . $array['template'] . self::XML_PROGRESSIVO;

        $replace = array(
            '%categoria%' => trim($progressivoFattura->getCatCliente()),
            '%negozio%' => trim($progressivoFattura->getNegProgr()),
            '%numfatturaultimo%' => trim($progressivoFattura->getNumFatturaUltimo()),
            '%notatestata%' => trim($progressivoFattura->getNotaTestaFattura()),
            '%notapiede%' => trim($progressivoFattura->getNotaPiedeFattura()),
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $progressivoFattura = ProgressivoFattura::getInstance();
        $db = Database::getInstance();
        
        $progressivoFattura->update($db);
        $progressivoFattura->load($db);

        parent::setIndexSession("Obj_configurazionicontroller", serialize(new ConfigurazioniController(RicercaProgressivoFattura::getInstance())));
        $controller = unserialize(parent::getIndexSession("Obj_configurazionicontroller"));
        $controller->start();
    }

}