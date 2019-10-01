<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'andamentoMercati.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class AndamentoMercati extends RiepiloghiAbstract implements RiepiloghiBusinessInterface {

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

        if (!isset($_SESSION[self::ANDAMENTO_MERCATI]))
            $_SESSION[self::ANDAMENTO_MERCATI] = serialize(new AndamentoMercati());
        return unserialize($_SESSION[self::ANDAMENTO_MERCATI]);
    }

    public function start() {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $riepilogo = Riepilogo::getInstance();

        $riepilogo->prepara();

        $andamentoMercatiTemplate = AndamentoMercatiTemplate::getInstance();
        $this->preparaPagina();

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $andamentoMercatiTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {

        $riepilogo = Riepilogo::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $riepilogo->prepara();

        $andamentoMercatiTemplate = AndamentoMercatiTemplate::getInstance();
        $this->preparaPagina();

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->ricercaDati()) {

            $andamentoMercatiTemplate->displayPagina();
            $riepilogo = Riepilogo::getInstance();

            $numRicavi = $riepilogo->getNumRicaviAndamentoMercatoVilla() +
                    $riepilogo->getNumRicaviAndamentoMercatoBrembate() +
                    $riepilogo->getNumRicaviAndamentoMercatoTrezzo();

            $_SESSION["messaggio"] = "Trovate " . $numRicavi . " voci di ricavo";
            $replace = array('%messaggio%' => $_SESSION["messaggio"]);

            if (($numRicavi) > 0) {
                $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), $replace);
            } else {
                $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), $replace);
            }
        } else {

            $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
            self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), parent::$replace);

            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        include($this->piede);
    }

    public function ricercaDati() {

        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $riepilogo = Riepilogo::getInstance();
        $riepilogo->prepara();

        $negozi = explode(",", $array["negozi"]);       // VIL,BRE,TRE  da config

        foreach ($negozi as $negozio) {
            $riepilogo->setCodnegSel($negozio);
            $riepilogo->ricercaVociAndamentoRicaviMercato($db);
        }
        return true;
    }

    public function preparaPagina() {

        $_SESSION[self::AZIONE] = self::AZIONE_ANDAMENTO_MERCATI;
        $_SESSION[self::TITOLO_PAGINA] = "%ml.andamentoMercati%";
    }

}

?>
