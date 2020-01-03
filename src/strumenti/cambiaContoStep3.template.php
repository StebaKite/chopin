<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep3Template extends StrumentiAbstract implements StrumentiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CAMBIA_CONTO_STEP3_TEMPLATE) === NULL) {
            parent::setIndexSession(self::CAMBIA_CONTO_STEP3_TEMPLATE, serialize(new CambiaContoStep3Template()));
        }
        return unserialize(parent::getIndexSession(self::CAMBIA_CONTO_STEP3_TEMPLATE));
    }

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {

        $esito = TRUE;
        $msg = "<br>";

        if ($msg != "<br>") {
            parent::setIndexSession(self::MESSAGGIO, $msg);
        } else {
            parent::unsetIndexSessione(self::MESSAGGIO);
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
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%confermaTip%' => parent::getIndexSession(self::TIP_CONFERMA),
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