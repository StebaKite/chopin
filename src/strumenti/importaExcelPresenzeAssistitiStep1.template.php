<?php

require_once 'strumenti.abstract.class.php';
require_once 'strumenti.presentation.interface.php';
require_once 'utility.class.php';
require_once 'presenzaAssistito.class.php';

class ImportaExcelPresenzeAssistitoStep1Template extends StrumentiAbstract implements StrumentiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::IMPORTA_PRESENZE_ASSISTITO_STEP1_TEMPLATE) === NULL) {
            parent::setIndexSession(self::IMPORTA_PRESENZE_ASSISTITO_STEP1_TEMPLATE, serialize(new ImportaExcelPresenzeAssistitoStep1Template()));
        }
        return unserialize(parent::getIndexSession(self::IMPORTA_PRESENZE_ASSISTITO_STEP1_TEMPLATE));
    }

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {

        $presenzaAssistito = PresenzaAssistito::getInstance();

        $esito = TRUE;
        $msg = "<br>";

        if (parent::isEmpty($presenzaAssistito->getAnno())) {
            $msg = $msg . "<br>&ndash; Manca l'anno di riferimento";
            $esito = FALSE;
        }
        if (parent::isEmpty($presenzaAssistito->getMese())) {
            $msg = $msg . "<br>&ndash; Manca il mese di riferimento";
            $esito = FALSE;
        }
        if (parent::isEmpty($presenzaAssistito->getFile())) {
            $msg = $msg . "<br>&ndash; Manca il file";
            $esito = FALSE;
        }
        
        if ($msg != "<br>") {
            parent::setIndexSession(self::MESSAGGIO, $msg);
        } else {
            parent::unsetIndexSessione(self::MESSAGGIO);
        }

        return $esito;
    }

    public function displayPagina() {

        $presenze_da_importare = parent::EMPTYSTRING;
        $presenzaAssistito = PresenzaAssistito::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_IMPORTA_PRESENZE_ASSISTITO_STEP1;

        if (parent::isNotEmpty($presenzaAssistito->getPresenzeTrovate())) {

            $dati = array(
                "assistito" => "Assistito",
                "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8",
                "9" => "9", "10" => "10", "11" => "11", "12" => "12", "13" => "13", "14" => "14", "15" => "15", "16" => "16",
                "17" => "17", "18" => "18", "19" => "19", "20" => "20", "21" => "21", "22" => "22", "23" => "23", "24" => "24",
                "25" => "25", "26" => "26", "27" => "27", "28" => "28", "29" => "29", "30" => "30", "31" => "31",
                "totale" => "Totale"
            );
            
            $presenze_da_importare = $this->intestazionePresenzeAssistito($dati);

            foreach ($presenzaAssistito->getPresenzeTrovate() as $presenza_row) {
                $presenze_da_importare .= "<tr>";
                foreach ($presenza_row as $presenza_col) {
                    $presenze_da_importare .= "<td>" . $presenza_col . "</td>";
                }
                $presenze_da_importare .= "</tr>";
            }
            $presenze_da_importare .= "</tbody></table>";
        }

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%confermaTip%' => parent::getIndexSession(self::TIP_CONFERMA),
            '%mese%' => $presenzaAssistito->getMese(),
            '%anno%' => $presenzaAssistito->getAnno(),
            '%file%' => $presenzaAssistito->getFile(),
            '%incompleti%' => ($presenzaAssistito->getPresenzeIncomplete() > 0) ? "( Presenze incomplete = " . $presenzaAssistito->getPresenzeIncomplete() . " )" : "",
            '%selected_0%' => ($presenzaAssistito->getMese() == 0) ? "selected" : "",
            '%selected_1%' => ($presenzaAssistito->getMese() == 1) ? "selected" : "",
            '%selected_2%' => ($presenzaAssistito->getMese() == 2) ? "selected" : "",
            '%selected_3%' => ($presenzaAssistito->getMese() == 3) ? "selected" : "",
            '%selected_4%' => ($presenzaAssistito->getMese() == 4) ? "selected" : "",
            '%selected_5%' => ($presenzaAssistito->getMese() == 5) ? "selected" : "",
            '%selected_6%' => ($presenzaAssistito->getMese() == 6) ? "selected" : "",
            '%selected_7%' => ($presenzaAssistito->getMese() == 7) ? "selected" : "",
            '%selected_8%' => ($presenzaAssistito->getMese() == 8) ? "selected" : "",
            '%selected_9%' => ($presenzaAssistito->getMese() == 9) ? "selected" : "",
            '%selected_10%' => ($presenzaAssistito->getMese() == 10) ? "selected" : "",
            '%selected_11%' => ($presenzaAssistito->getMese() == 11) ? "selected" : "",
            '%villa-selected%' => ($presenzaAssistito->getCodNeg() == "VIL") ? "selected" : "",
            '%brembate-selected%' => ($presenzaAssistito->getCodNeg() == "BRE") ? "selected" : "",
            '%trezzo-selected%' => ($presenzaAssistito->getCodNeg() == "TRE") ? "selected" : "",
            '%stato-step1%' => $presenzaAssistito->getStatoStep1(),
            '%stato-step2%' => $presenzaAssistito->getStatoStep2(),
            '%stato-step3%' => $presenzaAssistito->getStatoStep3(),
            '%disabled%' => parent::getIndexSession("buttonDisabled"),
            '%presenze_da_importare%' => $presenze_da_importare,
            '%msg_errore%' => parent::getIndexSession("messaggioImportFileErr"),
            '%msg_ok%' => parent::getIndexSession("messaggioImportFileOk")
        );

        parent::setIndexSession("buttonDisabled", "");
        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }
}