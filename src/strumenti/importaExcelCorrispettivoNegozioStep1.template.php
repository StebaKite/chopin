<?php

require_once 'strumenti.abstract.class.php';

class ImportaExcelCorrispettivoNegozioStep1Template extends StrumentiAbstract {

    private static $_instance = null;
    private static $pagina = "/primanota/importaExcelCorrispettivoNegozio.form.html";

    //-----------------------------------------------------------------------------

    function __construct() {
        self::$root = $_SERVER['DOCUMENT_ROOT'];
    }

    private function __clone() {

    }

    /**
     * Singleton Pattern
     */
    public static function getInstance() {

        if (!is_object(self::$_instance))
            self::$_instance = new ImportaExcelCorrispettivoNegozioStep1Template();

        return self::$_instance;
    }

    // template ------------------------------------------------

    public function inizializzaPagina() {

    }

    public function controlliLogici() {

        $esito = TRUE;
        $msg = "<br>";

        if ($_SESSION["anno"] == "") {
            $msg = $msg . "<br>&ndash; Manca l'anno di riferimento";
            $esito = FALSE;
        }
        if ($_SESSION["file"] == "") {
            $msg = $msg . "<br>&ndash; Manca il file";
            $esito = FALSE;
        }

        // ----------------------------------------------

        if ($msg != "<br>") {
            $_SESSION["messaggio"] = $msg;
        } else {
            unset($_SESSION["messaggio"]);
        }

        return $esito;
    }

    public function displayPagina() {

        require_once 'utility.class.php';

        // Template --------------------------------------------------------------

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = self::$root . $array['template'] . self::$pagina;

        if (isset($_SESSION["corrispettiviTrovati"])) {

            $corrispettivi_da_importare = "<table id='corrispettivi' class='display' width='100%'>" .
                    "	<thead>" .
                    "		<tr>" .
                    "			<th>Data</th>" .
                    "			<th>Totale</th>" .
                    "			<th>REP1 10%</th>" .
                    "			<th>REP2 22%</th>" .
                    "		</tr>" .
                    "	</thead>" .
                    "	<tbody>";

            $corrispettiviTrovati = $_SESSION["corrispettiviTrovati"];

            foreach ($corrispettiviTrovati as $corrispettivo_row) {
                $corrispettivi_da_importare .= "<tr>";
                foreach ($corrispettivo_row as $corrispettivo_col) {
                    $corrispettivi_da_importare .= "<td>" . $corrispettivo_col . "</td>";
                }
                $corrispettivi_da_importare .= "</tr>";
            }
            $corrispettivi_da_importare .= "</tbody></table>";
        }


        $replace = array(
            '%titoloPagina%' => $this->getTitoloPagina(),
            '%azione%' => $this->getAzione(),
            '%confermaTip%' => $this->getConfermaTip(),
            '%conferma%' => $this->getConfermaTip(),
            '%mese%' => $_SESSION["mese"],
            '%anno%' => $_SESSION["anno"],
            '%file%' => $_SESSION["file"],
            '%datada%' => $_SESSION["datada"],
            '%dataa%' => $_SESSION["dataa"],
            '%incompleti%' => ($_SESSION["corrispettiviIncompleti"] > 0) ? "( Corrispettivi incompleti = " . $_SESSION["corrispettiviIncompleti"] . " )" : "",
            '%selected_0%' => ($_SESSION["mese"] == 0) ? "selected" : "",
            '%selected_1%' => ($_SESSION["mese"] == 1) ? "selected" : "",
            '%selected_2%' => ($_SESSION["mese"] == 2) ? "selected" : "",
            '%selected_3%' => ($_SESSION["mese"] == 3) ? "selected" : "",
            '%selected_4%' => ($_SESSION["mese"] == 4) ? "selected" : "",
            '%selected_5%' => ($_SESSION["mese"] == 5) ? "selected" : "",
            '%selected_6%' => ($_SESSION["mese"] == 6) ? "selected" : "",
            '%selected_7%' => ($_SESSION["mese"] == 7) ? "selected" : "",
            '%selected_8%' => ($_SESSION["mese"] == 8) ? "selected" : "",
            '%selected_9%' => ($_SESSION["mese"] == 9) ? "selected" : "",
            '%selected_10%' => ($_SESSION["mese"] == 10) ? "selected" : "",
            '%selected_11%' => ($_SESSION["mese"] == 11) ? "selected" : "",
            '%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
            '%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
            '%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
            '%cassa-checked%' => ($_SESSION["contocassa"] == "S") ? "checked" : "",
            '%banca-checked%' => ($_SESSION["contocassa"] == "N") ? "checked" : "",
            '%esitoMese%' => $_SESSION["esitoMese"],
            '%esitoAnno%' => $_SESSION["esitoAnno"],
            '%esitoNegozio%' => $_SESSION["esitoNegozio"],
            '%esitoFile%' => $_SESSION["esitoFile"],
            '%corrispettivi_da_importare%' => $corrispettivi_da_importare
        );

        $utility = Utility::getInstance();

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}

?>