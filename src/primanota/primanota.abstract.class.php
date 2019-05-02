<?php

require_once 'nexus6.abstract.class.php';
require_once 'primanota.presentation.interface.php';
require_once 'lavoroPianificato.class.php';

abstract class PrimanotaAbstract extends Nexus6Abstract implements PrimanotaPresentationInterface {

    // Bottoni

    const AGGIUNGI_FATTURA_PAGATA_HREF = "<a onclick='aggiungiFatturaPagata(";
    const AGGIUNGI_FATTURA_INCASSATA_HREF = "<a onclick='aggiungiFatturaIncassata(";
    const RIMUOVI_FATTURA_PAGATA_HREF = "<a onclick='rimuoviFatturaPagata(";
    const RIMUOVI_FATTURA_INCASSATA_HREF = "<a onclick='rimuoviFatturaIncassata(";
    const CANCELLA_DETTAGLIO_NUOVA_REGISTRAZIONE_HREF = '<a onclick=cancellaDettaglioNuovaRegistrazione(';
    const CANCELLA_DETTAGLIO_NUOVO_CORRISPETTIVO_HREF = '<a onclick=cancellaDettaglioNuovoCorrispettivo(';

    // Query ---------------------------------------------------------------

    public static $queryCreaRegistrazione = "/primanota/creaRegistrazione.sql";
    public static $queryUpdateScadenza = "/primanota/updateScadenza.sql";
    public static $queryUpdateScadenzaCliente = "/primanota/updateScadenzaCliente.sql";
    public static $queryDissociaPagamento = "/primanota/dissociaPagamento.sql";
    public static $queryDissociaIncasso = "/primanota/dissociaIncasso.sql";
    public static $queryDeleteScadenzaRegistrazione = "/primanota/deleteScadenzaRegistrazione.sql";
    public static $queryCreaScadenzaCliente = "/primanota/creaScadenzaCliente.sql";
    public static $queryLeggiDettagliRegistrazione = "/primanota/leggiDettagliRegistrazione.sql";
    public static $queryLeggiScadenzeRegistrazione = "/primanota/leggiScadenzeRegistrazione.sql";
    public static $queryUpdateRegistrazione = "/primanota/updateRegistrazione.sql";
    public static $queryUpdateStatoRegistrazione = "/primanota/updateStatoRegistrazione.sql";
    public static $queryDeleteDettaglioRegistrazione = "/primanota/deleteDettaglioRegistrazione.sql";
    public static $queryLeggiScadenzeAperteFornitore = "/primanota/ricercaScadenzeAperteFornitore.sql";
    public static $queryLeggiScadenzeFornitore = "/primanota/ricercaScadenzeFornitore.sql";
    public static $queryLeggiScadenzeCliente = "/primanota/ricercaScadenzeCliente.sql";
    public static $queryPrelevaScadenzaCliente = "/primanota/leggiScadenzaCliente.sql";
    public static $queryPrelevaScadenzaFornitore = "/primanota/leggiScadenzaFornitore.sql";
    public static $queryScadenzaFornitore = "/primanota/scadenzaFornitore.sql";
    public static $queryScadenzaCliente = "/primanota/scadenzaCliente.sql";
    public static $queryUpdateStatoScadenza = "/primanota/updateStatoScadenzaFornitore.sql";
    public static $queryUpdateStatoScadenzaCliente = "/primanota/updateStatoScadenzaCliente.sql";
    public static $queryDeleteScadenza = "/primanota/deleteScadenza.sql";
    public static $queryDeleteScadenzaCliente = "/primanota/deleteScadenzaCliente.sql";
    public static $queryPrelevaRegistrazioneOriginaleCliente = "/primanota/leggiRegistrazioneOriginaleCliente.sql";
    public static $queryPrelevaRegistrazioneOriginaleFornitore = "/primanota/leggiRegistrazioneOriginaleFornitore.sql";
    public static $queryTrovaScadenzaFornitore = "/primanota/trovaScadenzaFornitore.sql";
    public static $queryTrovaScadenzaCliente = "/primanota/trovaScadenzaCliente.sql";
    public static $queryPrelevaCapocontoFornitore = "/primanota/ricercaCapocontoFornitore.sql";
    public static $queryPrelevaCapocontoCliente = "/primanota/ricercaCapocontoCliente.sql";
    // Altri campi

    public static $messaggio;
    public static $ggMese = array(
        '01' => '31',
        '02' => '28',
        '03' => '31',
        '04' => '30',
        '05' => '31',
        '06' => '30',
        '07' => '31',
        '08' => '31',
        '09' => '30',
        '10' => '31',
        '11' => '30',
        '12' => '31',
    );

    // Metodi comuni di utilita della prima note ---------------------------

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {
        
    }

    public function refreshTabellaFattureDaPagare($scadenzaFornitore) {
        $thead = "";
        $tbody = "";
        $tableIsNotEmpty = false;

        $tbody = "<tbody>";

        foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenzaDaPagare) {
            $parms = $unaScadenzaDaPagare[ScadenzaFornitore::ID_SCADENZA] . ',"' . $scadenzaFornitore->getIdTableScadenzeAperte() . '","' . $scadenzaFornitore->getIdTableScadenzeChiuse() . '"';
            $bottoneAggiungiFatturaPagata = self::AGGIUNGI_FATTURA_PAGATA_HREF . $parms . self::INCLUDI_ICON;

            $fatturaPagataNotExist = true;
            foreach ($scadenzaFornitore->getScadenzePagate() as $unaScadenzaPagata) {
                if (trim($unaScadenzaDaPagare[ScadenzaFornitore::NUM_FATTURA]) == trim($unaScadenzaPagata[ScadenzaFornitore::NUM_FATTURA])
                        and ( trim($unaScadenzaDaPagare[ScadenzaFornitore::DAT_SCADENZA]) == trim($unaScadenzaPagata[ScadenzaFornitore::DAT_SCADENZA]))) {
                    $fatturaPagataNotExist = false;
                    break;
                }
            }
            /**
             * Vengono escluse tutte le scadenze aggiunte alla tabella della pagate
             */
            if ($fatturaPagataNotExist) {
                $tableIsNotEmpty = true;
                $tbody .= "<tr>" .
                        "	<td id='icons'>" . $bottoneAggiungiFatturaPagata . "</td>" .
                        "	<td>" . $unaScadenzaDaPagare[ScadenzaFornitore::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaDaPagare[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
                        "	<td align='center'>" . $unaScadenzaDaPagare[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
                        "	<td>" . $unaScadenzaDaPagare[ScadenzaFornitore::NOTA_SCADENZA] . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";

        if ($tableIsNotEmpty) {
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='20'>&nbsp;</th>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "	</tr>" .
                    "</thead>";
        }
        return "<table id='" . $scadenzaFornitore->getIdTableScadenzeAperte() . "' class='result' style='width: 100%'>" . $thead . $tbody . "</table>";
    }

    public function refreshTabellaFattureDaIncassare($scadenzaCliente) {
        $thead = "";
        $tbody = "";
        $tableIsNotEmpty = false;

        $tbody = "<tbody>";

        foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenzaDaIncassare) {
            $parms = $unaScadenzaDaIncassare[ScadenzaCliente::ID_SCADENZA] . ',"' . $scadenzaCliente->getIdTableScadenzeAperte() . '","' . $scadenzaCliente->getIdTableScadenzeChiuse() . '"';
            $bottoneAggiungiFatturaIncassata = self::AGGIUNGI_FATTURA_INCASSATA_HREF . $parms . self::INCLUDI_ICON;

            $fatturaIncassataNotExist = true;
            foreach ($scadenzaCliente->getScadenzeIncassate() as $unaScadenzaIncassata) {
                if (trim($unaScadenzaDaIncassare[ScadenzaCliente::NUM_FATTURA]) == trim($unaScadenzaIncassata[ScadenzaCliente::NUM_FATTURA])
                        and ( trim($unaScadenzaDaIncassare[ScadenzaCliente::DAT_REGISTRAZIONE]) == trim($unaScadenzaIncassata[ScadenzaCliente::DAT_REGISTRAZIONE]))) {
                    $fatturaIncassataNotExist = false;
                    break;
                }
            }
            /**
             * Vengono escluse tutte le scadenze aggiunte alla tabella della incassate
             */
            if ($fatturaIncassataNotExist) {
                $tableIsNotEmpty = true;
                $tbody .= "<tr>" .
                        "	<td>" . $bottoneAggiungiFatturaIncassata . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::DAT_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::IMP_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::NOTA] . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";

        if ($tableIsNotEmpty) {
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='20'>&nbsp;</th>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "	</tr>" .
                    "</thead>";
        }
        return "<table id='" . $scadenzaCliente->getIdTableScadenzeAperte() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaFattureDaPagare($scadenzaFornitore) {
        $thead = "";
        $tbody = "";
        $tableIsNotEmpty = false;

        $tbody = "<tbody>";

        foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenzaDaPagare) {
            $parms = $unaScadenzaDaPagare[ScadenzaFornitore::ID_SCADENZA] . ',"' . $scadenzaFornitore->getIdTableScadenzeAperte() . '","' . $scadenzaFornitore->getIdTableScadenzeChiuse() . '"';
            $bottoneAggiungiFatturaPagata = self::AGGIUNGI_FATTURA_PAGATA_HREF . $parms . self::INCLUDI_ICON;

            $fatturaPagataNotExist = true;
            foreach ($scadenzaFornitore->getScadenzePagate() as $unaScadenzaPagata) {
                if (trim($unaScadenzaDaPagare[ScadenzaFornitore::NUM_FATTURA]) == trim($unaScadenzaPagata[ScadenzaFornitore::NUM_FATTURA])
                        and ( trim($unaScadenzaDaPagare[ScadenzaFornitore::DAT_SCADENZA]) == trim($unaScadenzaPagata[ScadenzaFornitore::DAT_SCADENZA]))) {
                    $fatturaPagataNotExist = false;
                    break;
                }
            }
            /**
             * Vengono escluse tutte le scadenze aggiunte alla tabella delle pagate
             */
            if ($fatturaPagataNotExist) {
                $tableIsNotEmpty = true;
                $tbody .= "<tr>" .
                        "	<td>" . $bottoneAggiungiFatturaPagata . "</td>" .
                        "	<td>" . $unaScadenzaDaPagare[ScadenzaFornitore::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaDaPagare[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
                        "	<td>" . $unaScadenzaDaPagare[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
                        "	<td>" . $unaScadenzaDaPagare[ScadenzaFornitore::NOTA_SCADENZA] . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";

        if ($tableIsNotEmpty) {
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='20'>&nbsp;</th>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "	</tr>" .
                    "</thead>";
        }
        return "<table id='" . $scadenzaFornitore->getIdTableScadenzeAperte() . "' class='table table-bordered table-hover'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaFatturePagate($scadenzaFornitore) {
        $thead = "";
        $tbody = "";

        if ($scadenzaFornitore->getQtaScadenzePagate() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "		<th width='20'>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaFornitore->getScadenzePagate() as $unaScadenzaPagata) {
                $parms = $unaScadenzaPagata[ScadenzaFornitore::ID_SCADENZA] . ',"' . $scadenzaFornitore->getIdTableScadenzeAperte() . '","' . $scadenzaFornitore->getIdTableScadenzeChiuse() . '"';
                $bottoneRimuoviFatturaPagata = self::RIMUOVI_FATTURA_PAGATA_HREF . $parms . self::ESCLUDI_ICON;

                $tbody .= "<tr>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::NOTA_SCADENZA] . "</td>" .
                        "	<td>" . $bottoneRimuoviFatturaPagata . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return "<table id='" . $scadenzaFornitore->getIdTableScadenzeChiuse() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaFattureDaIncassare($scadenzaCliente) {
        $thead = "";
        $tbody = "";
        $tableIsNotEmpty = false;

        $tbody = "<tbody>";

        foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenzaDaIncassare) {
            $parms = $unaScadenzaDaIncassare[ScadenzaCliente::ID_SCADENZA] . ',"' . $scadenzaCliente->getIdTableScadenzeAperte() . '","' . $scadenzaCliente->getIdTableScadenzeChiuse() . '"';
            $bottoneAggiungiFatturaIncassata = self::AGGIUNGI_FATTURA_INCASSATA_HREF . $parms . self::INCLUDI_ICON;

            $fatturaIncassataNotExist = true;
            foreach ($scadenzaCliente->getScadenzeIncassate() as $unaScadenzaIncassata) {
                if (trim($unaScadenzaDaIncassare[ScadenzaCliente::NUM_FATTURA]) == trim($unaScadenzaIncassata[ScadenzaCliente::NUM_FATTURA])
                        and ( trim($unaScadenzaDaIncassare[ScadenzaCliente::DAT_REGISTRAZIONE]) == trim($unaScadenzaIncassata[ScadenzaCliente::DAT_REGISTRAZIONE]))) {
                    $fatturaIncassateNotExist = false;
                    break;
                }
            }
            /**
             * Vengono escluse tutte le scadenze aggiunte alla tabella delle incassate
             */
            if ($fatturaIncassataNotExist) {
                $tableIsNotEmpty = true;
                $tbody .= "<tr>" .
                        "	<td>" . $bottoneAggiungiFatturaIncassata . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::DAT_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::IMP_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaDaIncassare[ScadenzaCliente::NOTA] . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";

        if ($tableIsNotEmpty) {
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='20'>&nbsp;</th>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "	</tr>" .
                    "</thead>";
        }
        return "<table id='" . $scadenzaCliente->getIdTableScadenzeAperte() . "' class='table table-bordered table-hover'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaFattureIncassate($scadenzaCliente) {
        $thead = "";
        $tbody = "";

        if ($scadenzaCliente->getQtaScadenzeIncassate() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "		<th width='20'>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaCliente->getScadenzeIncassate() as $unaScadenzaIncassata) {
                $parms = $unaScadenzaIncassata[ScadenzaCliente::ID_SCADENZA] . ',"' . $scadenzaCliente->getIdTableScadenzeAperte() . '","' . $scadenzaCliente->getIdTableScadenzeChiuse() . '"';
                $bottoneRimuoviFatturaIncassata = self::RIMUOVI_FATTURA_INCASSATA_HREF . $parms . self::ESCLUDI_ICON;

                $tbody .= "<tr>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::DAT_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::IMP_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::NOTA] . "</td>" .
                        "	<td>" . $bottoneRimuoviFatturaIncassata . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return "<table id='" . $scadenzaCliente->getIdTableScadenzeChiuse() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaReadOnlyFattureIncassate($scadenzaCliente) {
        $thead = "";
        $tbody = "";

        if ($scadenzaCliente->getQtaScadenzeIncassate() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaCliente->getScadenzeIncassate() as $unaScadenzaIncassata) {
                $tbody .= "<tr>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::DAT_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::IMP_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unaScadenzaIncassata[ScadenzaCliente::NOTA] . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return "<table id='" . $scadenzaCliente->getIdTableScadenzeChiuse() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaReadOnlyFatturePagate($scadenzaFornitore) {
        $thead = "";
        $tbody = "";

        if ($scadenzaFornitore->getQtaScadenzePagate() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='50'>Num.Fat.</th>" .
                    "		<th width='50'>Data</th>" .
                    "		<th width='50' align='center'>Importo</th>" .
                    "		<th width='200'>Nota</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaFornitore->getScadenzePagate() as $unaScadenzaPagata) {
                $tbody .= "<tr>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::NUM_FATTURA] . "</td>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
                        "	<td>" . $unaScadenzaPagata[ScadenzaFornitore::NOTA_SCADENZA] . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
        }
        return "<table id='" . $scadenzaFornitore->getIdTableScadenzeChiuse() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaDettagliRegistrazione($registrazione, $dettaglioRegistrazione) {
        $thead = "";
        $tbody = "";

        if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='500'>Conto</th>" .
                    "		<th width='180'>Importo</th>" .
                    "		<th width='100'>Segno</th>" .
                    "		<th width='50'>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                $contoComposto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
                $codConto = explode(".", $contoComposto[0]);
                $segno = $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE];

                $cancella_parms = "'" . $dettaglioRegistrazione->getIdTablePagina() . "',";
                $cancella_parms .= trim($contoComposto[0]) . ",'";
                $cancella_parms .= $segno . "'";

                $bottoneCancella = self::CANCELLA_DETTAGLIO_NUOVA_REGISTRAZIONE_HREF . $cancella_parms . self::CANCELLA_ICON;

                $idImportoDettaglio = " id='importo" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "' ";
                $idSegnoDettaglio = " id='segno" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "' ";

                $modifica_parms = "'" . $dettaglioRegistrazione->getIdTablePagina() . "',";
                $modifica_parms .= trim($codConto[0]) . ",";
                $modifica_parms .= trim($codConto[1]) . ",";
                $modifica_parms .= "$('#importo" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "').val()" . ",";
                $modifica_parms .= "$('#segno" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "').val()" . ",";
                $modifica_parms .= trim($unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE]);

                if (parent::isNotEmpty($registrazione->getIdFornitore())) {
                    $onBlurImporto = ' onblur=' . '"modificaDettaglioRegistrazione(' . $modifica_parms . '); ' . 'ripartisciImportoSuScadenzeFornitore(this.value)"';                    
                }                
                elseif (parent::isNotEmpty($registrazione->getIdCliente())) {
                    $onBlurImporto = ' onblur=' . '"modificaDettaglioRegistrazione(' . $modifica_parms . '); ' . 'ripartisciImportoSuScadenzeCliente(this.value)"';                    
                }                

                $onBlurSegno = ' onblur=' . '"' . 'modificaDettaglioRegistrazione(' . $modifica_parms . ')"';
                
                $tbody .= "<tr>" .
                        "	<td>" . $unDettaglio[DettaglioRegistrazione::COD_CONTO] . "</td>" .
                        "	<td>" .
                        "       <div class='input-group'>" .
                        "           <span class='input-group-addon'><span class='glyphicon glyphicon-euro'></span></span>" .
                        "		    <input class='form-control' type='text' maxlength='10'" . $idImportoDettaglio . $onBlurImporto . " value='" . $unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE] . "'></input>" .
                        "       </div>" .
                        "	</td>" .
                        "	<td>" .
                        "       <div class='input-group'>" .
                        "           <span class='input-group-addon'>D/A</span>" .
                        "		    <input class='form-control' type='text' maxlength='1'" . $idSegnoDettaglio . $onBlurSegno . " value='" . $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE] . "'></input>" .
                        "       </div>" .
                        "	</td>" .
                        "	<td id='icons'>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
            return "<table id='" . $dettaglioRegistrazione->getIdTablePagina() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
        }
        return "";
    }

    public function makeTabellaDettagliCorrispettivo($registrazione, $dettaglioRegistrazione) {
        $thead = "";
        $tbody = "";

        if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='500'>Conto</th>" .
                    "		<th width='180'>Importo</th>" .
                    "		<th width='100'>Segno</th>" .
                    "		<th width='50'>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                $contoComposto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO_COMPOSTO]);
                $codConto = explode(".", $contoComposto[0]);
                $segno = $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE];

                $cancella_parms = "'" . $dettaglioRegistrazione->getIdTablePagina() . "',";
                $cancella_parms .= trim($contoComposto[0]) . ",'";
                $cancella_parms .= $segno . "'";

                $bottoneCancella = self::CANCELLA_DETTAGLIO_NUOVO_CORRISPETTIVO_HREF . $cancella_parms . self::CANCELLA_ICON;

                $idImportoDettaglio = " id='importo" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "' ";
                $idSegnoDettaglio = " id='segno" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "' ";

                $modifica_parms = "'" . $dettaglioRegistrazione->getIdTablePagina() . "',";
                $modifica_parms .= trim($codConto[0]) . ",";
                $modifica_parms .= trim($codConto[1]) . ",";
                $modifica_parms .= "$('#importo" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "').val()" . ",";
                $modifica_parms .= "$('#segno" . trim($codConto[0]) . trim($codConto[1]) . trim($segno) . "').val()" . ",";
                $modifica_parms .= trim($unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE]);

                $onBlurImporto = ' onblur=' . '"modificaDettaglioCorrispettivo(' . $modifica_parms . ')"';                    
                $onBlurSegno   = ' onblur=' . '"modificaDettaglioCorrispettivo(' . $modifica_parms . ')"';
                
                $tbody .= "<tr>" .
                        "	<td>" . $unDettaglio[DettaglioRegistrazione::COD_CONTO_COMPOSTO] . "</td>" .
                        "	<td>" .
                        "       <div class='input-group'>" .
                        "           <span class='input-group-addon'><span class='glyphicon glyphicon-euro'></span></span>" .
                        "		    <input class='form-control' type='text' maxlength='10'" . $idImportoDettaglio . $onBlurImporto . " value='" . $unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE] . "'></input>" .
                        "       </div>" .
                        "	</td>" .
                        "	<td>" .
                        "       <div class='input-group'>" .
                        "           <span class='input-group-addon'>D/A</span>" .
                        "		    <input class='form-control' type='text' maxlength='1'" . $idSegnoDettaglio . $onBlurSegno . " value='" . $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE] . "'></input>" .
                        "       </div>" .
                        "	</td>" .
                        "	<td id='icons'>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
            return "<table id='" . $dettaglioRegistrazione->getIdTablePagina() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
        }
        return "";
    }

    public function makeTabellaReadOnlyDettagliRegistrazione($dettaglioRegistrazione) {
        $thead = "";
        $tbody = "";

        if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='500'>Conto</th>" .
                    "		<th width='180'>Importo</th>" .
                    "		<th width='100'>Segno</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                $tbody .= "<tr>" .
                        "	<td>" . $unDettaglio[DettaglioRegistrazione::COD_CONTO] . "</td>" .
                        "	<td>" . $unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE] . "</td>" .
                        "	<td>" . $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE] . "</td>" .
                        "</tr>";
            }
            $tbody .= "</tbody>";
            return "<table id='" . $dettaglioRegistrazione->getIdTablePagina() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
        }
        return "";
    }

    public function makeTabellaScadenzeFornitore($scadenzaFornitore) {
        $data_ko = "class='bg-danger'";
        $data_ok = "class='bg-success'";

        $thead = "";
        $tbody = "";

        if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "   <tr>" .
                    "       <th width='180'>Data</th>" .
                    "       <th width='100'>Stato</th>" .
                    "       <th width='180'>Importo</th>" .
                    "       <th width='60'>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza) {
                $idFornitore = $unaScadenza[ScadenzaFornitore::ID_FORNITORE];
                $dataScadenza = strtotime(str_replace('/', '-', $unaScadenza[ScadenzaFornitore::DAT_SCADENZA]));       // cambio i separatori altrimenti la strtotime non funziona
                $numFatt = $unaScadenza[ScadenzaFornitore::NUM_FATTURA];

                $modifica_parms = "'" . $scadenzaFornitore->getIdTableScadenzeAperte() . "',";
                $modifica_parms .= trim($idFornitore) . ",";
                $modifica_parms .= trim($dataScadenza) . ",";
                $modifica_parms .= trim($numFatt) . ",";
                $modifica_parms .= "this.value";

                $mod_data_parms = "'" . $scadenzaFornitore->getIdTableScadenzeAperte() . "',";
                $mod_data_parms .= trim($idFornitore) . ",";
                $mod_data_parms .= trim($dataScadenza) . ",";
                $mod_data_parms .= "this.value,";
                $mod_data_parms .= trim($numFatt);

                $onModifyImporto = " onblur=" . '"' . "modificaImportoScadenzaFornitore(" . $modifica_parms . ')"';
                $onModifyData = " onchange=" . '"' . "modificaDataScadenzaFornitore(" . $mod_data_parms . ')"';
                $onClickCancella = " onclick=" . '"' . "cancellaNuovaScadenzaFornitore(" . $modifica_parms;
                $idImportoDettaglio = " id='impscad" . trim($idFornitore) . trim($dataScadenza) . trim($numFatt) . "' ";
                $idDataDettaglio = " id='datscad" . trim($idFornitore) . trim($dataScadenza) . trim($numFatt) . "' ";

                $stato = ($unaScadenza[ScadenzaFornitore::STA_SCADENZA] == "10") ? "Pagata" : "Da Pagare";

                $tbody .= "<tr>" ;
                
                if ($stato == "Da Pagare") {
                    $tdclass = $data_ko;
                    $bottoneCancella = "<a" . $onClickCancella . ')"><span class="glyphicon glyphicon-trash"></span></a>';

                    $tbody .= "" .
                            "   <td>" .
                            "       <div class='input-group' id='datareg_cre_control_group'>" .
                            "           <div class='input-group date' data-provide='datepicker' data-date-format='dd-mm-yyyy' data-date-autoclose='true' data-date-today-highlight='true'>" .
                            "               <input type='text' class='form-control'" . $idDataDettaglio . $onModifyData .  "value='" . $unaScadenza[ScadenzaFornitore::DAT_SCADENZA] . "'></input>" .
                            "               <span class='input-group-addon'>" .
                            "                   <span class='glyphicon glyphicon-calendar'></span>" .
                            "               </span>" .
                            "           </div>" .
                            "       </div>" .
                            "   </td>" .
                            "	<td " . $tdclass . ">" . $stato . "</td>" .
                            "	<td>" .
                            "       <div class='input-group'>" .
                            "           <span class='input-group-addon'><span class='glyphicon glyphicon-euro'></span></span>" .
                            "		    <input class='form-control' type='text' maxlength='10' " . $idImportoDettaglio . $onModifyImporto . "value='" . $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] . "'></input>" .
                            "       </div>" .
                            "	</td>";
                }
                else {
                    $tdclass = $data_ok;
                    $bottoneCancella = self::OK_ICON;
                    $tbody .= "" .
                            "	<td>" . $unaScadenza[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
                            "	<td " . $tdclass . ">" . $stato . "</td>" .
                            "	<td>" . $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" ;
                }
                $tbody .= "" .
                        "	<td>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";
        return "<table id='" . $scadenzaFornitore->getIdTableScadenzeAperte() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaReadOnlyScadenzeFornitore($scadenzaFornitore) {
        $data_ko = "class='bg-danger'";
        $data_ok = "class='bg-success'";

        $thead = "";
        $tbody = "";

        if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='180'>Data</th>" .
                    "		<th width='100'>Stato</th>" .
                    "		<th width='180'>Importo</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza) {
                $stato = ($unaScadenza[ScadenzaFornitore::STA_SCADENZA] == "10") ? "Pagata" : "Da Pagare";

                if ($stato == "Da Pagare")
                    $tdclass = $data_ko;
                else
                    $tdclass = $data_ok;

                $tbody .= "<tr>" .
                        "	<td>" . $unaScadenza[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
                        "	<td " . $tdclass . ">" . $stato . "</td>" .
                        "	<td>" . $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";
        return "<table id='" . $scadenzaFornitore->getIdTableScadenzeAperte() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaScadenzeCliente($scadenzaCliente) {
        $data_ko = "class='bg-danger'";
        $data_ok = "class='bg-success'";

        $thead = "";
        $tbody = "";

            if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "       <th width='180'>Data</th>" .
                    "       <th width='100'>Stato</th>" .
                    "       <th width='180'>Importo</th>" .
                    "       <th width='60'>&nbsp;</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza) {
                $idCliente = $unaScadenza[ScadenzaCliente::ID_CLIENTE];
                $dataScadenza = strtotime(str_replace('/', '-', $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]));
                $numFatt = $unaScadenza[ScadenzaCliente::NUM_FATTURA];

                $modifica_parms = "'" . $scadenzaCliente->getIdTableScadenzeAperte() . "',";
                $modifica_parms .= trim($idCliente) . ",";
                $modifica_parms .= trim($dataScadenza) . ",";
                $modifica_parms .= trim($numFatt) . ",";
                $modifica_parms .= "this.value";
                
                $mod_data_parms = "'" . $scadenzaCliente->getIdTableScadenzeAperte() . "',";
                $mod_data_parms .= trim($idCliente) . ",";
                $mod_data_parms .= trim($dataScadenza) . ",";
                $mod_data_parms .= "this.value,";
                $mod_data_parms .= trim($numFatt);

                $onModifyImporto = " onblur=" . '"' . "modificaImportoScadenzaCliente(" . $modifica_parms . ')"';
                $onModifyData = " onchange=" . '"' . "modificaDataScadenzaCliente(" . $mod_data_parms . ')"';                
                $onClickCancella = " onclick=" . '"' . "cancellaNuovaScadenzaCliente(" . $modifica_parms;
                $idImportoDettaglio = " id='impscad" . trim($idCliente) . trim($dataScadenza) . trim($numFatt) . "' ";
                $idDataDettaglio = " id='datscad" . trim($idCliente) . trim($dataScadenza) . trim($numFatt) . "' ";

                $stato = ($unaScadenza[ScadenzaCliente::STA_SCADENZA] == "10") ? "Incassata" : "Da Incassare";

                $tbody .= "<tr>" ;
//                        "	<td>" . $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] . "</td>" ;
                
                if ($stato == "Da Incassare") {
                    $tdclass = $data_ko;
                    $bottoneCancella = "<a" . $onClickCancella . ')"><span class="glyphicon glyphicon-trash"></span></a>';
                    $tbody .= "" .
                            "   <td>" .
                            "       <div class='input-group' id='datareg_cre_control_group'>" .
                            "           <div class='input-group date' data-provide='datepicker' data-date-format='dd-mm-yyyy' data-date-autoclose='true' data-date-today-highlight='true'>" .
                            "               <input type='text' class='form-control'" . $idDataDettaglio . $onModifyData .  "value='" . $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] . "'></input>" .
                            "               <span class='input-group-addon'>" .
                            "                   <span class='glyphicon glyphicon-calendar'></span>" .
                            "               </span>" .
                            "           </div>" .
                            "       </div>" .
                            "   </td>" .                            
                            "	<td " . $tdclass . ">" . $stato . "</td>" .
                            "	<td>" .
                            "       <div class='input-group'>" .
                            "           <span class='input-group-addon'><span class='glyphicon glyphicon-euro'></span></span>" .
                            "			<input class='form-control' type='text' maxlength='10' " . $idImportoDettaglio . $onModifyImporto . "value='" . $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE] . "'></input>" .
                            "       </div>" .
                            "	</td>";
                }
                else {
                    $tdclass = $data_ok;
                    $bottoneCancella = self::OK_ICON;
                    $tbody .= "" .
                            "	<td " . $tdclass . ">" . $stato . "</td>" .
                            "	<td>" . $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE] . "</td>";                    
                }
                $tbody .= "" .
                        "	<td>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";
        return "<table id='" . $scadenzaCliente->getIdTableScadenzeAperte() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function makeTabellaReadOnlyScadenzeCliente($scadenzaCliente) {
        $data_ko = "class='bg-danger'";
        $data_ok = "class='bg-success'";

        $thead = "";
        $tbody = "";

        if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0) {

            $tbody = "<tbody>";
            $thead = "<thead>" .
                    "	<tr>" .
                    "		<th width='180'>Data</th>" .
                    "		<th width='100'>Stato</th>" .
                    "		<th width='180'>Importo</th>" .
                    "	</tr>" .
                    "</thead>";

            foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza) {
                $stato = ($unaScadenza[ScadenzaCliente::STA_SCADENZA] == "10") ? "Incassata" : "Da Incassare";

                if ($stato == "Da Incassare")
                    $tdclass = $data_ko;
                else
                    $tdclass = $data_ok;

                $tbody .= "<tr>" .
                        "	<td>" . $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] . "</td>" .
                        "	<td " . $tdclass . ">" . $stato . "</td>" .
                        "	<td>" . $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE] . "</td>" .
                        "</tr>";
            }
        }
        $tbody .= "</tbody>";
        return "<table id='" . $scadenzaCliente->getIdTableScadenzeAperte() . "' class='table table-bordered'>" . $thead . $tbody . "</table>";
    }

    public function calcolaDataScadenza($data, $numGiorniScadenza) {
        /**
         * Se i giorni scadenza fattura del fornitore sono = 0 non viene calcolata da data scadenza
         */
        if ($numGiorniScadenza > 0) {
            /**
             * Le data di registrazione viene aumentata dei giorni configurati per il fornitore,
             * alla data ottenuta viene sostituito il giorno con l'ultimo giorno del mese corrispondente
             */
            $dataScadenza = $this->sommaGiorniData($data, "-", $numGiorniScadenza);
            
            $data = explode("-", $dataScadenza);
            $mese = $data[1];
            $anno = $data[2];            
            return SELF::$ggMese[$mese] . "-" . $mese . "-" . $anno;
        } else
            return "";
    }

    public function aggiungiDettagliCorrispettivoNegozio($db, $utility, $array) {
        
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->setIdDettaglioRegistrazione(0);
        $dettaglioRegistrazione->setIdRegistrazione(0);
        $sottoconto = Sottoconto::getInstance();

        /**
         * Dettaglio sul conto selezionato
         */
        $_cc = explode(".", $dettaglioRegistrazione->getCodConto());
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);

        $dettaglioRegistrazione->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setCodConto($_cc[0]);
        $dettaglioRegistrazione->setCodSottoconto($_cc[1]);
        $dettaglioRegistrazione->setIndDareAvere("D");
        $dettaglioRegistrazione->aggiungi();

        /**
         * Dettaglio conto erario
         */
        $_cc = explode(".", $array['contoErarioNegozi']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $dettaglioRegistrazione->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setCodConto($_cc[0]);
        $dettaglioRegistrazione->setCodSottoconto($_cc[1]);
        $dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImpIva());
        $dettaglioRegistrazione->setIndDareAvere("A");
        $dettaglioRegistrazione->aggiungi();

        /**
         * Dettaglio Cassa/Banca
         */
        $_cc = explode(".", $array['contoCorrispettivoNegozi']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $dettaglioRegistrazione->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setCodConto($_cc[0]);
        $dettaglioRegistrazione->setCodSottoconto($_cc[1]);
        $dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImponibile());
        $dettaglioRegistrazione->setIndDareAvere("A");
        $dettaglioRegistrazione->aggiungi();

        return $dettaglioRegistrazione;
    }

    public function aggiungiDettagliCorrispettivoMercato($db, $utility, $array) {
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $dettaglioRegistrazione->setIdDettaglioRegistrazione(0);
        $dettaglioRegistrazione->setIdRegistrazione(0);
        $sottoconto = Sottoconto::getInstance();

        /**
         * Dettaglio sul conto selezionato
         */
        $_cc = explode(".", $dettaglioRegistrazione->getCodConto());
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);

        $dettaglioRegistrazione->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setIndDareAvere("D");
        $dettaglioRegistrazione->aggiungi();

        /**
         * Dettaglio conto erario
         */
        $_cc = explode(".", $array['contoErarioMercati']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $dettaglioRegistrazione->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setCodConto($_cc[0]);
        $dettaglioRegistrazione->setCodSottoconto($_cc[1]);
        $dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImpIva());
        $dettaglioRegistrazione->setIndDareAvere("A");
        $dettaglioRegistrazione->aggiungi();

        /**
         * Dettaglio Cassa/Banca
         */
        $_cc = explode(".", $array['contoCorrispettivoMercati']);
        $sottoconto->setCodConto($_cc[0]);
        $sottoconto->setCodSottoconto($_cc[1]);
        $sottoconto->leggi($db);
        $sottoconto->searchSottoconto($_cc[1]);
        
        $dettaglioRegistrazione->setCodContoComposto($sottoconto->getCodConto() . "." . $sottoconto->getCodSottoconto() . " - " . $sottoconto->getDesSottoconto());
        $dettaglioRegistrazione->setCodConto($_cc[0]);
        $dettaglioRegistrazione->setCodSottoconto($_cc[1]);
        $dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImponibile());
        $dettaglioRegistrazione->setIndDareAvere("A");
        $dettaglioRegistrazione->aggiungi();

        return $dettaglioRegistrazione;
    }

    public function creaCorrispettivo($utility, $registrazione, $dettaglioRegistrazione) {
        $db = Database::getInstance();
        $db->beginTransaction();
        $dettagli_ok = true;

        if ($registrazione->inserisci($db)) {

            foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                if ($this->creaDettaglioCorrispettivonegozio($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)) {
                    
                } else {
                    $dettagli_ok = false;
                    break;
                }
            }

            /** 
             * Ricalcolo i saldi dei conti
             */
            if ($dettagli_ok) {
                $this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
                $db->commitTransaction();
                return true;
            } else {
                $db->rollbackTransaction();
                return false;
            }
        } else {
            $db->rollbackTransaction();
            return false;
        }
    }

    public function aggiornaCorrispettivo($utility, $registrazione, $dettaglioRegistrazione) {
        $db = Database::getInstance();
        $db->beginTransaction();
        $array = $utility->getConfig();

        if ($registrazione->aggiorna($db)) {
            if ($this->aggiornaDettagli($db, $utility, $registrazione, $dettaglioRegistrazione)) {
                $this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
                $db->commitTransaction();
                return true;
            } else {
                $db->rollbackTransaction();
                return false;
            }
        } else {
            $db->rollbackTransaction();
            return false;
        }
    }

    public function creaDettaglioCorrispettivonegozio($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio) {
        $_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]); // il codconto del dettaglio contiene anche la descrizione
        $conto = explode(".", $_cc[0]);  // conto e sottoconto separati da un punto

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->setCodConto($conto[0]);
        $dettaglioRegistrazione->setCodSottoconto($conto[1]);
        $dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
        $dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);

        if (!$dettaglioRegistrazione->inserisci($db)) {
            $db->rollbackTransaction();
            return false;
        }
        return true;
    }

    public function aggiornaDettagli($db, $utility, $registrazione, $dettaglioRegistrazione) {
        foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
            if ($unDettaglio[DettaglioRegistrazione::ID_REGISTRAZIONE] == 0) {
                $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
                $dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
                $dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);

                $_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
                $conto = explode(".", $_cc[0]);

                $dettaglioRegistrazione->setCodConto($conto[0]);
                $dettaglioRegistrazione->setCodSottoconto($conto[1]);

                if ($dettaglioRegistrazione->inserisci($db)) {
                    
                }  // tutto ok
                else
                    return false;
            }
            else {
                $dettaglioRegistrazione->setIdDettaglioRegistrazione($unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE]);
                $dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
                $dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);

                if ($dettaglioRegistrazione->aggiorna($db)) {
                    
                }  // tutto ok
                else
                    return false;
            }
        }
        return true;
    }

    // Getters e Setters ---------------------------------------------------

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    public function getMessaggio() {
        return self::$messaggio;
    }

    /**     * ******************************************************************************
     * * *******************************************************************************
     * * *******************************************************************************
     */
    public function inserisciDettaglioRegistrazione($db, $utility, $idRegistrazione, $conto, $sottoConto, $importo, $d_a) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($idRegistrazione),
            '%imp_registrazione%' => trim($importo),
            '%ind_dareavere%' => trim($d_a),
            '%cod_conto%' => trim($conto),
            '%cod_sottoconto%' => trim($sottoConto)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryCreaDettaglioRegistrazione;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    /**
     * Il metodo aggiorna i dati di una scadenza per un fornitore
     */
    public function aggiornaScadenza($db, $utility, $idScadenza, $idRegistrazione, $datascad, $importo, $descreg, $tipaddebito, $codneg, $fornitore, $numfatt, $staScadenza) {

        $array = $utility->getConfig();

        $scadenza_esistente = $this->trovaScadenzaFornitore($db, $utility, $idRegistrazione, $datascad, $codneg, $fornitore, $numfatt);

        /**
         * Se la scadenza esiste la aggiorno altrimenti la inserisco.
         * Il buco di numerazione può essersi creato in seguito alla cancellazione di un pagamento e relativa scadenza
         */
        $numrow = pg_num_rows($scadenza_esistente);

        if ($numrow > 0) {

            $replace = array(
                '%id_scadenza%' => trim($idScadenza),
                '%id_registrazione%' => trim($idRegistrazione),
                '%dat_scadenza%' => trim($datascad),
                '%imp_in_scadenza%' => trim($importo),
                '%nota_in_scadenza%' => trim($descreg),
                '%tip_addebito%' => trim($tipaddebito),
                '%cod_negozio%' => trim($codneg),
                '%id_fornitore%' => $fornitore,
                '%num_fattura%' => trim($numfatt),
                '%sta_scadenza%' => trim($staScadenza)
            );
            $sqlTemplate = self::$root . $array['query'] . self::$queryUpdateScadenza;
            $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
            $result = $db->execSql($sql);
            return $result;
        } else {
            $this->inserisciScadenza($db, $utility, $idRegistrazione, $datascad, $importo, $descreg, $tipaddebito, $codneg, $fornitore, $numfatt, $staScadenza);
        }
        return $scadenza_esistente;
    }

    /**
     * Il metodo aggiorna i dati di una scadenza per un cliente
     */
    public function aggiornaScadenzaCliente($db, $utility, $idScadenza, $idRegistrazione, $datascad, $importo, $descreg, $tipaddebito, $codneg, $cliente, $numfatt, $staScadenza) {

        $array = $utility->getConfig();

        $scadenza_esistente = $this->trovaScadenzaCliente($db, $utility, $idRegistrazione, $datascad, $codneg, $cliente, $numfatt);

        /**
         * Se la scadenza esiste la aggiorno altrimenti la inserisco.
         * Il buco di numerazione può essersi creato in seguito alla cancellazione di un pagamento e relativa scadenza
         */
        if ($scadenza_esistente) {
            $replace = array(
                '%id_scadenza%' => trim($idScadenza),
                '%id_registrazione%' => trim($idRegistrazione),
                '%dat_registrazione%' => trim($datascad),
                '%imp_registrazione%' => trim($importo),
                '%nota_in_scadenza%' => trim($descreg),
                '%tip_addebito%' => trim($tipaddebito),
                '%cod_negozio%' => trim($codneg),
                '%id_cliente%' => $cliente,
                '%num_fattura%' => trim($numfatt),
                '%sta_scadenza%' => trim($staScadenza)
            );
            $sqlTemplate = self::$root . $array['query'] . self::$queryUpdateScadenzaCliente;
            $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
            $result = $db->execSql($sql);
            return $result;
        } else {
            $this->inserisciScadenzaCliente($db, $utility, $idRegistrazione, $datascad, $importo, $descreg, $tipaddebito, $codneg, $cliente, $numfatt, $staScadenza);
        }
        return $scadenza_esistente;
    }

    /**
     * Questo metodo dissocia un pagamento dalla registrazione originale sullo scadenziario fornitori
     */
    public function dissociaPagamentoScadenza($db, $utility, $idScadenza) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_scadenza%' => trim($idScadenza),
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryDissociaPagamento;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    /**
     * Questo metodo dissocia un incasso dalla registrazione originale sullo scadenziario clienti
     */
    public function dissociaIncassoScadenza($db, $utility, $idScadenza) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_scadenza%' => trim($idScadenza),
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryDissociaIncasso;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    /**
     * Il metodo cancella una scadenza di una registrazione
     */
// 	public function cancellaScadenzaRegistrazione($db, $utility, $idScadenza) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_scadenza%' => trim($idScadenza),
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenzaRegistrazione;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->getData($sql);
// 		return $result;
// 	}

    /**
     * Il metodo inserisce una scadenza per il cliente
     */
// 	public function inserisciScadenzaCliente($db, $utility, $idRegistrazione, $datareg, $importo,
// 			$descreg, $tipaddebito, $codneg, $cliente, $numfatt, $staScadenza) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_registrazione%' => trim($idRegistrazione),
// 				'%dat_registrazione%' => trim($datareg),
// 				'%imp_registrazione%' => trim($importo),
// 				'%nota%' => trim($descreg),
// 				'%tip_addebito%' => trim($tipaddebito),
// 				'%cod_negozio%' => trim($codneg),
// 				'%id_cliente%' => $cliente,
// 				'%num_fattura%' => trim($numfatt),
// 				'%sta_scadenza%' => trim($staScadenza)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaScadenzaCliente;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->execSql($sql);
// 		return $result;
// 	}
// 	public function leggiDettagliRegistrazione($db, $utility, $idregistrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_registrazione%' => trim($idregistrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiDettagliRegistrazione;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->getData($sql);
// 		return $result;
// 	}
// 	public function leggiScadenzeRegistrazione($db, $utility, $idregistrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_registrazione%' => trim($idregistrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiScadenzeRegistrazione;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->getData($sql);
// 		return $result;
// 	}

    public function updateStatoRegistrazione($db, $utility, $id_registrazione, $stareg) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($id_registrazione),
            '%sta_registrazione%' => trim($stareg)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoRegistrazione;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);
    }

    public function updateRegistrazione($db, $utility, $id_registrazione, $totaleDare, $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $stareg, $codneg, $staScadenza, $idmercato) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($id_registrazione),
            '%des_registrazione%' => trim($descreg),
            '%dat_scadenza%' => trim($datascad),
            '%dat_registrazione%' => trim($datareg),
            '%sta_registrazione%' => trim($stareg),
            '%num_fattura%' => trim($numfatt),
            '%cod_negozio%' => $codneg,
            '%cod_causale%' => $causale,
            '%id_fornitore%' => $fornitore,
            '%id_cliente%' => $cliente,
            '%id_mercato%' => $idmercato,
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryUpdateRegistrazione;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);

        return $result;
    }

    public function cancellaDettaglioRegistrazione($db, $utility, $id_dettaglioregistrazione) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_dettaglio_registrazione%' => trim($id_dettaglioregistrazione)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryDeleteDettaglioRegistrazione;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);
    }

    public function prelevaScadenzeAperteFornitore($db, $utility, $idfornitore) {
        
    }

    public function prelevaScadenzeFornitore($db, $utility, $idfornitore, $idregistrazione) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_fornitore%' => trim($idfornitore),
            '%id_registrazione%' => trim($idregistrazione)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryLeggiScadenzeFornitore;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);
        return $result;
    }

    public function prelevaScadenzeCliente($db, $utility, $idcliente, $idregistrazione) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_cliente%' => trim($idcliente),
            '%id_registrazione%' => trim($idregistrazione)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryLeggiScadenzeCliente;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);
        return $result;
    }

// 	public function leggiScadenzaCliente($db, $utility, $idcliente, $idincasso) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_cliente%' => trim($idcliente),
// 				'%id_incasso%' => trim($idincasso)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaScadenzaCliente;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->execSql($sql);
// 		return $result;
// 	}
// 	public function leggiScadenzaFornitore($db, $utility, $idfornitore, $idpagamento) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_fornitore%' => trim($idfornitore),
// 				'%id_pagamento%' => trim($idpagamento)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaScadenzaFornitore;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->execSql($sql);
// 		return $result;
// 	}
// 	public function scadenzaFornitore($db, $utility, $idregistrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_registrazione%' => trim($idregistrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryScadenzaFornitore;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->execSql($sql);
// 		return $result;
// 	}
// 	public function scadenzaCliente($db, $utility, $idregistrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_registrazione%' => trim($idregistrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryScadenzaCliente;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->execSql($sql);
// 		return $result;
// 	}

    public function trovaScadenzaFornitore($db, $utility, $idRegistrazione, $datascad, $codneg, $idfornitore, $numfatt) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_fornitore%' => trim($idfornitore),
            '%id_registrazione%' => trim($idRegistrazione),
            '%dat_scadenza%' => trim($datascad),
            '%cod_negozio%' => trim($codneg),
            '%num_fattura%' => trim($numfatt)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryTrovaScadenzaFornitore;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

    public function trovaScadenzaCliente($db, $utility, $idRegistrazione, $datascad, $codneg, $idcliente, $numfatt) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_cliente%' => trim($idcliente),
            '%id_registrazione%' => trim($idRegistrazione),
            '%dat_registrazione%' => trim($datascad),
            '%cod_negozio%' => trim($codneg),
            '%num_fattura%' => trim($numfatt)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryTrovaScadenzaCliente;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }

// 	public function cambiaStatoScadenzaFornitore($db, $utility, $idfornitore, $numeroFattura, $statoScadenza, $idregistrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_fornitore%' => (int)$idfornitore,
// 				'%num_fattura%' => trim($numeroFattura),
// 				'%sta_scadenza%' => trim($statoScadenza),
// 				'%id_registrazione%' => trim($idregistrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoScadenza;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->execSql($sql);
// 	}
// 	public function cambiaStatoScadenzaCliente($db, $utility, $idcliente, $numeroFattura, $statoScadenza, $idregistrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_cliente%' => (int)$idcliente,
// 				'%num_fattura%' => trim($numeroFattura),
// 				'%sta_scadenza%' => trim($statoScadenza),
// 				'%id_registrazione%' => trim($idregistrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoScadenzaCliente;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		$result = $db->execSql($sql);
// 	}

    public function cambioStatoRegistrazione($db, $utility, $id_registrazione, $stareg) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_registrazione%' => trim($id_registrazione),
            '%sta_registrazione%' => trim($stareg)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoRegistrazione;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
    }

// 	public function cancellaScadenzaFornitore($db, $utility, $id_registrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_registrazione%' => trim($id_registrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenza;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		return $db->execSql($sql);
// 	}
// 	public function cancellaScadenzaCliente($db, $utility, $id_registrazione) {
// 		$array = $utility->getConfig();
// 		$replace = array(
// 				'%id_registrazione%' => trim($id_registrazione)
// 		);
// 		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenzaCliente;
// 		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
// 		return $db->execSql($sql);
// 	}

    public function prelevaIdRegistrazioneOriginaleCliente($db, $utility, $id_cliente, $num_fattura) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_cliente%' => trim($id_cliente),
            '%num_fattura%' => trim($num_fattura)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaRegistrazioneOriginaleCliente;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        return $db->execSql($sql);
    }

    public function prelevaIdRegistrazioneOriginaleFornitore($db, $utility, $id_fornitore, $num_fattura) {

        $array = $utility->getConfig();
        $replace = array(
            '%id_fornitore%' => trim($id_fornitore),
            '%num_fattura%' => trim($num_fattura)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaRegistrazioneOriginaleFornitore;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        return $db->execSql($sql);
    }

    public function cercaCorrispettivo($db, $utility, $datareg, $codneg, $conto, $importo) {

        $array = $utility->getConfig();
        $replace = array(
            '%dat_registrazione%' => trim($datareg),
            '%cod_negozio%' => trim($codneg),
            '%cod_conto%' => substr(trim($conto), 0, 3),
            '%imp_registrazione%' => str_replace(",", ".", trim($importo))
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryTrovaCorrispettivo;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        return $db->getData($sql);
    }

    public function prelevaDatiScadenzeRegistrazione($utility) {

        require_once 'database.class.php';

        $db = Database::getInstance();

        $result = $this->leggiScadenzeRegistrazione($db, $utility, $_SESSION["idRegistrazione"]);

        if ($result) {
            if (pg_num_rows($result) > 1) {
                $_SESSION["numeroScadenzeRegistrazione"] = pg_num_rows($result);
                $_SESSION["elencoScadenzeRegistrazione"] = pg_fetch_all($result);
            } else {
                unset($_SESSION["numeroScadenzeRegistrazione"]);
                unset($_SESSION["elencoScadenzeRegistrazione"]);
            }
        } else {
            error_log(">>>>>> Errore prelievo scadenze registrazione (dettagli) : " . $_SESSION["idRegistrazione"] . " <<<<<<<<");
        }
    }

    /**
     * Questo metodo preleva il capoconto di un fornitore accedendo con il codice fornitore che corrisponde al sottoconto
     */
    public function leggiContoFornitore($db, $utility, $cod_fornitore) {

        $array = $utility->getConfig();
        $replace = array(
            '%cod_fornitore%' => trim($cod_fornitore)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaCapocontoFornitore;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        return $db->execSql($sql);
    }

    public function leggiContoCliente($db, $utility, $cod_cliente) {

        $array = $utility->getConfig();
        $replace = array(
            '%cod_cliente%' => trim($cod_cliente)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaCapocontoCliente;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        return $db->execSql($sql);
    }

}

?>
