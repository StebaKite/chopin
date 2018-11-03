<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cambiaContoStep2.template.php';
require_once 'strumenti.business.interface.php';

class CambiaContoStep2 extends StrumentiAbstract implements StrumentiBusinessInterface {

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
        if (!isset($_SESSION[self::CAMBIA_CONTO_STEP2]))
            $_SESSION[self::CAMBIA_CONTO_STEP2] = serialize(new CambiaContoStep2());
        return unserialize($_SESSION[self::CAMBIA_CONTO_STEP2]);
    }

    public function start() {

        $registrazione = Registrazione::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $registrazione->preparaFiltri();

        $cambiaContoStep2Template = CambiaContoStep2Template::getInstance();
        $this->preparaPagina($cambiaContoStep1Template);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $cambiaContoStep2Template->displayPagina();
        include($this->piede);
    }

    public function go() {
        
    }

    public function preparaPagina($ricercaRegistrazioneTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_CAMBIA_CONTO_STEP2;
        $_SESSION[self::TIP_CONFERMA] = "%ml.confermaContoDestinazione%";
        $_SESSION[self::TITOLO_PAGINA] = "%ml.cambioContoStep2%";
    }
}

?>