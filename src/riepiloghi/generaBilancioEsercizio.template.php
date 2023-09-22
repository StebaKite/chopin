<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';
require_once 'utility.class.php';
require_once 'bilancio.class.php';

class GeneraBilancioEsercizioTemplate extends RiepiloghiAbstract implements RiepiloghiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::GENERA_BILANCIO_ESERCIZIO_TEMPLATE) === NULL) {
            parent::setIndexSession(self::GENERA_BILANCIO_ESERCIZIO_TEMPLATE, serialize(new GeneraBilancioEsercizioTemplate()));
        }
        return unserialize(parent::getIndexSession(self::GENERA_BILANCIO_ESERCIZIO_TEMPLATE));
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {
        return TRUE;
    }

    public function displayPagina() {

        $bilancio = Bilancio::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_GENERA_BILANCIO_ESERCIZIO;

        /*
          Costruzione delle tabelle
         */
        $this->makeCostiTable($bilancio);
        $this->makeRicaviTable($bilancio);
        $this->makeAttivoTable($bilancio);
        $this->makePassivoTable($bilancio);

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%datareg_da%' => $bilancio->getDataregDa(),
            '%datareg_a%' => $bilancio->getDataregA(),
            '%codneg_sel%' => $bilancio->getCodnegSel(),
            '%catconto_sel%' => $bilancio->getCatconto(),
            '%villa-selected%' => ($bilancio->getCodnegSel() == "ERB") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%tabs%' => $this->makeTabs($bilancio),
            '%saldiInclusichecked%' => ($bilancio->getSaldiInclusi() == "S") ? self::CHECK_THIS_ITEM : self::EMPTYSTRING,
            '%saldiEsclusichecked%' => ($bilancio->getSaldiInclusi() == "N") ? self::CHECK_THIS_ITEM : self::EMPTYSTRING,
            '%activeSaldiInclusi%' => ($bilancio->getSaldiInclusi() == "S") ? self::ACTIVE_THIS_ITEM : self::EMPTYSTRING,
            '%activeSaldiEsclusi%' => ($bilancio->getSaldiInclusi() == "N") ? self::ACTIVE_THIS_ITEM : self::EMPTYSTRING,
            '%anno_esercizio_corrente-selected%' => ($bilancio->getAnnoEsercizioSel() == date("Y")) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%anno_esercizio_menouno-selected%' => ($bilancio->getAnnoEsercizioSel() == date("Y") - 1) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%anno_esercizio_menodue-selected%' => ($bilancio->getAnnoEsercizioSel() == date("Y") - 2) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%anno_esercizio_menotre-selected%' => ($bilancio->getAnnoEsercizioSel() == date("Y") - 3) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%ml.anno_esercizio_corrente%' => date("Y"),
            '%ml.anno_esercizio_menouno%' => date("Y") - 1,
            '%ml.anno_esercizio_menodue%' => date("Y") - 2,
            '%ml.anno_esercizio_menotre%' => date("Y") - 3
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}