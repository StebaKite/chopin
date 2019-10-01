<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.presentation.interface.php';
require_once "categoriaCliente.class.php";
require_once 'utility.class.php';
require_once 'cliente.class.php';

class RicercaClienteTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {

        if (!isset($_SESSION[self::RICERCA_CLIENTE_TEMPLATE])) {
            $_SESSION[self::RICERCA_CLIENTE_TEMPLATE] = serialize(new RicercaClienteTemplate());
        }
        return unserialize($_SESSION[self::RICERCA_CLIENTE_TEMPLATE]);
    }

    // template ------------------------------------------------

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {

        // Template --------------------------------------------------------------

        $cliente = Cliente::getInstance();
        $categoriaCliente = CategoriaCliente::getInstance();
        $categoriaCliente->load();

        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_RICERCA_CLIENTE;
        $risultato_ricerca = "";

        if ($cliente->getQtaClienti() > 0) {

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
                    "			<th>%ml.codcliente%</th>" .
                    "			<th>%ml.descliente%</th>" .
                    "			<th>%ml.desindirizzocliente%</th>" .
                    "			<th>%ml.descittacliente%</th>" .
                    "			<th>%ml.capcliente%</th>" .
                    "			<th>%ml.tipaddebito%</th>" .
                    "			<th>%ml.qtareg%</th>" .
                    "			<th>&nbsp;</th>" .
                    "			<th>&nbsp;</th>" .
                    "		</tr>" .
                    "	</thead>" .
                    "	<tbody id='myTable'>";

            foreach ($cliente->getClienti() as $row) {

                if ($row[$cliente::QTA_REGISTRAZIONI_CLIENTE] == 0) {
                    $bottoneModifica = self::MODIFICA_CLIENTE_HREF . trim($row['id_cliente']) . self::MODIFICA_ICON;
                    $bottoneCancella = self::CANCELLA_CLIENTE_HREF . trim($row['id_cliente']) . self::CANCELLA_ICON;
                } else {
                    $bottoneModifica = self::MODIFICA_CLIENTE_HREF . trim($row['id_cliente']) . self::MODIFICA_ICON;
                    $bottoneCancella = "&nbsp;";
                }

                $risultato_ricerca .= "<tr>" .
                        "	<td>" . trim($row[$cliente::COD_CLIENTE]) . "</td>" .
                        "	<td>" . trim($row[$cliente::DES_CLIENTE]) . "</td>" .
                        "	<td>" . trim($row[$cliente::DES_INDIRIZZO_CLIENTE]) . "</td>" .
                        "	<td>" . trim($row[$cliente::DES_CITTA_CLIENTE]) . "</td>" .
                        "	<td>" . trim($row[$cliente::CAP_CLIENTE]) . "</td>" .
                        "	<td>" . trim($row[$cliente::TIP_ADDEBITO]) . "</td>" .
                        "	<td>" . trim($row[$cliente::QTA_REGISTRAZIONI_CLIENTE]) . "</td>" .
                        "	<td>" . $bottoneModifica . "</td>" .
                        "	<td>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $risultato_ricerca .= "</tbody></table>";
        }

        $cliente->prepara();
        $_SESSION[self::CLIENTE] = serialize($cliente);

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO],
            '%azione%' => $_SESSION[self::AZIONE_RICERCA_CLIENTE],
            '%elenco_categorie_cliente%' => $categoriaCliente->getElencoCategorieCliente(),
            '%codcliente%' => $cliente->getCodCliente(),
            '%descliente%' => $cliente->getDesCliente(),
            '%indcliente%' => $cliente->getDesIndirizzoCliente(),
            '%cittacliente%' => $cliente->getDesCittaCliente(),
            '%capcliente%' => $cliente->getCapCliente(),
            '%tipoaddebito%' => $cliente->getTipAddebito(),
            '%codpiva%' => $cliente->getCodPiva(),
            '%codfisc%' => $cliente->getCodFisc(),
            '%catcliente%' => $cliente->getCatCliente(),
            '%risultato_ricerca%' => $risultato_ricerca
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}