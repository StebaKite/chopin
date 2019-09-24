<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.presentation.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';
require_once 'registrazione.class.php';
require_once 'causale.class.php';

class RicercaScadenzeClienteTemplate extends ScadenzeAbstract implements ScadenzePresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::RICERCA_SCADENZE_CLIENTE_TEMPLATE]))
            $_SESSION[self::RICERCA_SCADENZE_CLIENTE_TEMPLATE] = serialize(new RicercaScadenzeClienteTemplate());
        return unserialize($_SESSION[self::RICERCA_SCADENZE_CLIENTE_TEMPLATE]);
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {
        $esito = TRUE;
        $msg = "<br>";

        // ----------------------------------------------
        // Eventuali controlli da backend
        // ----------------------------------------------

        if ($msg != "<br>") {
            $_SESSION[self::MESSAGGIO] = $msg;
        } else {
            unset($_SESSION[self::MESSAGGIO]);
        }
        return $esito;
    }

    public function displayPagina() {

        $scadenzaCliente = ScadenzaCliente::getInstance();
        $causale = Causale::getInstance();
        $cliente = Cliente::getInstance();
        $fornitore = Fornitore::getInstance();
        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        // Parti utili per la composizione della pagina

        $paginaRicercaScadenze = $this->root . $array['template'] . self::PAGINA_RICERCA_SCADENZE_CLIENTE;
        $dialogoVisualizzaRegistrazione = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_REGISTRAZIONE;
        $dialogoVisualizzaIncasso = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_INCASSO;
        $dialogoModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_MODIFICA_REGISTRAZIONE;
        $dialogoNuovoDettaglioModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_REGISTRAZIONE;
        $dialogoNuovaScadenzaModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVA_SCADENZA_MODIFICA_REGISTRAZIONE;
        $dialogoModificaIncasso = $this->root . $array['template'] . self::DIALOGO_MODIFICA_INCASSO;
        $dialogoNuovoDettaglioModificaIncasso = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_INCASSO;
        // Creo l'elenco delle scadenze

        $risultato_ricerca = "";
        $dati = "";

        if ($scadenzaCliente->getQtaScadenze() > 0) {

            $dati = array(
                "labelclifor" => "%ml.codclie%",
                "labeldata" => "%ml.datregistrazione%",
                "labelnota" => "%ml.notascadenza%",
                "labelnumfatt" => "%ml.numfatt%",
                "labeltipoaddebito" => "%ml.tipaddebito%",
                "labelstatoscadenza" => "%ml.stascadenza%",
                "labelimporto" => "%ml.impscadenza%"
            );

            $risultato_ricerca = $this->intestazione($dati);

            $idcliente_break = "";
            $datregistrazione_break = "";
            $totale_data = 0;
            $totale_cliente = 0;
            $totale_scadenze = 0;

            foreach ($scadenzaCliente->getScadenze() as $row) {

                $numfatt = trim($row[ScadenzaCliente::NUM_FATTURA]);

                if (($idcliente_break == self::EMPTYSTRING) && ($datregistrazione_break == self::EMPTYSTRING)) {
                    $idcliente_break = trim($row[ScadenzaCliente::ID_CLIENTE]);
                    $datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);

                    $descliente = trim($row[Cliente::DES_CLIENTE]);
                    $datregistrazione = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
                }

                if (trim($row[ScadenzaCliente::NOTA]) != "") {
                    $nota = trim($row[ScadenzaCliente::NOTA]);
                } else {
                    $nota = "&ndash;&ndash;&ndash;";
                }

                if (trim($row[ScadenzaCliente::TIP_ADDEBITO]) != "") {
                    $tipaddebito = trim($row[ScadenzaCliente::TIP_ADDEBITO]);
                } else {
                    $tipaddebito = "&ndash;&ndash;&ndash;";
                }

                $bottoneVisualizzaScadenza = self::VISUALIZZA_SCADENZA_CLIENTE_HREF . trim($row[ScadenzaCliente::ID_SCADENZA]) . self::VISUALIZZA_ICON;
                $bottoneModificaScadenza = self::MODIFICA_SCADENZA_CLIENTE_HREF . trim($row[ScadenzaCliente::ID_SCADENZA]) . self::MODIFICA_ICON;

                if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_APERTA) {
                    $stascadenza = self::SCADENZA_DA_INCASSARE;
                    $tdclass = self::DATA_KO;
                }

                if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
                    $stascadenza = self::SCADENZA_INCASSATA;
                    $tdclass = self::DATA_OK;
                }

                if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
                    $stascadenza = self::SCADENZA_POSTICIPATA;
                    $tdclass = self::DATA_CHIUSA;
                }

                /**
                 * Totali a rottura
                 */
                if (trim($row[ScadenzaCliente::ID_CLIENTE]) != $idcliente_break) {

                    $dati = array(
                        'totaledata' => $totale_data,
                        'totaleclifor' => $totale_cliente,
                        'labeltotaledata' => "Totale Data",
                        'labeltotaleclifor' => "Totale Cliente",
                        'descrizione' => $descliente,
                        'data' => $datregistrazione
                    );

                    $risultato_ricerca .= $this->totaleData($dati);
                    $risultato_ricerca .= $this->totaleCliFor($dati);

                    $idcliente_break = trim($row[Cliente::ID_CLIENTE]);
                    $descliente = trim($row[Cliente::DES_CLIENTE]);
                    $datregistrazione = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
                    $datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);

                    $totale_scadenze += $totale_cliente;
                    $totale_cliente = 0;
                    $totale_data = 0;
                } else if (trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]) != $datregistrazione_break) {

                    $dati = array(
                        'totaledata' => $totale_data,
                        'totaleclifor' => $totale_cliente,
                        'labeltotaledata' => "Totale Data",
                        'labeltotaleclifor' => "Totale Cliente",
                        'descrizione' => $descliente,
                        'data' => $datregistrazione
                    );

                    $risultato_ricerca .= $this->totaleData($dati);

                    $datregistrazione = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
                    $datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
                    $totale_scadenze += $totale_data;
                    $totale_data = 0;
                }

                /**
                 * riga scadenza
                 */
                $dati = array(
                    'descrizione' => $descliente,
                    'data' => $datregistrazione,
                    'nota' => $nota,
                    'numfatt' => $numfatt,
                    'tipaddebito' => $tipaddebito,
                    'stascadenza' => $stascadenza,
                    'tdclass' => $tdclass,
                    'importo' => $row[ScadenzaCliente::IMP_REGISTRAZIONE],
                    'bottoneVisualizzaScadenza' => $bottoneVisualizzaScadenza,
                    'bottoneModificaScadenza' => $bottoneModificaScadenza
                );

                $risultato_ricerca .= $this->riga($dati);

                $descliente = self::EMPTYSTRING;
                $datregistrazione = self::EMPTYSTRING;
                $totale_data += trim($row[ScadenzaCliente::IMP_REGISTRAZIONE]);
                $totale_cliente += trim($row[ScadenzaCliente::IMP_REGISTRAZIONE]);
            }

            /**
             * Ultimo totale scadenze fuori ciclo
             */
            $totale_scadenze += $totale_cliente;

            $dati = array(
                'totaledata' => $totale_data,
                'totaleclifor' => $totale_cliente,
                'totalescadenze' => $totale_scadenze,
                'labeltotaledata' => "Totale Data",
                'labeltotaleclifor' => "Totale Cliente",
                'labeltotalescadenze' => "Totale Scadenze Clienti",
                'descrizione' => $descliente,
                'data' => $datregistrazione
            );

            $risultato_ricerca .= $this->totaleData($dati);
            $risultato_ricerca .= $this->totaleCliFor($dati);
            $risultato_ricerca .= $this->totaleScadenze($dati);

            $risultato_ricerca .= "</tbody></table>";
        } else {
            $risultato_ricerca = "<div class='row'>" .
                    "    <div class='col-sm-12'>" . $_SESSION[self::MSG] . "</div>" .
                    "</div>";
        }

        $elencoCausali = $causale->caricaCausali($db);

        $cliente->load($db);
        $_SESSION[self::CLIENTE] = serialize($cliente);

        $fornitore->load($db);
        $_SESSION[self::FORNITORE] = serialize($fornitore);

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%datascad_da%' => $scadenzaCliente->getDatScadenzaDa(),
            '%datascad_a%' => $scadenzaCliente->getDatScadenzaA(),
            '%codneg_sel%' => $scadenzaCliente->getCodNegozioSel(),
            '%villa-selected%' => ($scadenzaCliente->getCodNegozioSel() == self::VILLA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%brembate-selected%' => ($scadenzaCliente->getCodNegozioSel() == self::BREMBATE) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%trezzo-selected%' => ($scadenzaCliente->getCodNegozioSel() == self::TREZZO) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%00-selected%' => ($scadenzaCliente->getStaScadenzaSel() == self::SCADENZA_APERTA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%10-selected%' => ($scadenzaCliente->getStaScadenzaSel() == self::SCADENZA_CHIUSA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%02-selected%' => ($scadenzaCliente->getStaScadenzaSel() == self::SCADENZA_RIMANDATA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
            '%elenco_causali%' => $elencoCausali,
            '%elenco_causali_cre%' => $elencoCausali,
            '%elenco_causali_mod%' => $elencoCausali,
            '%elenco_causali_inc_mod%' => $elencoCausali,
            '%elenco_clienti%' => $this->caricaElencoClienti($cliente),
            '%elenco_fornitori%' => $this->caricaElencoFornitori($fornitore),
            '%risultato_ricerca%' => $risultato_ricerca
        );

        // Includo la pagina principale e tutti i dialoghi che servono e faccio la send della pagina ottenuta

        $template = $utility->tailFile($utility->getTemplate($paginaRicercaScadenze), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaIncasso), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovaScadenzaModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaIncasso), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaIncasso), $replace);

        echo $utility->tailTemplate($template);
    }

}

?>