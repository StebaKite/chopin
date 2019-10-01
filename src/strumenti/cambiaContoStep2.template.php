<?php

require_once 'strumenti.abstract.class.php';
require_once 'strumenti.presentation.interface.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class CambiaContoStep2Template extends StrumentiAbstract implements StrumentiPresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CAMBIA_CONTO_STEP2_TEMPLATE]))
            $_SESSION[self::CAMBIA_CONTO_STEP2_TEMPLATE] = serialize(new CambiaContoStep2Template());
        return unserialize($_SESSION[self::CAMBIA_CONTO_STEP2_TEMPLATE]);
    }

    public function inizializzaPagina() { }

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

        $form = $this->root . $array['template'] . self::PAGINA_CAMBIO_CONTO_STEP2;

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
            '%datareg_da%' => $registrazione->getDatRegistrazioneDa(),
            '%datareg_a%' => $registrazione->getDatRegistrazioneA(),
            '%elenco_conti%' => $conto->preparaElencoConti(),            
            '%numRegSel%' => $registrazione->getQtaRegistrazioni(),
            '%contoOrig%' => $registrazione->getCodContoSel(),
            '%conto_sel%' => $conto->getCodContoSel(),
            '%codneg_sel%' => $conto->getCodContoSel(),
        );
        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }
}

?>