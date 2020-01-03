<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cambiaContoStep1.template.php';
require_once 'strumenti.business.interface.php';

class CambiaContoStep1 extends StrumentiAbstract implements StrumentiBusinessInterface {

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
        if (parent::getIndexSession(self::CAMBIA_CONTO_STEP1) === NULL) {
            parent::setIndexSession(self::CAMBIA_CONTO_STEP1, serialize(new CambiaContoStep1()));
        }
        return unserialize(parent::getIndexSession(self::CAMBIA_CONTO_STEP1));
    }

    public function start() {

        $registrazione = Registrazione::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $registrazione->preparaFiltri();

        $cambiaContoStep1Template = CambiaContoStep1Template::getInstance();
        $this->preparaPagina($cambiaContoStep1Template);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
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

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($cambiaContoStep1Template->controlliLogici()) {

            if ($registrazione->leggiRegistrazioniConto($db)) {

                if (parent::getIndexSession("messaggioCambioConto") !== NULL) {
                    parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession("messaggioCambioConto") . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni");
                    parent::unsetIndexSessione("messaggioCambioConto");
                } else {
                    parent::setIndexSession(self::MESSAGGIO, "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni");
                }    
                self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));

                if ($registrazione->getQtaRegistrazioni() > 0) {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                } else {
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                }
                parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
            } else {
                parent::setIndexSession(self::MESSAGGIO, "Errore fatale durante la lettura delle registrazioni");
                self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
                $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
                parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
            }
        } else {
            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        }

        $this->preparaPagina($cambiaContoStep1Template);
        $cambiaContoStep1Template->displayPagina();
        include($this->piede);
    }

    public function preparaPagina($ricercaRegistrazioneTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_REGISTRAZIONE);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.confermaRicercaRegistrazione%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.cambioContoStep1%");
    }
}