<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'causale.class.php';
require_once 'ricercaRegistrazione.template.php';
require_once 'scadenzaCliente.class.php';
require_once 'scadenzaFornitore.class.php';

class RicercaRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    private $refererFunctionName;

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
        if (!isset($_SESSION[self::RICERCA_REGISTRAZIONE])) {
            $_SESSION[self::RICERCA_REGISTRAZIONE] = serialize(new RicercaRegistrazione());
        }
        return unserialize($_SESSION[self::RICERCA_REGISTRAZIONE]);
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        unset($_SESSION[self::FUNCTION_REFERER]);
        unset($_SESSION[self::MSG]);

        $registrazione->prepara();
        $dettaglioRegistrazione->prepara();
        $scadenzaCliente->prepara();
        $scadenzaFornitore->prepara();
        
        $registrazione->preparaFiltri();
        $this->setRefererFunctionName("");

        $ricercaRegistrazioneTemplate = RicercaRegistrazioneTemplate::getInstance();
        $this->preparaPagina($ricercaRegistrazioneTemplate);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

//        $_SESSION[self::CAUSALE] = $causale;

        $ricercaRegistrazioneTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        unset($_SESSION[self::FUNCTION_REFERER]);

        $registrazione->preparaFiltri();
        $dettaglioRegistrazione->prepara();
        $scadenzaCliente->prepara();
        $scadenzaFornitore->prepara();

        $ricercaRegistrazioneTemplate = RicercaRegistrazioneTemplate::getInstance();
        $this->preparaPagina($ricercaRegistrazioneTemplate);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($ricercaRegistrazioneTemplate->controlliLogici()) {

            if ($registrazione->load($db)) {

                $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);

                /**
                 * Gestione del messaggio proveniente da altre funzioni
                 */
                if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
                    $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni";
                    unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
                } elseif (isset($_SESSION[self::MSG_DA_MODIFICA])) {
                    $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_MODIFICA] . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni";
                    unset($_SESSION[self::MSG_DA_MODIFICA]);
                } elseif (isset($_SESSION[self::MSG_DA_CREAZIONE])) {
                    $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CREAZIONE] . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni";
                    unset($_SESSION[self::MSG_DA_CREAZIONE]);
                } else {
                    $_SESSION[self::MESSAGGIO] = "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni";
                }

                self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);

                $pos = strpos($_SESSION[self::MESSAGGIO], "ERRORE");
                if ($pos === false) {
                    if ($registrazione->getQtaRegistrazioni() > 0)
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                    else
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                } else
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);

                $_SESSION[self::MSG] = $utility->tailTemplate($template);
            }
            else {

                $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
                self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
                $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                $_SESSION[self::MSG] = $utility->tailTemplate($template);
            }
        } else {

            self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        $ricercaRegistrazioneTemplate->displayPagina();

        include($this->piede);
    }

    public function preparaPagina($ricercaRegistrazioneTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_RICERCA_REGISTRAZIONE;
        $_SESSION[self::TIP_CONFERMA] = "%ml.confermaRicercaRegistrazione%";
        $_SESSION[self::TITOLO_PAGINA] = "%ml.ricercaRegistrazione%";
    }

    public function getRefererFunctionName() {
        return $this->refererFunctionName;
    }

    public function setRefererFunctionName($refererFunctionName) {
        $this->refererFunctionName = $refererFunctionName;
    }

}