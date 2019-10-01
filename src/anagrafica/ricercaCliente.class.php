<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaCliente.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';

class RicercaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
        if (!isset($_SESSION[self::RICERCA_CLIENTE])) {
            $_SESSION[self::RICERCA_CLIENTE] = serialize(new RicercaCliente());
        }
        return unserialize($_SESSION[self::RICERCA_CLIENTE]);
    }

    public function start() {

        // Template

        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $ricercaClienteTemplate = RicercaClienteTemplate::getInstance();

        $this->preparaPagina($ricercaClienteTemplate);

        $replace = (isset($_SESSION[self::AMBIENTE]) ? array('%amb%' => $_SESSION[self::AMBIENTE], '%users%' => $_SESSION[self::USERS], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->refreshClienti($db, $cliente)) {

            if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
                $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti";
                unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
            } elseif (isset($_SESSION[self::MSG_DA_CREAZIONE])) {
                $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CREAZIONE] . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti";
                unset($_SESSION[self::MSG_DA_CREAZIONE]);
            } elseif (isset($_SESSION[self::MSG_DA_MODIFICA])) {
                $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_MODIFICA] . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti";
                unset($_SESSION[self::MSG_DA_MODIFICA]);
            } else {
                $_SESSION[self::MESSAGGIO] = "Trovati " . $cliente->getQtaClienti() . " clienti";
            }

            self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);

            $pos = strpos($_SESSION[self::MESSAGGIO], "ERRORE");
            if ($pos === false) {
                if ($cliente->getQtaClienti() > 0) {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                } else {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                }
            } else {
                $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            }

            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        else {

            self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        $ricercaClienteTemplate->displayPagina();

        include($this->piede);
    }

    public function go() {
        $this->start();
    }

    private function refreshClienti($db, $cliente) {

        if (sizeof($cliente->getClienti()) == 0) {

            if (!$cliente->load($db)) {
                $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
                return false;
            }
            $_SESSION[self::CLIENTE] = serialize($cliente);
        }
        return true;
    }

    private function preparaPagina($ricercaCausaleTemplate) {

        $_SESSION["azione"] = self::AZIONE_RICERCA_CLIENTE;
        $_SESSION["confermaTip"] = "%ml.cercaTip%";
        $_SESSION["titoloPagina"] = "%ml.ricercaCliente%";
    }

}