<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.presentation.interface.php';
require_once 'utility.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'causale.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';

class RicercaScadenzeTemplate extends ScadenzeAbstract implements ScadenzePresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::RICERCA_SCADENZE_FORNITORE_TEMPLATE]))
            $_SESSION[self::RICERCA_SCADENZE_FORNITORE_TEMPLATE] = serialize(new RicercaScadenzeTemplate());
        return unserialize($_SESSION[self::RICERCA_SCADENZE_FORNITORE_TEMPLATE]);
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {
        $esito = TRUE;
        $msg = "<br>";

        // ----------------------------------------------
        // ----------------------------------------------

        if ($msg != "<br>") {
            $_SESSION[self::MESSAGGIO] = $msg;
        } else {
            unset($_SESSION[self::MESSAGGIO]);
        }
        return $esito;
    }

    public function displayPagina() {
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $causale = Causale::getInstance();
        $cliente = Cliente::getInstance();
        $fornitore = Fornitore::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        // Parti utili per la composizione della pagina

        $paginaRicercaScadenze = $this->root . $array['template'] . self::PAGINA_RICERCA_SCADENZE_FORNITORE;
        $dialogoVisualizzaRegistrazione = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_REGISTRAZIONE;
        $dialogoVisualizzaPagamento = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_PAGAMENTO;
        $dialogoModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_MODIFICA_REGISTRAZIONE;
        $dialogoNuovoDettaglioModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_REGISTRAZIONE;
        $dialogoNuovaScadenzaModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVA_SCADENZA_MODIFICA_REGISTRAZIONE;
        $dialogoModificaPagamento = $this->root . $array['template'] . self::DIALOGO_MODIFICA_PAGAMENTO;
        $dialogoNuovoDettaglioModificaPagamento = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_PAGAMENTO;

        // Creo l'elenco delle scadenze

        $risultato_ricerca = "";
        $dati = "";

        if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {

            $dati = array(
                "labelclifor" => "%ml.codforn%",
                "labeldata" => "%ml.datscadenza%",
                "labelnota" => "%ml.notascadenza%",
                "labelnumfatt" => "%ml.numfatt%",
                "labeltipoaddebito" => "%ml.tipaddebito%",
                "labelstatoscadenza" => "%ml.stascadenza%",
                "labelimporto" => "%ml.impscadenza%"
            );

            $risultato_ricerca = $this->intestazione($dati);

            $idfornitore_break = "";
            $datscadenza_break = "";
            $totale_data = 0;
            $totale_fornitore = 0;
            $totale_scadenze = 0;

            foreach ($scadenzaFornitore->getScadenzeDaPagare() as $row) {

                if (($idfornitore_break == self::EMPTYSTRING) && ($datscadenza_break == self::EMPTYSTRING)) {
                    $idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
                    $datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                    $desfornitore = trim($row[Fornitore::DES_FORNITORE]);
                    $numfatt = trim($row[ScadenzaFornitore::NUM_FATTURA]);
                    $datscadenza = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                }

                if (trim($row[ScadenzaFornitore::NOTA_SCADENZA]) != self::EMPTYSTRING) {
                    $notascadenza = trim($row[ScadenzaFornitore::NOTA_SCADENZA]);
                } else {
                    $notascadenza = "&ndash;&ndash;&ndash;";
                }

                if (trim($row[ScadenzaFornitore::TIP_ADDEBITO]) != self::EMPTYSTRING) {
                    $tipaddebito = trim($row[ScadenzaFornitore::TIP_ADDEBITO]);
                } else {
                    $tipaddebito = "&ndash;&ndash;&ndash;";
                }

                $bottoneVisualizzaScadenza = self::VISUALIZZA_SCADENZA_HREF . trim($row[ScadenzaFornitore::ID_SCADENZA]) . self::VISUALIZZA_ICON;
                $bottoneModificaScadenza = self::MODIFICA_SCADENZA_HREF . trim($row[ScadenzaFornitore::ID_SCADENZA]) . self::MODIFICA_ICON;

                if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_SOSPESA) {
                    $stascadenza = self::CAMPO_VUOTO;
                    $tdclass = self::DATA_KO;
                }

                if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_APERTA) {
                    $stascadenza = self::SCADENZA_DA_PAGARE;
                    $tdclass = self::DATA_KO;
                }

                if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
                    $stascadenza = self::SCADENZA_PAGATA;
                    $tdclass = self::DATA_OK;
                }

                if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
                    $stascadenza = self::SCADENZA_POSTICIPATA;
                    $tdclass = self::DATA_CHIUSA;
                }

                /**
                 * Totali a rottura
                 */
                if (trim($row[ScadenzaFornitore::ID_FORNITORE]) != $idfornitore_break) {

                    $dati = array(
                        'totaledata' => $totale_data,
                        'totaleclifor' => $totale_fornitore,
                        'labeltotaledata' => "Totale Data",
                        'labeltotaleclifor' => "Totale Fornitore",
                        'descrizione' => $desfornitore,
                        'data' => $datscadenza
                    );

                    $risultato_ricerca .= $this->totaleData($dati);
                    $risultato_ricerca .= $this->totaleCliFor($dati);

                    $idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
                    $datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                    $desfornitore = trim($row[Fornitore::DES_FORNITORE]);
                    $datscadenza = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                    $numfatt = trim($row[ScadenzaFornitore::NUM_FATTURA]);

                    $totale_scadenze += $totale_fornitore;
                    $totale_fornitore = 0;
                    $totale_data = 0;
                } else if (trim($row[ScadenzaFornitore::DAT_SCADENZA]) != $datscadenza_break) {

                    $dati = array(
                        'totaledata' => $totale_data,
                        'totaleclifor' => $totale_fornitore,
                        'labeltotaledata' => "Totale Data",
                        'labeltotaleclifor' => "Totale Forniore",
                        'descrizione' => $desfornitore,
                        'data' => $datscadenza
                    );

                    $risultato_ricerca .= $this->totaleData($dati);

                    $datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                    $datscadenza = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
                    $totale_scadenze += $totale_data;
                    $totale_data = 0;
                }

                /**
                 * riga scadenza
                 */
                $dati = array(
                    'descrizione' => $desfornitore,
                    'data' => $datscadenza,
                    'nota' => $notascadenza,
                    'numfatt' => $numfatt,
                    'tipaddebito' => $tipaddebito,
                    'stascadenza' => $stascadenza,
                    'tdclass' => $tdclass,
                    'importo' => $row[ScadenzaFornitore::IMP_IN_SCADENZA],
                    'bottoneVisualizzaScadenza' => $bottoneVisualizzaScadenza,
                    'bottoneModificaScadenza' => $bottoneModificaScadenza
                );

                $risultato_ricerca .= $this->riga($dati);

                $desfornitore = self::EMPTYSTRING;
                $datscadenza = self::EMPTYSTRING;
                $totale_data += trim($row[ScadenzaFornitore::IMP_IN_SCADENZA]);
                $totale_fornitore += trim($row[ScadenzaFornitore::IMP_IN_SCADENZA]);
            }

            /**
             * Ultimo totale scadenze fuori ciclo
             */
            $totale_scadenze += $totale_fornitore;

            $dati = array(
                'totaledata' => $totale_data,
                'totaleclifor' => $totale_fornitore,
                'totalescadenze' => $totale_scadenze,
                'labeltotaledata' => "Totale Data",
                'labeltotaleclifor' => "Totale Fornitore",
                'labeltotalescadenze' => "Totale Scadenze Fornitori",
                'descrizione' => $desfornitore,
                'data' => $datscadenza
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

        $fornitore->load($db);
        $_SESSION[self::FORNITORE] = serialize($fornitore);

        $cliente->load($db);
        $_SESSION[self::CLIENTE] = serialize($cliente);

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%datascad_da%' => $scadenzaFornitore->getDatScadenzaDa(),
            '%datascad_a%' => $scadenzaFornitore->getDatScadenzaA(),
            '%codneg_sel%' => $scadenzaFornitore->getCodNegozioSel(),
            '%villa-selected%' => ($scadenzaFornitore->getCodNegozioSel() == self::VILLA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%brembate-selected%' => ($scadenzaFornitore->getCodNegozioSel() == self::BREMBATE) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%trezzo-selected%' => ($scadenzaFornitore->getCodNegozioSel() == self::TREZZO) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%00-selected%' => ($scadenzaFornitore->getStaScadenzaSel() == self::SCADENZA_APERTA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%10-selected%' => ($scadenzaFornitore->getStaScadenzaSel() == self::SCADENZA_CHIUSA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%02-selected%' => ($scadenzaFornitore->getStaScadenzaSel() == self::SCADENZA_RIMANDATA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
            '%elenco_causali%' => $elencoCausali,
            '%elenco_causali_cre%' => $elencoCausali,
            '%elenco_causali_mod%' => $elencoCausali,
            '%elenco_causali_pag_mod%' => $elencoCausali,
            '%elenco_fornitori%' => $this->caricaElencoFornitori($fornitore),
            '%elenco_clienti%' => $this->caricaElencoClienti($cliente),
            '%risultato_ricerca%' => $risultato_ricerca
        );

        // Includo la pagina principale e tutti i dialoghi che servono e faccio la send della pagina ottenuta

        $template = $utility->tailFile($utility->getTemplate($paginaRicercaScadenze), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaPagamento), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovaScadenzaModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaPagamento), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaPagamento), $replace);

        echo $utility->tailTemplate($template);
    }

}

?>