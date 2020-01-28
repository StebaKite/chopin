<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';
require_once 'utility.class.php';
require_once 'presenzaAssistito.class.php';

class GeneraQuadroPresenzeAssistitiTemplate extends RiepiloghiAbstract implements RiepiloghiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::GENERA_QUADRO_PRESENZE_ASSISTITI_TEMPLATE) === NULL) {
            parent::setIndexSession(self::GENERA_QUADRO_PRESENZE_ASSISTITI_TEMPLATE, serialize(new GeneraQuadroPresenzeAssistitiTemplate()));
        }
        return unserialize(parent::getIndexSession(self::GENERA_QUADRO_PRESENZE_ASSISTITI_TEMPLATE));        
    }

    public function controlliLogici() {
        return TRUE;        
    }

    public function displayPagina() {

        $presenzaAssistito = PresenzaAssistito::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_GENERA_PRESENZE_ASSISTITI;

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%anno%' => $presenzaAssistito->getAnno(),
            '%mese%' => $presenzaAssistito->getMese(),
            '%codneg_sel%' => $presenzaAssistito->getCodneg(),
            '%villa-selected%' => ($presenzaAssistito->getCodneg() === "VIL") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%brembate-selected%' => ($presenzaAssistito->getCodneg() === "BRE") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%trezzo-selected%' => ($presenzaAssistito->getCodneg() === "TRE") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_0%' => ($presenzaAssistito->getMese() === self::EMPTYSTRING) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_1%' => ($presenzaAssistito->getMese() === '01') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_2%' => ($presenzaAssistito->getMese() === '02') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_3%' => ($presenzaAssistito->getMese() === '03') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_4%' => ($presenzaAssistito->getMese() === '04') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_5%' => ($presenzaAssistito->getMese() === '05') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_6%' => ($presenzaAssistito->getMese() === '06') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_7%' => ($presenzaAssistito->getMese() === '07') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_8%' => ($presenzaAssistito->getMese() === '08') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_9%' => ($presenzaAssistito->getMese() === '09') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_10%' => ($presenzaAssistito->getMese() === '10') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_11%' => ($presenzaAssistito->getMese() === '11') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%selected_12%' => ($presenzaAssistito->getMese() === '12') ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            
            '%risultato_ricerca%' => $this->makePresenzeAssistiti($presenzaAssistito)
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function inizializzaPagina() {
        
    }

}