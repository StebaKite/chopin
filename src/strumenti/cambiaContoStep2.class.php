<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cambiaContoStep2.template.php';
require_once 'strumenti.business.interface.php';

class CambiaContoStep2 extends StrumentiAbstract implements StrumentiBusinessInterface {

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
        if (parent::getIndexSession(self::CAMBIA_CONTO_STEP2) === NULL) {
            parent::setIndexSession(self::CAMBIA_CONTO_STEP2, serialize(new CambiaContoStep2()));
        }
        return unserialize(parent::getIndexSession(self::CAMBIA_CONTO_STEP2));
    }

    public function start() {

        $registrazione = Registrazione::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $registrazione->preparaFiltri();

        $cambiaContoStep2Template = CambiaContoStep2Template::getInstance();
        $this->preparaPagina($cambiaContoStep2Template);

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $cambiaContoStep2Template->displayPagina();
        include($this->piede);
    }

    public function go() {
        
    }

    public function preparaPagina($ricercaRegistrazioneTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_CAMBIA_CONTO_STEP2);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.confermaContoDestinazione%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.cambioContoStep2%");
    }
}