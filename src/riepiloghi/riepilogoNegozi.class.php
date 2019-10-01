<?php

require_once 'riepiloghiComparati.abstract.class.php';
require_once 'riepiloghi.business.interface.php';
require_once 'riepilogoNegozi.template.php';
require_once 'utility.class.php';
require_once 'riepilogo.class.php';
require_once 'database.class.php';

class RiepilogoNegozi extends RiepiloghiComparatiAbstract implements RiepiloghiBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::RIEPILOGO_NEGOZI]))
            $_SESSION[self::RIEPILOGO_NEGOZI] = serialize(new RiepilogoNegozi());
        return unserialize($_SESSION[self::RIEPILOGO_NEGOZI]);
    }

    public function start() {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $riepilogo = Riepilogo::getInstance();

        $riepilogo->prepara();

        $riepilogoNegoziTemplate = RiepilogoNegoziTemplate::getInstance();
        $this->preparaPagina();

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
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

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->ricercaDati()) {

            $riepilogoNegoziTemplate->displayPagina();

            $totVoci = $riepilogo->getNumCostiComparatiTrovati();
            $_SESSION["messaggio"] = "Trovate " . $totVoci . " voci";
            parent::$replace = array('%messaggio%' => $_SESSION["messaggio"]);

            if ($totVoci > 0) {
                $template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
            } else {
                $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
            }
        } else {

            $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
            self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), parent::$replace);

            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        include($this->piede);
    }

    private function ricercaDati() {

        $db = Database::getInstance();
        $riepilogo = Riepilogo::getInstance();
        $riepilogo->prepara();

        $riepilogo->ricercaCostiComparati($db);
        $riepilogo->ricercaRicaviComparati($db);

        if ($riepilogo->getSoloContoEconomico() == "N") {

            $riepilogo->ricercaAttivoComparati($db);
            $riepilogo->ricercaPassivoComparati($db);
            $riepilogo->ricercaCostiVariabiliNegozi($db);
            $riepilogo->ricercaCostiFissiNegozi($db);
            $riepilogo->ricercaRicaviNegozi($db);
            return TRUE;
        } else {

            $riepilogo->ricercaCostiVariabiliNegozi($db);
            $riepilogo->ricercaCostiFissiNegozi($db);
            $riepilogo->ricercaRicaviNegozi($db);
            return TRUE;
        }

        return FALSE;
    }

    public function preparaPagina() {
        $_SESSION[self::AZIONE] = self::AZIONE_RIEPILOGO_NEGOZI;
        $_SESSION[self::TITOLO_PAGINA] = "%ml.riepilogoNegozi%";
    }

}
