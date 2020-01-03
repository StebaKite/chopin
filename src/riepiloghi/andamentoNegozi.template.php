<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';
require_once 'utility.class.php';
require_once 'riepilogo.class.php';

class AndamentoNegoziTemplate extends RiepiloghiAbstract implements RiepiloghiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::ANDAMENTO_NEGOZI_TEMPLATE) === NULL) {
            parent::setIndexSession(self::ANDAMENTO_NEGOZI_TEMPLATE, serialize(new AndamentoNegoziTemplate()));
        }
        return unserialize(parent::getIndexSession(self::ANDAMENTO_NEGOZI_TEMPLATE));
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

        $form = $this->root . $array['template'] . self::PAGINA_ANDAMENTO_NEGOZI;

        if ($riepilogo->getNumCostiAndamentoNegozio() > 0) {
            $this->makeAndamentoCostiTable($riepilogo);
        }

        if ($riepilogo->getNumRicaviAndamentoNegozio() > 0) {
            $this->makeAndamentoRicaviDeltaTable($riepilogo);
        }

        if (self::isNotEmpty($riepilogo->getTotaliAcquistiMesi()) or ( self::isNotEmpty($riepilogo->getTotaliRicaviMesi()))) {
            $this->makeUtilePerditaTable($riepilogo);
            $this->makeTableMargineContribuzioneAndamentoNegozi($riepilogo);
        }

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%datareg_da%' => $riepilogo->getDataregDa(),
            '%datareg_a%' => $riepilogo->getDataregA(),
            '%villa-selected%' => ($riepilogo->getCodnegSel() == self::VILLA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%brembate-selected%' => ($riepilogo->getCodnegSel() == self::BREMBATE) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%trezzo-selected%' => ($riepilogo->getCodnegSel() == self::TREZZO) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%codneg_sel%' => $riepilogo->getCodnegSel(),
            '%tabs%' => $this->makeTabsAndamentoNegozi($riepilogo),
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function tabellaTotaliRiepilogoNegozi($tipoTotale) {

    }

}

?>