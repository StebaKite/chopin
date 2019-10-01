<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep3Template extends StrumentiAbstract implements StrumentiPresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CAMBIA_CONTO_STEP3_TEMPLATE]))
            $_SESSION[self::CAMBIA_CONTO_STEP3_TEMPLATE] = serialize(new CambiaContoStep3Template());
        return unserialize($_SESSION[self::CAMBIA_CONTO_STEP3_TEMPLATE]);
    }

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {

        $esito = TRUE;
        $msg = "<br>";

        if ($msg != "<br>") {
            $_SESSION["messaggio"] = $msg;
        } else {
            unset($_SESSION["messaggio"]);
        }

        return $esito;
    }

    public function displayPagina() {

        $registrazione = Registrazione::getInstance();
        $conto = Conto::getInstance();
        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_CAMBIO_CONTO_STEP3;

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
            '%datareg_da%' => $registrazione->getDatRegistrazioneDa(),
            '%datareg_a%' => $registrazione->getDatRegistrazioneA(),
            '%numRegSel%' => $registrazione->getQtaRegistrazioni(),
            '%contoOrig%' => $registrazione->getCodContoSel(),
            '%contoDest%' => $conto->getCodContoSelNuovo(),
            '%codneg_sel%' => $conto->getCodContoSel(),
        );
        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }
}

?>