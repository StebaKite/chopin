<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'sottoconto.class.php';

class EstraiMovimentiSottoconto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::ESTRAI_MOVIMENTI_SOTTOCONTO])) {
            $_SESSION[self::ESTRAI_MOVIMENTI_SOTTOCONTO] = serialize(new EstraiMovimentiSottoconto());
        }
        return unserialize($_SESSION[self::ESTRAI_MOVIMENTI_SOTTOCONTO]);
    }

    public function start() {
        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($sottoconto->cercaRegistrazioni($db)) {

            $risultato_xml = $this->root . $array['template'] . self::XML_SOTTOCONTO;

            $replace = array(
                '%conto%' => trim($sottoconto->getCodConto()),
                '%sottoconto%' => trim($sottoconto->getCodSottoconto()),
                '%movimenti%' => $this->makeTabellaMovimentiSottoconto($sottoconto)
            );
            $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
            echo $utility->tailTemplate($template);
        }
    }

    public function go() {}

}