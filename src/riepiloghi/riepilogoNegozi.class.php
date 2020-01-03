<?php

require_once 'riepiloghiComparati.abstract.class.php';
require_once 'riepiloghi.business.interface.php';
require_once 'riepilogoNegozi.template.php';
require_once 'utility.class.php';
require_once 'riepilogo.class.php';
require_once 'database.class.php';

class RiepilogoNegozi extends RiepiloghiComparatiAbstract implements RiepiloghiBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RIEPILOGO_NEGOZI) === NULL) {
            parent::setIndexSession(self::RIEPILOGO_NEGOZI, serialize(new RiepilogoNegozi()));
        }
        return unserialize(parent::getIndexSession(self::RIEPILOGO_NEGOZI));
    }

    public function start() {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $riepilogo = Riepilogo::getInstance();

        $riepilogo->prepara();

        $riepilogoNegoziTemplate = RiepilogoNegoziTemplate::getInstance();
        $this->preparaPagina();

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $riepilogoNegoziTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {

        $riepilogo = Riepilogo::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $riepilogo->prepara();

        $riepilogoNegoziTemplate = RiepilogoNegoziTemplate::getInstance();
        $this->preparaPagina();

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);
        
        $this->ricercaDati($riepilogo);

        $totVoci = $riepilogo->getNumCostiComparatiTrovati();
        parent::setIndexSession(self::MESSAGGIO, "Trovate " . $totVoci . " voci");
        parent::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));

        if ($totVoci > 0) {
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), parent::$replace);
        } else {
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), parent::$replace);
        }
            
        parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        $riepilogoNegoziTemplate->displayPagina();
        include($this->piede);
    }

    private function ricercaDati($riepilogo) {

        $db = Database::getInstance();

        $riepilogo->ricercaCostiComparati($db);
        $riepilogo->ricercaRicaviComparati($db);

        if ($riepilogo->getSoloContoEconomico() == "N") {

            $riepilogo->ricercaAttivoComparati($db);
            $riepilogo->ricercaPassivoComparati($db);
            $riepilogo->ricercaCostiVariabiliNegozi($db);
            $riepilogo->ricercaCostiFissiNegozi($db);
            $riepilogo->ricercaRicaviNegozi($db);
        } else {

            $riepilogo->ricercaCostiVariabiliNegozi($db);
            $riepilogo->ricercaCostiFissiNegozi($db);
            $riepilogo->ricercaRicaviNegozi($db);
        }
    }

    public function preparaPagina() {
        parent::setIndexSession(self::AZIONE, self::AZIONE_RIEPILOGO_NEGOZI);
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.riepilogoNegozi%");
    }

}
