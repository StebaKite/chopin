<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'causale.class.php';

class VisualizzaCorrispettivoNegozio extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::VISUALIZZA_CORRISPETTIVO_NEGOZIO) === NULL) {
            parent::setIndexSession(self::VISUALIZZA_CORRISPETTIVO_NEGOZIO, serialize(new VisualizzaCorrispettivoNegozio()));
        }
        return unserialize(parent::getIndexSession(self::VISUALIZZA_CORRISPETTIVO_NEGOZIO));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $causale = Causale::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $registrazione->prepara();

        $registrazione->leggi($db);
        parent::setIndexSession(self::REGISTRAZIONE, serialize($registrazione));

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
        $dettaglioRegistrazione->setIdTablePagina("dettagli_cormer_vis");
        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));

        $negozio = (trim($registrazione->getCodNegozio()) == "ERB") ? "Erba" : $negozio;

        $causale->setCodCausale($registrazione->getCodCausale());
        $causale->leggi($db);

        $risultato_xml = $this->root . $array['template'] . self::XML_CORRISPETTIVO;

        $replace = array(
            '%datareg%' => trim($registrazione->getDatRegistrazione()),
            '%descreg%' => trim($registrazione->getDesRegistrazione()),
            '%causale%' => trim($causale->getDesCausale()),
            '%codneg%' => $negozio,
            '%dettagli%' => trim($this->makeTabellaReadOnlyDettagliRegistrazione($dettaglioRegistrazione))
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        
    }

}