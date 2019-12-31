<?php

require_once 'primanota.abstract.class.php';

class VisualizzaCorrispettivoNegozioTemplate extends PrimanotaAbstract {

    private static $_instance = null;
    private static $pagina = "/primanota/visualizzaCorrispettivoNegozio.form.html";

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

        if (!is_object(self::$_instance)) {
            self::$_instance = new VisualizzaCorrispettivoNegozioTemplate();
        }

        return self::$_instance;
    }

    // template ------------------------------------------------

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {

        require_once 'utility.class.php';

        // Template --------------------------------------------------------------

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = self::$root . $array['template'] . self::$pagina;

        /**
         * Prepara la tabella dei dettagli della registrazione da iniettare in pagina
         */
        $result = $_SESSION["elencoDettagliRegistrazione"];

        $dettaglioregistrazione = pg_fetch_all($result);
        $tbodyDettagli = "";

        foreach ($dettaglioregistrazione as $row) {

            $tbodyDettagli = $tbodyDettagli .
                    "<tr>" .
                    "<td>" . $row["cod_conto"] . $row["cod_sottoconto"] . " - " . $row["des_sottoconto"] . "</td>" .
                    "<td class='dt-right'>" . number_format(trim($row["imp_registrazione"]), 2, ',', '.') . "</td>" .
                    "<td class='dt-center'>" . $row["ind_dareavere"] . "</td>" .
                    "</tr>";
        }

        $replace = array(
            '%titoloPagina%' => $this->getTitoloPagina(),
            '%referer%' => $_SERVER["HTTP_REFERER"],
            '%datascad_da%' => $_SESSION["datascad_da"],
            '%datascad_a%' => $_SESSION["datascad_a"],
            '%confermaTip%' => $this->getConfermaTip(),
            '%idregistrazione%' => $_SESSION["idRegistrazione"],
            '%descreg%' => str_replace("'", "&apos;", $_SESSION["descreg"]),
            '%datareg%' => $_SESSION["datareg"],
            '%codneg_sel%' => $_SESSION["codneg_sel"],
            '%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
            '%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
            '%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
            '%datareg_da%' => $_SESSION["datareg_da"],
            '%datareg_a%' => $_SESSION["datareg_a"],
            '%elenco_causali%' => $_SESSION["elenco_causali"],
            '%tbody_dettagli%' => $tbodyDettagli,
        );

        $utility = Utility::getInstance();

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}