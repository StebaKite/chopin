<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.presentation.interface.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class RicercaFornitoreTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::RICERCA_FORNITORE_TEMPLATE])) {
            $_SESSION[self::RICERCA_FORNITORE_TEMPLATE] = serialize(new RicercaFornitoreTemplate());
        }
        return unserialize($_SESSION[self::RICERCA_FORNITORE_TEMPLATE]);
    }

    // template ------------------------------------------------

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {

        $fornitore = Fornitore::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_RICERCA_FORNITORE;
        $risultato_ricerca = "";

        if ($fornitore->getQtaFornitori() > 0) {

            $risultato_ricerca = "<div class='row'>" .
                    "    <div class='col-sm-4'>" .
                    "        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
                    "    </div>" .
                    "    <div class='col-sm-8'>" . $_SESSION[self::MSG] . "</div>" .
                    "</div>" .
                    "<br/>" .
                    "<table class='table table-bordered table-hover'>" .
                    "	<thead>" .
                    "		<tr>" .
                    "			<th>%ml.codfornitore%</th>" .
                    "			<th>%ml.desfornitore%</th>" .
                    "			<th>%ml.desindirizzofornitore%</th>" .
                    "			<th>%ml.descittafornitore%</th>" .
                    "			<th>%ml.capfornitore%</th>" .
                    "			<th>%ml.tipaddebito%</th>" .
                    "			<th>%ml.numggscafatt%</th>" .
                    "			<th>%ml.qtareg%</th>" .
                    "			<th>&nbsp;</th>" .
                    "			<th>&nbsp;</th>" .
                    "		</tr>" .
                    "	</thead>" .
                    "	<tbody id='myTable'>";

            foreach ($fornitore->getFornitori() as $row) {

                if ($row[self::QTA_REGISTRAZIONI_FORNITORE] == 0) {
                    $bottoneModifica = self::MODIFICA_FORNITORE_HREF . trim($row['id_fornitore']) . self::MODIFICA_ICON;
                    $bottoneCancella = self::CANCELLA_FORNITORE_HREF . trim($row['id_fornitore']) . self::CANCELLA_ICON;
                } else {
                    $bottoneModifica = self::MODIFICA_FORNITORE_HREF . trim($row['id_fornitore']) . self::MODIFICA_ICON;
                    $bottoneCancella = "&nbsp;";
                }

                $risultato_ricerca .= "<tr>" .
                        "	<td>" . trim($row[$fornitore::COD_FORNITORE]) . "</td>" .
                        "	<td>" . trim($row[$fornitore::DES_FORNITORE]) . "</td>" .
                        "	<td>" . trim($row[$fornitore::DES_INDIRIZZO_FORNITORE]) . "</td>" .
                        "	<td>" . trim($row[$fornitore::DES_CITTA_FORNITORE]) . "</td>" .
                        "	<td>" . trim($row[$fornitore::CAP_FORNITORE]) . "</td>" .
                        "	<td>" . trim($row[$fornitore::TIP_ADDEBITO]) . "</td>" .
                        "	<td>" . trim($row[$fornitore::NUM_GG_SCADENZA_FATTURA]) . "</td>" .
                        "	<td>" . trim($row[$fornitore::QTA_REGISTRAZIONI_FORNITORE]) . "</td>" .
                        "	<td>" . $bottoneModifica . "</td>" .
                        "	<td>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $risultato_ricerca .= "</tbody></table>";
        }

        $fornitore->prepara();
        $_SESSION[self::FORNITORE] = serialize($fornitore);

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%codfornitore%' => $fornitore->getCodFornitore(),
            '%desfornitore%' => $fornitore->getDesFornitore(),
            '%indfornitore%' => $fornitore->getDesIndirizzoFornitore(),
            '%cittafornitore%' => $fornitore->getDesCittaFornitore(),
            '%capfornitore%' => $fornitore->getCapFornitore(),
            '%tipoaddebito%' => $fornitore->getTipAddebito(),
            '%risultato_ricerca%' => $risultato_ricerca
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}