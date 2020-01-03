<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';
require_once 'utility.class.php';
require_once 'bilancio.class.php';

class GeneraBilancioPeriodicoTemplate extends RiepiloghiAbstract implements RiepiloghiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::GENERA_BILANCIO_PERIODICO_TEMPLATE) === NULL) {
            parent::setIndexSession(self::GENERA_BILANCIO_PERIODICO_TEMPLATE, serialize(new GeneraBilancioPeriodicoTemplate()));
        }
        return unserialize(parent::getIndexSession(self::GENERA_BILANCIO_PERIODICO_TEMPLATE));
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

        $form = $this->root . $array['template'] . self::PAGINA_GENERA_BILANCIO_PERIODICO;

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
            '%tuttiContiChecked%' => ($bilancio->getSoloContoEconomico() == "N") ? self::CHECK_THIS_ITEM : "",
            '%soloContoEconomicoChecked%' => ($bilancio->getSoloContoEconomico() == "S") ? self::CHECK_THIS_ITEM : "",
            '%activeTutti%' => ($bilancio->getSoloContoEconomico() == "N") ? self::ACTIVE_THIS_ITEM : "",
            '%activeContoeco%' => ($bilancio->getSoloContoEconomico() == "S") ? self::ACTIVE_THIS_ITEM : "",
            '%villa-selected%' => ($bilancio->getCodnegSel() == "VIL") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%brembate-selected%' => ($bilancio->getCodnegSel() == "BRE") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%trezzo-selected%' => ($bilancio->getCodnegSel() == "TRE") ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%tabs%' => $this->makeTabs($bilancio),
            '%saldiInclusichecked%' => ($bilancio->getSaldiInclusi() == "S") ? self::CHECK_THIS_ITEM : self::EMPTYSTRING,
            '%saldiEsclusichecked%' => ($bilancio->getSaldiInclusi() == "N") ? self::CHECK_THIS_ITEM : self::EMPTYSTRING,
            '%activeSaldiInclusi%' => ($bilancio->getSaldiInclusi() == "S") ? self::ACTIVE_THIS_ITEM : self::EMPTYSTRING,
            '%activeSaldiEsclusi%' => ($bilancio->getSaldiInclusi() == "N") ? self::ACTIVE_THIS_ITEM : self::EMPTYSTRING,
            '%ml.anno_esercizio_corrente%' => date("Y"),
            '%ml.anno_esercizio_menouno%' => date("Y") - 1,
            '%ml.anno_esercizio_menodue%' => date("Y") - 2,
            '%ml.anno_esercizio_menotre%' => date("Y") - 3
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}

?>