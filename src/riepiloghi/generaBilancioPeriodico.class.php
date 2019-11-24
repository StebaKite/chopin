<?php

require_once 'riepiloghi.abstract.class.php';
require_once 'riepiloghi.business.interface.php';
require_once 'generaBilancioPeriodico.template.php';
require_once 'utility.class.php';
require_once 'bilancio.class.php';
require_once 'database.class.php';

class GeneraBilancioPeriodico extends RiepiloghiAbstract implements RiepiloghiBusinessInterface {

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

        if (!isset($_SESSION[self::GENERA_BILANCIO_PERIODICO]))
            $_SESSION[self::GENERA_BILANCIO_PERIODICO] = serialize(new GeneraBilancioPeriodico());
        return unserialize($_SESSION[self::GENERA_BILANCIO_PERIODICO]);
    }

    public function start() {

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $bilancio = Bilancio::getInstance();

        $bilancio->prepara();
        
        $bilancio->setTipoBilancio(self::PERIODICO);
        $_SESSION[self::BILANCIO] = serialize($bilancio);

        $bilancioTemplate = GeneraBilancioPeriodicoTemplate::getInstance();
        $this->preparaPagina($bilancioTemplate);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $bilancioTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {

        $bilancio = Bilancio::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $bilancioTemplate = GeneraBilancioPeriodicoTemplate::getInstance();
        $this->preparaPagina($bilancioTemplate);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $this->ricercaDati($utility);

        $totVoci = $bilancio->getNumCostiTrovati() + $bilancio->getNumRicaviTrovati();
        $_SESSION["messaggio"] = "Trovate " . $totVoci . " voci";
        self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
        
        if ($totVoci > 0) {
            $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), parent::$replace);
        } else {
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), parent::$replace);
        }
        $_SESSION[self::MSG] = $utility->tailTemplate($template);            
        $bilancioTemplate->displayPagina();
        include($this->piede);
    }

    private function ricercaDati($utility) {

        $db = Database::getInstance();
        $bilancio = Bilancio::getInstance();
        $bilancio->prepara();
        $bilancio->setTipoBilancio(self::PERIODICO);

        $bilancio->ricercaCosti($db);
        $bilancio->ricercaRicavi($db);

        if (($bilancio->getCatconto() == self::STATO_PATRIMONIALE) or ( $bilancio->getCatconto() == self::TUTTI_CONTI)) {
            $bilancio->ricercaAttivo($db);
            $bilancio->ricercaPassivo($db);
        }

        $bilancio->ricercaCostiMargineContribuzione($db);       // Conto economico
        $bilancio->ricercaRicaviMargineContribuzione($db);      // Conto economico
        $bilancio->ricercaCostiFissi($db);                      // Conto economico
        
        $_SESSION[self::BILANCIO] = serialize($bilancio);
    }

    private function preparaPagina($bilancioTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_BILANCIO_PERIODICO;
        $_SESSION[self::TITOLO_PAGINA] = "%ml.bilancioPeriodico%";
    }

}

?>
