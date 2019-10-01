<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class RicercaContoTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::RICERCA_CONTO_TEMPLATE])) {
            $_SESSION[self::RICERCA_CONTO_TEMPLATE] = serialize(new RicercaContoTemplate());
        }
        return unserialize($_SESSION[self::RICERCA_CONTO_TEMPLATE]);
    }

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {

        // Template --------------------------------------------------------------

        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_RICERCA_CONTO;
        $risultato_ricerca = "";

        if ($conto->getQtaConti() > 0) {

            $risultato_ricerca = "<div class='row'>" .
                    "    <div class='col-sm-4'>" .
                    "        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
                    "    </div>" .
                    "    <div class='col-sm-8'>" . $_SESSION[self::MSG] . "</div>" .
                    "</div>" .
                    "<br/>" .
                    "<table class='table table-bordered table-hover'>" .
                    "	<thead>" .
                    "   	<tr>" .
                    "			<th>%ml.conto%</th>" .
                    "			<th>%ml.desconto%</th>" .
                    "			<th>%ml.catconto%</th>" .
                    "			<th>%ml.tipconto%</th>" .
                    "			<th></th>" .
                    "			<th></th>" .
                    "			<th></th>" .
                    "		</tr>" .
                    "	</thead>" .
                    "	<tbody id='myTable'>";

            foreach ($conto->getConti() as $row) {
                $bottoneVisualizza = self::VISUALIZZA_CONTO_HREF . trim($row[$conto::COD_CONTO]) . self::VISUALIZZA_ICON;
                $bottoneModifica = self::MODIFICA_CONTO_HREF . trim($row[$conto::COD_CONTO]) . self::MODIFICA_ICON;
                $bottoneCancella = "&nbsp;";

                if ($row[self::NUM_REG_CONTO] == 0) {
                    $bottoneCancella = self::CANCELLA_CONTO_HREF . trim($row[$conto::COD_CONTO]) . self::CANCELLA_ICON;
                }

                $risultato_ricerca .= "<tr>" .
                        "	<td>" . trim($row[$conto::COD_CONTO]) . "</td>" .
                        "	<td>" . trim($row[$conto::DES_CONTO]) . "</td>" .
                        "	<td>" . trim($row[$conto::CAT_CONTO]) . "</td>" .
                        "	<td>" . trim($row[$conto::TIP_CONTO]) . "</td>" .
                        "	<td>" . $bottoneVisualizza . "</td>" .
                        "	<td>" . $bottoneModifica . "</td>" .
                        "	<td>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $risultato_ricerca .= "</tbody></table>";
        }

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO],
            '%azione%' => $_SESSION[self::AZIONE],
            '%conto-economico-selected%' => ($conto->getCatContoSel() == "Conto Economico") ? "selected" : "",
            '%stato-patrimoniale-selected%' => ($conto->getCatContoSel() == "Stato Patrimoniale") ? "selected" : "",
            '%dare-selected%' => ($conto->getTipContoSel() == "Dare") ? "selected" : "",
            '%avere-selected%' => ($conto->getTipContoSel() == "Avere") ? "selected" : "",
            '%risultato_ricerca%' => $risultato_ricerca
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}