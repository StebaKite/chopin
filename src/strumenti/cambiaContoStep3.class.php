<?php

require_once 'strumenti.abstract.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cambiaContoStep3.template.php';
require_once 'strumenti.business.interface.php';
require_once 'cambiaContoStep1.class.php';

class CambiaContoStep3 extends StrumentiAbstract implements StrumentiBusinessInterface {

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
        if (!isset($_SESSION[self::CAMBIA_CONTO_STEP3]))
            $_SESSION[self::CAMBIA_CONTO_STEP3] = serialize(new CambiaContoStep3());
        return unserialize($_SESSION[self::CAMBIA_CONTO_STEP3]);
    }

    public function start() {

        $registrazione = Registrazione::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $registrazione->preparaFiltri();

        $cambiaContoStep3Template = CambiaContoStep3Template::getInstance();
        $this->preparaPagina($cambiaContoStep3Template);

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $cambiaContoStep3Template->displayPagina();
        include($this->piede);
    }

    public function go() {

        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $conto = Conto::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        $db = Database::getInstance();
        $db->beginTransaction();
        $cambiaContoStep3Template = CambiaContoStep3Template::getInstance();

        if ($this->spostaDettagliRegistrazioni($db, $registrazione, $dettaglioRegistrazione, $conto->getCodContoSelNuovo())) {

            /**
             * Rigenero i saldi a partire dal mese successivo a quello aggiornato dallo spostamento sino all'ultimo 
             * già eseguito
             */
            $array = $utility->getConfig();

            if ($array['lavoriPianificatiAttivati'] == "Si") {
                $datareg_da = strtotime(str_replace('/', '-', $_SESSION["datareg_da"]));
                $this->ricalcolaSaldi($db, $datareg_da);
            }
            $db->commitTransaction();
            $_SESSION["messaggioCambioConto"] = "Operazione effettuata con successo";
            $cambiaContoStep1 = CambiaContoStep1::getInstance();
            $cambiaContoStep1->go();
            
        } else {

            $db->rollbackTransaction();
            $this->preparaPagina($cambiaContoStep3Template);
            
            $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
            $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
            echo $utility->tailTemplate($template);

            $_SESSION[self::MESSAGGIO] = "Errore fatale durante lo spostamento dei dettagli";

            $replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), $replace);
            echo $utility->tailTemplate($template);

            $cambiaContoStep3Template->displayPagina();
            include($this->piede);
        }
    }

    protected function spostaDettagliRegistrazioni($db, $registrazione, $dettaglioRegistrazione, $contoSelNuovo) {

        foreach ($registrazione->getRegistrazioni() as $row) {
            $dettaglioRegistrazione->setIdRegistrazione($row['id_dettaglio_registrazione']);
            $dettaglioRegistrazione->setCodConto(explode(".", $contoSelNuovo)[0]);
            $dettaglioRegistrazione->setCodSottoconto(explode(".", $contoSelNuovo)[1]);
            if (!$dettaglioRegistrazione->aggiornaConto($db)) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public function preparaPagina($ricercaRegistrazioneTemplate) {

        $_SESSION[self::AZIONE] = self::AZIONE_CAMBIA_CONTO_STEP3;
        $_SESSION[self::TITOLO_PAGINA] = "%ml.cambioContoStep3%";
    }
}

?>