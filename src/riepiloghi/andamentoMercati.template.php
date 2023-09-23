<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';
require_once 'utility.class.php';
require_once 'riepilogo.class.php';

class AndamentoMercatiTemplate extends RiepiloghiAbstract implements RiepiloghiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RIEPILOGO_MERCATI_TEMPLATE) === NULL) {
            parent::setIndexSession(self::RIEPILOGO_MERCATI_TEMPLATE, serialize(new AndamentoMercatiTemplate()));
        }
        return unserialize(parent::getIndexSession(self::RIEPILOGO_MERCATI_TEMPLATE));
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

        $form = $this->root . $array['template'] . parent::PAGINA_ANDAMENTO_MERCATI;

        $mercatiTabs = array();

        $negozi = explode(",", $array["negozi"]);       // VIL,BRE,TRE da config

        foreach ($negozi as $negozio) {

            switch ($negozio) {
                case self::ERBA:
                    if ($riepilogo->getNumRicaviAndamentoMercatoVilla() > 0) {
                        $mercatiTabs[$negozio] = $this->makeAndamentoRicaviMercatoTable($riepilogo->getRicaviAndamentoMercatoVilla());
                    }
                    break;
            }
        }

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%datareg_da%' => $riepilogo->getDataregDa(),
            '%datareg_a%' => $riepilogo->getDataregA(),
            '%villa-selected%' => ($riepilogo->getCodnegSel() == self::ERBA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%tabs%' => (count($mercatiTabs) > 0 ? $this->makeTabsAndamentoMercati($mercatiTabs) : self::EMPTYSTRING)
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}