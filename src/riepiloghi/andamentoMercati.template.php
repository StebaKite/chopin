<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.presentation.interface.php';
require_once 'utility.class.php';
require_once 'riepilogo.class.php';

class AndamentoMercatiTemplate extends RiepiloghiAbstract implements RiepiloghiPresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::RIEPILOGO_MERCATI_TEMPLATE]))
            $_SESSION[self::RIEPILOGO_MERCATI_TEMPLATE] = serialize(new AndamentoMercatiTemplate());
        return unserialize($_SESSION[self::RIEPILOGO_MERCATI_TEMPLATE]);
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
                case self::VILLA:
                    if ($riepilogo->getNumRicaviAndamentoMercatoVilla() > 0) {
                        $mercatiTabs[$negozio] = $this->makeAndamentoRicaviMercatoTable($riepilogo->getRicaviAndamentoMercatoVilla());
                    }
                    break;
                case self::BREMBATE:
                    if ($riepilogo->getNumRicaviAndamentoMercatoBrembate() > 0) {
                        $mercatiTabs[$negozio] = $this->makeAndamentoRicaviMercatoTable($riepilogo->getRicaviAndamentoMercatoBrembate());
                    }
                    break;
                case self::TREZZO:
                    if ($riepilogo->getNumRicaviAndamentoMercatoTrezzo() > 0) {
                        $mercatiTabs[$negozio] = $this->makeAndamentoRicaviMercatoTable($riepilogo->getRicaviAndamentoMercatoTrezzo());
                    }
                    break;
            }
        }

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%datareg_da%' => $riepilogo->getDataregDa(),
            '%datareg_a%' => $riepilogo->getDataregA(),
            '%villa-selected%' => ($riepilogo->getCodnegSel() == self::VILLA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%brembate-selected%' => ($riepilogo->getCodnegSel() == self::BREMBATE) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%trezzo-selected%' => ($riepilogo->getCodnegSel() == self::TREZZO) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%tabs%' => (count($mercatiTabs > 0) ? $this->makeTabsAndamentoMercati($mercatiTabs) : self::EMPTYSTRING)
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}

?>