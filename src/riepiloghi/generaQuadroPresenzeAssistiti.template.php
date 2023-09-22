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
            '%codneg_sel%' => $presenzaAssistito->getCodneg(),
            '%villa-selected%' => ($presenzaAssistito->getCodneg() === "ERB") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%risultato_ricerca%' => ($presenzaAssistito->getNumPresenze() > 0) ? $this->makePresenzeAssistiti($presenzaAssistito) : self::EMPTYSTRING
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function inizializzaPagina() {
        
    }

}