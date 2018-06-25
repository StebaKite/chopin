<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.presentation.interface.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'causale.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';
require_once 'sottoconto.class.php';

class RicercaRegistrazioneTemplate extends PrimanotaAbstract implements PrimanotaPresentationInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::RICERCA_REGISTRAZIONE_TEMPLATE]))
            $_SESSION[self::RICERCA_REGISTRAZIONE_TEMPLATE] = serialize(new RicercaRegistrazioneTemplate());
        return unserialize($_SESSION[self::RICERCA_REGISTRAZIONE_TEMPLATE]);
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {

        $registrazione = Registrazione::getInstance();
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
        $registrazione = Registrazione::getInstance();
        $causale = Causale::getInstance();
        $cliente = Cliente::getInstance();
        $fornitore = Fornitore::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        // Parti utili per la composizione della pagina

        $paginaRicercaRegistrazione = $this->root . $array['template'] . self::PAGINA_RICERCA_REGISTRAZIONE;
        $dialogoVisualizzaRegistrazione = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_REGISTRAZIONE;
        $dialogoNuovaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVA_REGISTRAZIONE;
        $dialogoCancellaRegistrazione = $this->root . $array['template'] . self::DIALOGO_CANCELLA_REGISTRAZIONE;
        $dialogoVisualizzaPagamento = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_PAGAMENTO;
        $dialogoVisualizzaIncasso = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_INCASSO;
        $dialogoVisualizzaCorrispettivoMercato = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_CORRISPETTIVO_MERCATO;
        $dialogoVisualizzaCorrispettivoNegozio = $this->root . $array['template'] . self::DIALOGO_VISUALIZZA_CORRISPETTIVO_NEGOZIO;
        $dialogoModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_MODIFICA_REGISTRAZIONE;
        $dialogoModificaIncasso = $this->root . $array['template'] . self::DIALOGO_MODIFICA_INCASSO;
        $dialogoNuovoDettaglioNuovaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_NUOVA_REGISTRAZIONE;
        $dialogoNuovoDettaglioModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_REGISTRAZIONE;
        $dialogoNuovaScadenzaNuovaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVA_SCADENZA_NUOVA_REGISTRAZIONE;
        $dialogoNuovaScadenzaModificaRegistrazione = $this->root . $array['template'] . self::DIALOGO_NUOVA_SCADENZA_MODIFICA_REGISTRAZIONE;
        $dialogoModificaPagamento = $this->root . $array['template'] . self::DIALOGO_MODIFICA_PAGAMENTO;
        $dialogoModificaCorrispettivoMercato = $this->root . $array['template'] . self::DIALOGO_MODIFICA_CORRISPETTIVO_MERCATO;
        $dialogoModificaCorrispettivoNegozio = $this->root . $array['template'] . self::DIALOGO_MODIFICA_CORRISPETTIVO_NEGOZIO;
        $dialogoNuovoDettaglioModificaPagamento = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_PAGAMENTO;
        $dialogoNuovoIncasso = $this->root . $array['template'] . self::DIALOGO_NUOVO_INCASSO;
        $dialogoNuovoPagamento = $this->root . $array['template'] . self::DIALOGO_NUOVO_PAGAMENTO;
        $dialogoNuovoCorrispettivoMercato = $this->root . $array['template'] . self::DIALOGO_NUOVO_CORRISPETTIVO_MERCATO;
        $dialogoNuovoCorrispettivoNegozio = $this->root . $array['template'] . self::DIALOGO_NUOVO_CORRISPETTIVO_NEGOZIO;
        $dialogoNuovoDettaglioNuovoIncasso = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_NUOVO_INCASSO;
        $dialogoNuovoDettaglioNuovoPagamento = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_NUOVO_PAGAMENTO;
        $dialogoNuovoDettaglioModificaIncasso = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_INCASSO;
        $dialogoNuovoDettaglioNuovoCorrispettivoMercato = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_NUOVO_CORRISPETTIVO_MERCATO;
        $dialogoNuovoDettaglioModificaCorrispettivoMercato = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_CORRISPETTIVO_MERCATO;
        $dialogoNuovoDettaglioNuovoCorrispettivoNegozio = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_NUOVO_CORRISPETTIVO_NEGOZIO;
        $dialogoNuovoDettaglioModificaCorrispettivoNegozio = $this->root . $array['template'] . self::DIALOGO_NUOVO_DETTAGLIO_MODIFICA_CORRISPETTIVO_NEGOZIO;

        // creo l'elenco delle registrazioni

        $risultato_ricerca = "";

        if ($registrazione->getQtaRegistrazioni() > 0) {

            $risultato_ricerca = "" .
                    "<div class='row'>" .
                    "   <div class='col-sm-4'>" .
                    "       <div class='input-group input-group-sm'>" .
                    "           <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
                    "       </div>" .
                    "   </div>" .
                    "   <div class='col-sm-8'>" . $_SESSION[self::MSG] . "</div>" .
                    "</div>" .
                    "<br/>" .
                    "<table class='table table-bordered table-hover'>" .
                    "	<thead>" .
                    "		<tr>" .
                    "			<th>id</th>" .
                    "			<th>%ml.datReg%</th>" .
                    "			<th>%ml.numfatt%</th>" .
                    "			<th>%ml.desReg%</th>" .
                    "			<th>%ml.codcau%</th>" .
                    "			<th>&nbsp;</th>" .
                    "			<th>&nbsp;</th>" .
                    "			<th>&nbsp;</th>" .
                    "		</tr>" .
                    "	</thead>" .
                    "	<tbody id='myTable'>";

            foreach ($registrazione->getRegistrazioni() as $unaRegistrazione) {

                if (trim($unaRegistrazione[Registrazione::TIPO_RIGA_REGISTRAZIONE]) == Registrazione::RIGA_REGISTRAZIONE) {

                    /**
                     * Imposto i bottoni validi sulla riga della registrazione
                     */
                    switch ($unaRegistrazione[Registrazione::STA_REGISTRAZIONE]) {
                        case (self::REGISTRAZIONE_APERTA): {

                                switch ($unaRegistrazione[Registrazione::COD_CAUSALE]) {
                                    case ($array[self::CORRISPETTIVO_MERCATO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
                                            $bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
                                            break;
                                        }
                                    case ($array[self::CORRISPETTIVO_NEGOZIO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
                                            $bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
                                            break;
                                        }
                                    case ($array[self::PAGAMENTO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_PAGAMENTO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_PAGAMENTO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
                                            $bottoneCancella = "&nbsp;";
                                            break;
                                        }
                                    case ($array[self::INCASSO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_INCASSO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_INCASSO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
                                            $bottoneCancella = "&nbsp;";
                                            break;
                                        }
                                    default: {
                                            $bottoneVisualizza = self::VISUALIZZA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;

                                            /**
                                             * Se vi è una correlazione della registrazione con un pagamento o un incasso la cancellazione della
                                             * stessa non è possibile.
                                             */
                                            if (($unaRegistrazione[Registrazione::ID_PAGAMENTO_CORRELATO] == "") and ( $unaRegistrazione[Registrazione::ID_INCASSO_CORRELATO] == "")) {
                                                $bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
                                            } else {
                                                $bottoneCancella = "&nbsp;";
                                            }
                                        }
                                }
                                break;
                            }
                        case (self::REGISTRAZIONE_ERRATA): {

                                switch ($row['cod_causale']) {
                                    case ($array[self::CORRISPETTIVO_MERCATO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_CORRISPETTIVO_MERCATO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
                                            $bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
                                            break;
                                        }
                                    case ($array[self::CORRISPETTIVO_NEGOZIO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_CORRISPETTIVO_NEGOZIO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
                                            $bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
                                            break;
                                        }
                                    case ($array[self::PAGAMENTO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_PAGAMENTO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = "&nbsp;";
                                            $bottoneCancella = "&nbsp;";
                                            break;
                                        }
                                    case ($array[self::INCASSO]): {
                                            $bottoneVisualizza = self::VISUALIZZA_INCASSO_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = "&nbsp;";
                                            $bottoneCancella = "&nbsp;";
                                            break;
                                        }
                                    default: {
                                            $bottoneVisualizza = self::VISUALIZZA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::VISUALIZZA_ICON;
                                            $bottoneModifica = self::MODIFICA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::MODIFICA_ICON;
                                            $bottoneCancella = self::CANCELLA_REGISTRAZIONE_HREF . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . self::CANCELLA_ICON;
                                            break;
                                        }
                                }
                                break;
                            }
                        default: {
                                $bottoneVisualizza = "&nbsp;";
                                $bottoneModifica = "&nbsp;";
                                $bottoneCancella = "&nbsp;";
                                break;
                            }
                    }

                    $risultato_ricerca .= "<tr>" .
                            "	<td>" . trim($unaRegistrazione[Registrazione::ID_REGISTRAZIONE]) . "</td>" .
                            "	<td>" . trim($unaRegistrazione[Registrazione::DAT_REGISTRAZIONE]) . "</td>" .
                            "	<td>" . trim($unaRegistrazione[Registrazione::NUM_FATTURA]) . "</td>" .
                            "	<td>" . trim($unaRegistrazione[Registrazione::DES_REGISTRAZIONE]) . "</td>" .
                            "	<td>" . trim($unaRegistrazione[Registrazione::COD_CAUSALE]) . " &ndash; " . trim($unaRegistrazione[Causale::DES_CAUSALE]) . "</td>" .
                            "	<td>" . $bottoneVisualizza . "</td>" .
                            "	<td>" . $bottoneModifica . "</td>" .
                            "	<td>" . $bottoneCancella . "</td>" .
                            "</tr>";
                }
// 				elseif (trim($unaRegistrazione[Registrazione::TIPO_RIGA_REGISTRAZIONE]) == Registrazione::RIGA_DETTAGLIO_REGISTRAZIONE) {
// 					$risultato_ricerca .=
// 					"<tr>" .
// 					"	<td colspan='2'></td>" .
// 					"	<td colspan='6'><i>&ndash;&nbsp;Importo:&nbsp;" .
// 					trim($unaRegistrazione[DettaglioRegistrazione::IMP_REGISTRAZIONE]) . "&nbsp;&nbsp;" .
// 					trim($unaRegistrazione[DettaglioRegistrazione::IND_DAREAVERE]) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Conto:&nbsp;" .
// 					trim($unaRegistrazione[DettaglioRegistrazione::COD_CONTO]) .
// 					trim($unaRegistrazione[DettaglioRegistrazione::COD_SOTTOCONTO]) . " &ndash; " .
// 					trim($unaRegistrazione[Sottoconto::DES_SOTTOCONTO]) .
// 					"   </i></td>" .
// 					"</tr>";
// 				}
            }
            $risultato_ricerca .= "</tbody></table>";
        } else {
            $risultato_ricerca = "<div class='row'>" .
                    "    <div class='col-sm-12'>" . $_SESSION[self::MSG] . "</div>" .
                    "</div>";
        }

        $causale->setCodCausale($registrazione->getCodCausaleSel());
        $elencoCausali = $causale->caricaCausali($db);

        $fornitore->load($db);
        $cliente->load($db);
        $_SESSION[self::FORNITORE] = serialize($fornitore);
        $_SESSION[self::CLIENTE] = serialize($cliente);

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
            '%datareg_da%' => $registrazione->getDatRegistrazioneDa(),
            '%datareg_a%' => $registrazione->getDatRegistrazioneA(),
            '%villa-selected%' => ($registrazione->getCodNegozioSel() == "VIL") ? "selected" : "",
            '%brembate-selected%' => ($registrazione->getCodNegozioSel() == "BRE") ? "selected" : "",
            '%trezzo-selected%' => ($registrazione->getCodNegozioSel() == "TRE") ? "selected" : "",
            '%elenco_causali%' => $elencoCausali,
            '%elenco_causali_cre%' => $elencoCausali,
            '%elenco_causali_mod%' => $elencoCausali,
            '%elenco_causali_inc_cre%' => $elencoCausali,
            '%elenco_causali_inc_mod%' => $elencoCausali,
            '%elenco_causali_pag_cre%' => $elencoCausali,
            '%elenco_causali_pag_mod%' => $elencoCausali,
            '%elenco_causali_cormer_cre%' => $elencoCausali,
            '%elenco_causali_cormer_mod%' => $elencoCausali,
            '%elenco_causali_corneg_cre%' => $elencoCausali,
            '%elenco_causali_corneg_mod%' => $elencoCausali,
            '%elenco_fornitori%' => $this->caricaElencoFornitori($fornitore),
            '%elenco_clienti%' => $this->caricaElencoClienti($cliente),
            '%risultato_ricerca%' => $risultato_ricerca
        );

        // Includo la pagina principale e tutti i dialoghi che servono
        // Registrazione
        $template = $utility->tailFile($utility->getTemplate($paginaRicercaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoCancellaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioNuovaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovaScadenzaNuovaRegistrazione), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovaScadenzaModificaRegistrazione), $replace);

        // Incassi e Pagamenti
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaPagamento), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaIncasso), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoIncasso), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoPagamento), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaPagamento), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaIncasso), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaPagamento), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioNuovoIncasso), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioNuovoPagamento), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaIncasso), $replace);

        // Corrispettivi
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaCorrispettivoMercato), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoVisualizzaCorrispettivoNegozio), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaCorrispettivoMercato), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoModificaCorrispettivoNegozio), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoCorrispettivoMercato), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoCorrispettivoNegozio), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioNuovoCorrispettivoMercato), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaCorrispettivoMercato), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioNuovoCorrispettivoNegozio), $replace);
        $template .= $utility->tailFile($utility->getTemplate($dialogoNuovoDettaglioModificaCorrispettivoNegozio), $replace);

        echo $utility->tailTemplate($template);
    }

}

?>