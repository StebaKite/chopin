<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'configurazioni.controller.class.php';
require_once 'ricercaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class VisualizzaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::VISUALIZZA_CONTO) === NULL) {
            parent::setIndexSession(self::VISUALIZZA_CONTO, serialize(new VisualizzaConto()));
        }
        return unserialize(parent::getIndexSession(self::VISUALIZZA_CONTO));
    }

    public function start() {
        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $conto->leggi($db);
        parent::setIndexSession(self::CONTO, serialize($conto));

        $sottoconto->setCodConto($conto->getCodConto());
        $sottoconto->leggi($db);
        parent::setIndexSession(self::SOTTOCONTO, serialize($sottoconto));

        $risultato_xml = $this->root . $array['template'] . self::XML_CONTO;

        $replace = array(
            '%codice%' => trim($conto->getCodConto()),
            '%descrizione%' => trim($conto->getDesConto()),
            '%categoria%' => trim($conto->getCatConto()),
            '%tipo%' => trim($conto->getTipConto()),
            '%presenzaInBilancio%' => trim($conto->getIndPresenzaInBilancio()),
            '%presenzaSottoconti%' => trim($conto->getIndVisibilitaSottoconti()),
            '%numeroRigaBilancio%' => trim($conto->getNumRigaBilancio()),
            '%sottoconti%' => $this->makeTabellaSottocontiReadonly($conto, $sottoconto)
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {}

}