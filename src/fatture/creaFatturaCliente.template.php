<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fattura.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';

class CreaFatturaClienteTemplate extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::CREA_FATTURA_CLIENTE_TEMPLATE]))
            $_SESSION[self::CREA_FATTURA_CLIENTE_TEMPLATE] = serialize(new CreaFatturaClienteTemplate());
        return unserialize($_SESSION[self::CREA_FATTURA_CLIENTE_TEMPLATE]);
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {

    }

    public function displayPagina() {

        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fattura = Fattura::getInstance();
        $cliente = Cliente::getInstance();

        $cliente->load($db);

        $form = $this->root . $array['template'] . self::PAGINA_CREA_FATTURA_CLIENTE;

//        if ($_SESSION['dettagliInseriti'] != "") {
//
//            $thead_dettagli = "<tr>" .
//                    "<th class='dt-center'>Quantit&agrave;</th>" .
//                    "<th>Articolo</th>" .
//                    "<th class='dt-right'>Importo</th>" .
//                    "<th class='dt-right'>Totale</th>" .
//                    "<th class='dt-right'>Imponibile</th>" .
//                    "<th class='dt-right'>Iva</th>" .
//                    "<th class='dt-right'>% Aliq</th>" .
//                    "<th>&nbsp;</th>" .
//                    "</tr>";
//
//            $tbody_dettagli = "";
//            $d_x_array = "";
//
//            $d = explode(",", $_SESSION['dettagliInseriti']);
//
//            foreach ($d as $ele) {
//
//                $e = explode("#", $ele);
//                $id = $e[0];
//
//                $dettaglio = "<tr id='" . trim($id) . "'>" .
//                        "<td class='dt-center'>" . $e[1] . "</td>" .
//                        "<td>" . $e[2] . "</td>" .
//                        "<td class='dt-right'>" . number_format($e[3], 2, ',', '.') . "</td>" .
//                        "<td class='dt-right'>" . number_format($e[4], 2, ',', '.') . "</td>" .
//                        "<td class='dt-right'>" . number_format($e[5], 2, ',', '.') . "</td>" .
//                        "<td class='dt-right'>" . number_format($e[6], 2, ',', '.') . "</td>" .
//                        "<td class='dt-right'>" . number_format($e[7]) . "</td>" .
//                        "<td id='icons'><a class='tooltip' onclick='cancellaDettaglioFattura(" . trim($id) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
//                        "</tr>";
//
//                $tbody_dettagli = $tbody_dettagli . $dettaglio;
//
//                /**
//                 * Prepara la valorizzazione dell'array di pagina per i dettagli inseriti
//                 */
//                $d_x_array = $d_x_array . "'" . $ele . "',";
//            }
//        }

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
            '%titolo%' => $_SESSION[$fattura->getDesTitolo()],
            '%numfat%' => $fattura->getNumFattura(),
            '%datafat%' => $fattura->getDatFattura(),
            '%tipoadd%' => $fattura->getTipAddebito(),
            '%ragsocbanca%' => str_replace("'", "&apos;", $fattura->getDesRagsocBanca()),
            '%ibanbanca%' => $fattura->getCodIbanBanca(),
            '%descli%' => $fattura->getDesCliente(),
            '%contributo-checked%' => ($fattura->getTipFattura() == self::CONTRIBUTO) ? self::CHECK_THIS_ITEM : "",
            '%vendita-checked%' => ($fattura->getTipFattura() == self::VENDITA) ? self::CHECK_THIS_ITEM : "",
            '%assistito%' => $fattura->getAssistito(),
            '%villa-checked%' => ($fattura->getCodNegozio() == self::VILLA) ? self::CHECK_THIS_ITEM : "",
            '%brembate-checked%' => ($fattura->getCodNegozio() == self::BREMBATE) ? self::CHECK_THIS_ITEM : "",
            '%trezzo-checked%' => ($fattura->getCodNegozio() == self::TREZZO) ? self::CHECK_THIS_ITEM : "",
            '%elenco_clienti%' => $this->caricaElencoClienti($cliente)

//            '%thead_dettagli%' => $thead_dettagli,
//            '%tbody_dettagli%' => $tbody_dettagli,
//            '%dettagliInseriti%' => $_SESSION["dettagliInseriti"],
//            '%arrayDettagliInseriti%' => $d_x_array,
//            '%arrayIndexDettagliInseriti%' => $_SESSION["indexDettagliInseriti"],
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {

    }

    public function start() {

    }

}

?>