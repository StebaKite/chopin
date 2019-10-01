<?php

require_once 'strumenti.abstract.class.php';
require_once 'strumenti.presentation.interface.php';
require_once 'utility.class.php';
require_once 'corrispettivo.class.php';

class ImportaExcelCorrispettiviNegozioStep1Template extends StrumentiAbstract implements StrumentiPresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1_TEMPLATE]))
            $_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1_TEMPLATE] = serialize(new ImportaExcelCorrispettiviNegozioStep1Template());
        return unserialize($_SESSION[self::IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1_TEMPLATE]);
    }

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {

        $corrispettivo = Corrispettivo::getInstance();

        $esito = TRUE;
        $msg = "<br>";

        if (parent::isEmpty($corrispettivo->getAnno())) {
            $msg = $msg . "<br>&ndash; Manca l'anno di riferimento";
            $esito = FALSE;
        }
        if (parent::isEmpty($corrispettivo->getMese())) {
            $msg = $msg . "<br>&ndash; Manca il mese di riferimento";
            $esito = FALSE;
        }
        if (parent::isEmpty($corrispettivo->getFile())) {
            $msg = $msg . "<br>&ndash; Manca il file";
            $esito = FALSE;
        }
        
        if ($msg != "<br>") {
            $_SESSION["messaggio"] = $msg;
        } else {
            unset($_SESSION["messaggio"]);
        }

        return $esito;
    }

    public function displayPagina() {

        $corrispettivo = Corrispettivo::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_IMPORTA_CORRISPETTIVI_NEGOZIO_STEP1;

        if (parent::isNotEmpty($corrispettivo->getCorrispettiviTrovati())) {

            $dati = array(
                "labeldata" => "Data",
                "labeltotale" => "Totale",
                "labelrep1" => "REP1 10%",
                "labelrep2" => "REP2 22%"
            );
            
            $corrispettivi_da_importare = $this->intestazioneCorrispettiviNegozio($dati);

            foreach ($corrispettivo->getCorrispettiviTrovati() as $corrispettivo_row) {
                $corrispettivi_da_importare .= "<tr>";
                foreach ($corrispettivo_row as $corrispettivo_col) {
                    $corrispettivi_da_importare .= "<td>" . $corrispettivo_col . "</td>";
                }
                $corrispettivi_da_importare .= "</tr>";
            }
            $corrispettivi_da_importare .= "</tbody></table>";
        }

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],            
            '%mese%' => $corrispettivo->getMese(),
            '%anno%' => $corrispettivo->getAnno(),
            '%file%' => $corrispettivo->getFile(),
            '%datada%' => $corrispettivo->getDatada(),
            '%dataa%' => $corrispettivo->getDataa(),
            '%incompleti%' => ($corrispettivo->getCorrispettiviIncompleti() > 0) ? "( Corrispettivi incompleti = " . $corrispettivo->getCorrispettiviIncompleti() . " )" : "",
            '%selected_0%' => ($corrispettivo->getMese() == 0) ? "selected" : "",
            '%selected_1%' => ($corrispettivo->getMese() == 1) ? "selected" : "",
            '%selected_2%' => ($corrispettivo->getMese() == 2) ? "selected" : "",
            '%selected_3%' => ($corrispettivo->getMese() == 3) ? "selected" : "",
            '%selected_4%' => ($corrispettivo->getMese() == 4) ? "selected" : "",
            '%selected_5%' => ($corrispettivo->getMese() == 5) ? "selected" : "",
            '%selected_6%' => ($corrispettivo->getMese() == 6) ? "selected" : "",
            '%selected_7%' => ($corrispettivo->getMese() == 7) ? "selected" : "",
            '%selected_8%' => ($corrispettivo->getMese() == 8) ? "selected" : "",
            '%selected_9%' => ($corrispettivo->getMese() == 9) ? "selected" : "",
            '%selected_10%' => ($corrispettivo->getMese() == 10) ? "selected" : "",
            '%selected_11%' => ($corrispettivo->getMese() == 11) ? "selected" : "",
            '%villa-selected%' => ($corrispettivo->getCodNeg() == "VIL") ? "selected" : "",
            '%brembate-selected%' => ($corrispettivo->getCodNeg() == "BRE") ? "selected" : "",
            '%trezzo-selected%' => ($corrispettivo->getCodNeg() == "TRE") ? "selected" : "",
            '%cassa-checked%' => ($corrispettivo->getContoCassa() == "S") ? "checked" : "",
            '%banca-checked%' => ($corrispettivo->getContoCassa() == "N") ? "checked" : "",
            '%stato-step1%' => $corrispettivo->getStatoStep1(),
            '%stato-step2%' => $corrispettivo->getStatoStep2(),
            '%stato-step3%' => $corrispettivo->getStatoStep3(),
//            '%esitoMese%' => $_SESSION["esitoMese"],
//            '%esitoAnno%' => $_SESSION["esitoAnno"],
//            '%esitoNegozio%' => $_SESSION["esitoNegozio"],
//            '%esitoFile%' => $_SESSION["esitoFile"],
            '%disabled%' => $_SESSION["buttonDisabled"],
            '%corrispettivi_da_importare%' => $corrispettivi_da_importare,
            '%msg_errore%' => $_SESSION["messaggioImportFileErr"],
            '%msg_ok%' => $_SESSION["messaggioImportFileOk"]    
        );

        $_SESSION["buttonDisabled"] = "";
        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }
}