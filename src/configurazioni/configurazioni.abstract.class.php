<?php

require_once 'nexus6.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'registrazione.class.php';
require_once 'conto.class.php';

abstract class ConfigurazioniAbstract extends Nexus6Abstract implements ConfigurazioniPresentationInterface {

    public static $messaggio;

    const NESSUNO = "NS";
    const COSTI_FISSI = "CF";
    const COSTI_VARIABILI = "CV";
    const RICAVI = "RC";
    
    // Bottoni

    const CANCELLA_SOTTOCONTO_HREF = "<a onclick='cancellaSottoconto(";
    const MODIFICA_GRUPPO_SOTTOCONTO_HREF = "<a onclick='modificaGruppoSottoconto(";
    const ESTRAI_MOVIMENTI_SOTTOCONTO_HREF = "<a onclick='estraiMovimentiSottoconto(";
    const ESCLUDI_CONTO_HREF = "<a onclick='escludiConto(";
    const INCLUDI_CONTO_HREF = "<a onclick='includiConto(";

    // Metodi comuni di utilita della prima note ---------------------------

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {
        
    }

    public function makeTabellaSottoconti($conto, $sottoconto) {

        $thead = "";
        $tbody = "";

        if ($sottoconto->getQtaSottoconti() > 0) {
            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th>Sottoconto</th>" .
                    "		<th>Descrizione</th>" .
                    "		<th>Gruppo</th>" .
                    "		<th>&nbsp;</th>" .
                    "		<th>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($sottoconto->getSottoconti() as $row) {
                $bottoneCancella = "&nbsp;";
                $bottoneModifica = self::MODIFICA_GRUPPO_SOTTOCONTO_HREF . "&apos;" . $row[Sottoconto::IND_GRUPPO] . "&apos;," . $row[Sottoconto::COD_CONTO] . "," . $row[Sottoconto::COD_SOTTOCONTO] . self::MODIFICA_ICON;

                if ($row[Sottoconto::NUM_REG_SOTTOCONTO] == 0) {
                    $bottoneCancella = self::CANCELLA_SOTTOCONTO_HREF . $row[Sottoconto::COD_SOTTOCONTO] . "," . $sottoconto->getCodConto() . ",&apos;_mod&apos;" . self::CANCELLA_ICON;
                }

                if ($row[Sottoconto::IND_GRUPPO] == "") {
                    $indGruppo = "&ndash;&ndash;&ndash;";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::NESSUNO) {
                    $indGruppo = "&ndash;&ndash;&ndash;";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_FISSI) {
                    $indGruppo = "Costi Fissi";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_VARIABILI) {
                    $indGruppo = "Costi Variabili";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::RICAVI) {
                    $indGruppo = "Ricavi";
                }

                $tbody .= "<tr>" .
                        "	<td>" . $row[Sottoconto::COD_SOTTOCONTO] . "</td>" .
                        "	<td>" . $row[Sottoconto::DES_SOTTOCONTO] . "</td>" .
                        "	<td>" . $indGruppo . "</td>" .
                        "   <td>" . $bottoneModifica . "</td>" .
                        "   <td>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return $thead . $tbody;
    }

    public function makeTabellaSottocontiReadonly($conto, $sottoconto) {

        $thead = "";
        $tbody = "";

        if ($sottoconto->getQtaSottoconti() > 0) {
            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th>Sottoconto</th>" .
                    "		<th>Descrizione</th>" .
                    "		<th>Gruppo</th>" .
                    "		<th>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($sottoconto->getSottoconti() as $row) {
                $bottoneMovimenti = self::ESTRAI_MOVIMENTI_SOTTOCONTO_HREF . $row[Sottoconto::COD_CONTO] . "," . $row[Sottoconto::COD_SOTTOCONTO] . self::LISTA_ICON;

                if ($row[Sottoconto::IND_GRUPPO] == "") {
                    $indGruppo = "&ndash;&ndash;&ndash;";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::NESSUNO) {
                    $indGruppo = "&ndash;&ndash;&ndash;";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_FISSI) {
                    $indGruppo = "Costi Fissi";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_VARIABILI) {
                    $indGruppo = "Costi Variabili";
                } elseif ($row[Sottoconto::IND_GRUPPO] == self::RICAVI) {
                    $indGruppo = "Ricavi";
                }

                $tbody .= "<tr>" .
                        "	<td>" . $row[Sottoconto::COD_SOTTOCONTO] . "</td>" .
                        "	<td>" . $row[Sottoconto::DES_SOTTOCONTO] . "</td>" .
                        "	<td>" . $indGruppo . "</td>" .
                        "   <td>" . $bottoneMovimenti . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return $thead . $tbody;
    }

    public function makeTableContiConfigurati($configurazioneCausale) {
        $thead = "";
        $tbody = "";

        if ($configurazioneCausale->getQtaContiConfigurati() > 0) {
            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th>Conto</th>" .
                    "		<th>Descrizione</th>" .
                    "		<th>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($configurazioneCausale->getContiConfigurati() as $row) {

                $bottoneEscludi = self::ESCLUDI_CONTO_HREF . $row[ConfigurazioneCausale::COD_CONTO] . self::ESCLUDI_ICON;

                $tbody .= "<tr>" .
                        "	<td>" . $row[ConfigurazioneCausale::COD_CONTO] . "</td>" .
                        "	<td>" . $row[Conto::DES_CONTO] . "</td>" .
                        "	<td>" . $bottoneEscludi . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return $thead . $tbody;
    }

    public function makeTableContiConfigurabili($configurazioneCausale) {
        $thead = "";
        $tbody = "";

        if ($configurazioneCausale->getQtaContiConfigurabili() > 0) {
            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th>&nbsp;</th>" .
                    "		<th>Conto</th>" .
                    "		<th>Descrizione</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($configurazioneCausale->getContiConfigurabili() as $row) {

                $bottoneIncludi = self::INCLUDI_CONTO_HREF . $row[ConfigurazioneCausale::COD_CONTO] . self::INCLUDI_ICON;

                $tbody .= "<tr>" .
                        "	<td>" . $bottoneIncludi . "</td>" .
                        "	<td>" . $row[ConfigurazioneCausale::COD_CONTO] . "</td>" .
                        "	<td>" . $row[Conto::DES_CONTO] . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return $thead . $tbody;
    }

    public function refreshContiConfigurati($db, $configurazioneCausale) {
        $CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";

        if (!$configurazioneCausale->loadContiConfigurati($db)) {
            $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
            return false;
        }
        $_SESSION[$CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
        return true;
    }

    public function refreshContiConfigurabili($db, $configurazioneCausale) {
        $CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";

        if (!$configurazioneCausale->loadContiConfigurabili($db)) {
            $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
            return false;
        }
        $_SESSION[$CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
        return true;
    }

    public function makeTabellaMovimentiSottoconto($sottoconto) {

        $thead = "";
        $tbody = "";

        if ($sottoconto->getQtaRegistrazioniTrovate() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th></th>" .
                    "		<th>%ml.datReg%</th>" .
                    "		<th>%ml.descreg%</th>" .
                    "		<th>%ml.dare%</th>" .
                    "		<th>%ml.avere%</th>" .
                    "		<th>%ml.saldoprogressivo%</th>" .
                    "	</tr>" .
                    "</thead>" .
                    $totaleDare = 0;
            $totaleAvere = 0;
            $saldo = 0;

            foreach ($sottoconto->getRegistrazioniTrovate() as $row) {

                $class = "";

                if ($row[DettaglioRegistrazione::IND_DAREAVERE] == 'D') {
                    $totaleDare = $totaleDare + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                    $impDare = number_format(round($row[DettaglioRegistrazione::IMP_REGISTRAZIONE], 2), 2, ',', '.');
                    $impAvere = "";
                } elseif ($row[DettaglioRegistrazione::IND_DAREAVERE] == 'A') {
                    $totaleAvere = $totaleAvere + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
                    $impDare = "";
                    $impAvere = number_format(round($row[DettaglioRegistrazione::IMP_REGISTRAZIONE], 2), 2, ',', '.');
                }

                if (trim($row[Conto::TIP_CONTO]) == "Dare") {
                    $saldo = $totaleDare - $totaleAvere;
                } elseif (trim($row[Conto::TIP_CONTO]) == "Avere") {
                    $saldo = $totaleAvere - $totaleDare;
                }

                /**
                 * Evidenzia la riga se il saldo è negativo
                 */
                if ($saldo < 0) {
                    $class = "class='bg-danger'";
                }

                $tbody .= "<tr>" .
                        "	<td>" . $row[Registrazione::DAT_REGISTRAZIONE] . "</td>" .
                        "	<td>" . date("d/m/Y", strtotime($row[Registrazione::DAT_REGISTRAZIONE])) . "</td>" .
                        "	<td>" . trim($row[Registrazione::DES_REGISTRAZIONE]) . "</td>" .
                        "	<td>" . $impDare . "</td>" .
                        "	<td>" . $impAvere . "</td>" .
                        "	<td " . $class . ">" . number_format(round($saldo, 2), 2, ',', '.') . "</td>" .
                        "</tr>";
            }

            /**
             * Aggiunto una riga di totalizzazione per le colonna Dare e Avere
             */
            $tbody .= "<tr>" .
                    "	<td></td>" .
                    "	<td></td>" .
                    "	<td></td>" .
                    "	<td>" . number_format(round($totaleDare, 2), 2, ',', '.') . "</td>" .
                    "	<td>" . number_format(round($totaleAvere, 2), 2, ',', '.') . "</td>" .
                    "	<td " . $class . ">" . number_format(round($saldo, 2), 2, ',', '.') . "</td>" .
                    "</tr>" .
                    "</tbody>";
        } else {
            $thead = "<thead>" .
                    "	<tr class='bg-warning'>" .
                    "		<th>Nessun movimento trovato per il conto</th>" .
                    "	</tr>" .
                    "</thead>";
            $tbody = "<tbody></tbody>";
        }
        return $thead . $tbody;
    }

    // Getters e Setters ---------------------------------------------------

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    public function getMessaggio() {
        return self::$messaggio;
    }

}