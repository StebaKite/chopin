<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaCliente.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';

class RicercaCliente extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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
        if (parent::getIndexSession(self::RICERCA_CLIENTE) === NULL) {
            parent::setIndexSession(self::RICERCA_CLIENTE, serialize(new RicercaCliente()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_CLIENTE));
    }

    public function start() {

        // Template

        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $ricercaClienteTemplate = RicercaClienteTemplate::getInstance();

        $this->preparaPagina($ricercaClienteTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->refreshClienti($db, $cliente)) {

            if (parent::getIndexSession(self::MSG_DA_CANCELLAZIONE) !== NULL) {
                parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_CANCELLAZIONE) . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti");
                parent::unsetIndexSessione(self::MSG_DA_CANCELLAZIONE);
            } elseif (parent::getIndexSession(self::MSG_DA_CREAZIONE) !== NULL) {
                parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_CREAZIONE) . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti");
                parent::unsetIndexSessione(self::MSG_DA_CREAZIONE);
            } elseif (parent::getIndexSession (self::MSG_DA_MODIFICA) !== NULL) {
                parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_MODIFICA) . "<br>" . "Trovati " . $cliente->getQtaClienti() . " clienti");
                parent::unsetIndexSessione(self::MSG_DA_MODIFICA);
            } else {
                parent::setIndexSession(self::MESSAGGIO, "Trovati " . $cliente->getQtaClienti() . " clienti");
            }

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));

            $pos = strpos(parent::getIndexSession(self::MESSAGGIO), "ERRORE");
            if ($pos === false) {
                if ($cliente->getQtaClienti() > 0) {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                } else {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                }
            } else {
                $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            }

            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        }
        else {

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        }
        $ricercaClienteTemplate->displayPagina();

        include($this->piede);
    }

    public function go() {
        $this->start();
    }

    private function refreshClienti($db, $cliente) {

        if ($cliente->getQtaClienti() == 0) {

            if (!$cliente->load($db)) {
                parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);
                return false;
            }
            parent::setIndexSession(self::CLIENTE, serialize($cliente));
        }
        return true;
    }

    private function preparaPagina($ricercaCausaleTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_CLIENTE);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.cercaTip%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaCliente%");
    }

}