
<?php

require_once 'riepiloghiComparati.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';
require_once 'utility.class.php';
require_once 'riepilogo.class.php';

class RiepilogoNegoziTemplate extends RiepiloghiComparatiAbstract implements RiepiloghiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RIEPILOGO_NEGOZI_TEMPLATE) === NULL) {
            parent::setIndexSession(self::RIEPILOGO_NEGOZI_TEMPLATE, serialize(new RiepilogoNegoziTemplate()));
        }
        return unserialize(parent::getIndexSession(self::RIEPILOGO_NEGOZI_TEMPLATE));
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {
        return TRUE;
    }

    public function displayPagina() {

        $riepilogo = Riepilogo::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_RIEPILOGO_NEGOZI;

        if (parent::isNotEmpty($riepilogo->getCostiComparati()))
            $this->makeTableCostiComparati($riepilogo);

        if (parent::isNotEmpty($riepilogo->getRicaviComparati()))
            $this->makeTableRicaviComparati($riepilogo);

        if (parent::isNotEmpty($riepilogo->getAttivoComparati()))
            $this->makeTableAttivoComparati($riepilogo);

        if (parent::isNotEmpty($riepilogo->getPassivoComparati()))
            $this->makeTablePassivoComparati($riepilogo);

        /*
         * Se ci sono le condizioni calcolo l'mct e il bep e genero le tabelle in output
         */
        if (parent::isNotEmpty($riepilogo->getCostoVariabileVilla()) or
                parent::isNotEmpty($riepilogo->getRicavoVenditaProdottiVilla()) or
                parent::isNotEmpty($riepilogo->getCostoFissoVilla()) or
                parent::isNotEmpty($riepilogo->getCostoVariabileBrembate()) or
                parent::isNotEmpty($riepilogo->getRicavoVenditaProdottiBrembate()) or
                parent::isNotEmpty($riepilogo->getCostoFissoBrembate()) or
                parent::isNotEmpty($riepilogo->getCostoVariabileTrezzo()) or
                parent::isNotEmpty($riepilogo->getRicavoVenditaProdottiTrezzo()) or
                parent::isNotEmpty($riepilogo->getCostoFissoTrezzo())) {

            $this->makeTableMct($riepilogo);
            $this->makeTableBep($riepilogo);
        }

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%datareg_da%' => $riepilogo->getDataregDa(),
            '%datareg_a%' => $riepilogo->getDataregA(),
            '%tabs%' => $this->makeTabs($riepilogo),
            '%saldiInclusichecked%' => ($riepilogo->getSaldiInclusi() == "S") ? self::CHECK_THIS_ITEM : "",
            '%saldiEsclusichecked%' => ($riepilogo->getSaldiInclusi() == "N") ? self::CHECK_THIS_ITEM : "",
            '%activeSaldiInclusi%' => ($riepilogo->getSaldiInclusi() == "S") ? self::ACTIVE_THIS_ITEM : self::EMPTYSTRING,
            '%activeSaldiEsclusi%' => ($riepilogo->getSaldiInclusi() == "N") ? self::ACTIVE_THIS_ITEM : self::EMPTYSTRING,
            '%tuttiContiChecked%' => ($riepilogo->getSoloContoEconomico() == "N") ? self::CHECK_THIS_ITEM : "",
            '%soloContoEconomicoChecked%' => ($riepilogo->getSoloContoEconomico() == "S") ? self::CHECK_THIS_ITEM : "",
            '%activeTutti%' => ($riepilogo->getSoloContoEconomico() == "N") ? self::ACTIVE_THIS_ITEM : "",
            '%activeContoeco%' => ($riepilogo->getSoloContoEconomico() == "S") ? self::ACTIVE_THIS_ITEM : "",
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}

?>