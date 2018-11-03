<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cambiaContoStep1.template.php';
require_once 'strumenti.business.interface.php';

class CambiaContoStep1 extends StrumentiAbstract implements StrumentiBusinessInterface {

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
        if (!isset($_SESSION[self::CAMBIA_CONTO_STEP1]))
            $_SESSION[self::CAMBIA_CONTO_STEP1] = serialize(new CambiaContoStep1());
        return unserialize($_SESSION[self::CAMBIA_CONTO_STEP1]);
    }

    public function start() {

        $registrazione = Registrazione::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $registrazione->preparaFiltri();

        $cambiaContoStep1Template = CambiaContoStep1Template::getInstance();
        $this->preparaPagina($cambiaContoStep1Template);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $cambiaContoStep1Template->displayPagina();
        include($this->piede);
    }

    public function go() {

        $registrazione = Registrazione::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $cambiaContoStep1Template = CambiaContoStep1Template::getInstance();

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($cambiaContoStep1Template->controlliLogici()) {

            if ($registrazione->leggiRegistrazioniConto($db)) {

                if (isset($_SESSION["messaggioCambioConto"])) {
                    $_SESSION[self::MESSAGGIO] = $_SESSION["messaggioCambioConto"] . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni";
                    unset($_SESSION["messaggioCambioConto"]);
                } else {
                    $_SESSION[self::MESSAGGIO] = "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni";
                }    
                self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);

                if ($registrazione->getQtaRegistrazioni() > 0) {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                } else {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                }
                $_SESSION[self::MSG] = $utility->tailTemplate($template);
            } else {
                $_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni";
                self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
                $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
                $_SESSION[self::MSG] = $utility->tailTemplate($template);
            }
        } else {
            self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }

        $this->preparaPagina($cambiaContoStep1Template);
        $cambiaContoStep1Template->displayPagina();
        include($this->piede);
    }

    public function preparaPagina($ricercaRegistrazioneTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_RICERCA_REGISTRAZIONE;
        $_SESSION[self::TIP_CONFERMA] = "%ml.confermaRicercaRegistrazione%";
        $_SESSION[self::TITOLO_PAGINA] = "%ml.cambioContoStep1%";
    }
}