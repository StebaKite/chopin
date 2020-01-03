<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.business.interface.php';
require_once 'ricercaFornitore.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fornitore.class.php';

class RicercaFornitore extends AnagraficaAbstract implements AnagraficaBusinessInterface {

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

        if (parent::getIndexSession(self::RICERCA_FORNITORE) === NULL) {
            parent::setIndexSession(self::RICERCA_FORNITORE, serialize(new RicercaFornitore()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_FORNITORE));
    }

    public function start() {

        $fornitore = Fornitore::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $ricercaFornitoreTemplate = RicercaFornitoreTemplate::getInstance();

        $this->preparaPagina($ricercaFornitoreTemplate);

        $replace = parent::getIndexSession(self::AMBIENTE) ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($this->refreshFornitori($db, $fornitore)) {

            /**
             * Gestione del messaggio proveniente dalla cancellazione
             */
            if (parent::getIndexSession(self::MSG_DA_CANCELLAZIONE) !== NULL) {
                parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_CANCELLAZIONE) . "<br>" . "Trovati " . $fornitore->getQtaFornitori() . " fornitori");
                parent::unsetIndexSessione(self::MSG_DA_CANCELLAZIONE);
            } elseif (parent::getIndexSession (self::MSG_DA_CREAZIONE) !== NULL) {
                parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_CREAZIONE) . "<br>" . "Trovati " . $fornitore->getQtaFornitori() . " fornitori");
                parent::unsetIndexSessione(self::MSG_DA_CREAZIONE);
            } elseif (parent::getIndexSession(self::MSG_DA_MODIFICA) !== NULL) {
                parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_MODIFICA) . "<br>" . "Trovati " . $fornitore->getQtaFornitori() . " fornitori");
                parent::unsetIndexSessione(self::MSG_DA_MODIFICA);
            } else {
                parent::setIndexSession(self::MESSAGGIO, "Trovati " . $fornitore->getQtaFornitori() . " fornitori");
            }

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));

            $pos = strpos(parent::getIndexSession(self::MESSAGGIO), "ERRORE");
            if ($pos === false) {
                if ($fornitore->getQtaFornitori() > 0) {
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
        $ricercaFornitoreTemplate->displayPagina();

        include($this->piede);
    }

    public function go() {
        $this->start();
    }

    private function refreshFornitori($db, $fornitore) {

        if ($fornitore->getQtaFornitori() == 0) {

            if (!$fornitore->load($db)) {
                parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);
                return false;
            }
            parent::setIndexSession(self::FORNITORE, serialize($fornitore));
        }
        return true;
    }

    private function preparaPagina() {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_FORNITORE);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.cercaTip%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaFornitore%");
    }

}