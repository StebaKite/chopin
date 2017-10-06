<?php

require_once 'nexus6.abstract.class.php';
require_once 'lavoroPianificato.class.php';

abstract class PrimanotaAbstract extends Nexus6Abstract {

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
	public static $queryTrovaCorrispettivo = "/primanota/trovaCorrispettivo.sql";

	public static $queryTrovaScadenzaFornitore = "/primanota/trovaScadenzaFornitore.sql";
	public static $queryTrovaScadenzaCliente = "/primanota/trovaScadenzaCliente.sql";

	public static $queryPrelevaCapocontoFornitore = "/primanota/ricercaCapocontoFornitore.sql";
	public static $queryPrelevaCapocontoCliente = "/primanota/ricercaCapocontoCliente.sql";

	// Altri campi

	public static $messaggio;

	// Metodi comuni di utilita della prima note ---------------------------
	
	public function makeTabellaFattureDaPagare($scadenzeFornitore, $idTable)
	{
	    $aggiungi_fattura_pagata_href = "<a class='tooltip' onclick='aggiungiFatturaPagata(";
	    $aggiungi_icon = ")'><li class='ui-state-default ui-corner-all' ><span class='ui-icon ui-icon-plus'></span></li></a>";
	    
	    $thead = "";
	    $tbody = "";
	    
	    if ($scadenzeFornitore->getQtaScadenzeDaPagare() > 0) {
	        
	        $tbody = "<tbody>";
	        $thead =
	        "<thead>" .
	        "	<tr>" .
	        "		<th width='20'>&nbsp;</th>" .
	        "		<th width='50'>Num.Fat.</th>" .
	        "		<th width='50'>Data</th>" .
	        "		<th width='50' align='center'>Importo</th>" .
	        "		<th width='200'>Nota</th>" .
	        "	</tr>" .
	        "</thead>";
	        
	        foreach ($scadenzeFornitore->getScadenzeDaPagare() as $unaScadenza)
	        {
	            $bottoneAggiungiFatturaPagata = $aggiungi_fattura_pagata_href . $unaScadenza[ScadenzaFornitore::ID_SCADENZA] . $aggiungi_icon;
	            
	            $tbody .=
	            "<tr>" .
	            "	<td id='icons'>" . $bottoneAggiungiFatturaPagata . "</td>" .
	            "	<td>" . $unaScadenza[ScadenzaFornitore::NUM_FATTURA] . "</td>" .
	            "	<td>" . $unaScadenza[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
	            "	<td align='center'>" . $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
	            "	<td>" . $unaScadenza[ScadenzaFornitore::NOTA_SCADENZA] . "</td>" .
	            "</tr>";
	        }
	        $tbody .= "</tbody>";
	    }
        return "<table id='" . $idTable . "' class='result' style='width: 100%'>" . $thead . $tbody . "</table>";
	}	    
	
	public function makeTabellaFatturePagate($scadenzeFornitore, $idTable)
	{
	    $rimuovi_fattura_pagata_href = "<a class='tooltip' onclick='rimuoviFatturaPagata(";
	    $rimuovi_icon = ")'><li class='ui-state-default ui-corner-all' ><span class='ui-icon ui-icon-minus'></span></li></a>";
	    
	    $thead = "";
	    $tbody = "";
	    
	    if ($scadenzeFornitore->getQtaScadenzePagate() > 0) {
	        
	        $tbody = "<tbody>";
	        $thead =
	        "<thead>" .
	        "	<tr>" .
	        "		<th width='50'>Num.Fat.</th>" .
	        "		<th width='50'>Data</th>" .
	        "		<th width='50' align='center'>Importo</th>" .
	        "		<th width='200'>Nota</th>" .
	        "		<th width='20'>&nbsp;</th>" .
	        "	</tr>" .
	        "</thead>";
	        
	        foreach ($scadenzeFornitore->getScadenzePagate() as $unaScadenza)
	        {
	            $bottoneRimuoviFatturaPagata = $rimuovi_fattura_pagata_href . $unaScadenza[ScadenzaFornitore::ID_SCADENZA] . $rimuovi_icon;
	            
	            $tbody .=
	            "<tr>" .
	            "	<td>" . $unaScadenza[ScadenzaFornitore::NUM_FATTURA] . "</td>" .
	            "	<td>" . $unaScadenza[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
	            "	<td align='center'>" . $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
	            "	<td>" . $unaScadenza[ScadenzaFornitore::NOTA_SCADENZA] . "</td>" .
	            "	<td id='icons'>" . $bottoneRimuoviFatturaPagata . "</td>" .
	            "</tr>";
	        }
	        $tbody .= "</tbody>";
	    }
	    return "<table id='" . $idTable . "' class='result' style='width: 100%'>" . $thead . $tbody . "</table>";
	}
	
	public function makeTabellaDettagliRegistrazione($dettaglioRegistrazione, $idTable)
	{
		$cancella_dettaglio_nuova_registrazione_href = "<a class='tooltip' onclick='cancellaDettaglioNuovaRegistrazione(";
		$cancella_icon = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";

		$thead = "";
		$tbody = "";

		if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() > 0) {

			$tbody = "<tbody>";
			$thead =
			"<thead>" .
			"	<tr>" .
			"		<th width='600'>Conto</th>" .
			"		<th width='150' align='right'>Importo</th>" .
			"		<th align='center'>D/A</th>" .
			"		<th>&nbsp;</th>" .
			"	</tr>" .
			"</thead>";

			foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio)
			{
				$contoComposto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
				$codConto = explode(".", $contoComposto[0]);
				$idDettaglio = $unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE];

				$bottoneCancella = $cancella_dettaglio_nuova_registrazione_href . $contoComposto[0] . $cancella_icon;

				$onModifyImporto = "onkeyup='modificaImportoDettaglioRegistrazione(" . $codConto[0] . "," . $codConto[1] . ",this.value," . $idDettaglio . ")'";
				$onModifySegno   = "onkeyup='modificaSegnoDettaglioRegistrazione(" . $codConto[0] . "," . $codConto[1] . ",this.value," . $idDettaglio . ")'";

				$tbody .=
				"<tr>" .
				"	<td>" . $unDettaglio[DettaglioRegistrazione::COD_CONTO] . "</td>" .
				"	<td align='right'>" .
				"		<input type='text' size='15' maxlength='10' " . $onModifyImporto . " value='" . $unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE] . "'></input>" .
				"	</td>" .
				"	<td align='center'>" .
				"		<input type='text' size='2' maxlength='1' " . $onModifySegno . " value='" . $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE] . "'></input>" .
				"	</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$tbody .= "</tbody>";
			return "<table id='" . $idTable . "' class='result'>" . $thead . $tbody . "</table>";
		}
		return "";
	}

	public function makeTabellaScadenzeFornitore($scadenzaFornitore, $idTable)
	{
		$cancellaLocked = "<li class='ui-state-default ui-corner-all'><span class='ui-icon ui-icon-locked'></span></li>";
		$cancella_nuova_scadenza_fornitore_href = "<a class='tooltip' onclick='cancellaNuovaScadenzaFornitore(";
		$cancella_icon = ")'><li class='ui-state-default ui-corner-all'><span class='ui-icon ui-icon-trash'></span></li></a>";

		$data_ko = "class='ko'";
		$data_ok = "class='ok'";

		$thead = "";
		$tbody = "";

		if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0) {

			$tbody = "<tbody>";
			$thead =
			"<thead>" .
			"	<tr>" .
			"		<th width='80'>Data</th>" .
			"		<th width='60'>Stato</th>" .
			"		<th width='80'>Importo</th>" .
			"		<th>&nbsp;</th>" .
			"	</tr>" .
			"</thead>";

			foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza)
			{
				$idFornitore = $unaScadenza[ScadenzaFornitore::ID_FORNITORE];
				$dataScadenza = strtotime(str_replace('/', '-', $unaScadenza[ScadenzaFornitore::DAT_SCADENZA]));							// cambio i separatori altrimenti la strtotime non funziona
				$numFatt = $unaScadenza[ScadenzaFornitore::NUM_FATTURA];

				$onModifyImporto = "onkeyup='modificaImportoScadenzaFornitore(" . $idFornitore . "," . $dataScadenza . "," . $numFatt . ",this.value)'";
				$stato = ($unaScadenza[ScadenzaFornitore::STA_SCADENZA] == "10") ? "Pagata" : "Da Pagare";

				if ($stato == "Da Pagare") {
					$tdclass = $data_ko;
					$bottoneCancella = $cancella_nuova_scadenza_fornitore_href . $idFornitore . "," .$dataScadenza . "," . $numFatt . $cancella_icon;
				}
				else {
					$tdclass = $data_ok;
					$bottoneCancella = $cancellaLocked;
				}

				$tbody .=
				"<tr>" .
				"	<td>" . $unaScadenza[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
				"	<td " . $tdclass . ">" . $stato . "</td>" .
				"	<td align='right'>" .
				"		<input type='text' size='15' maxlength='10' " . $onModifyImporto . " value='" . $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] . "'></input>" .
				"	</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$tbody .= "</tbody>";
			return "<table id='" . $idTable . "' class='result'>" . $thead . $tbody . "</table>";
		}
		return "";
	}

	public function makeTabellaScadenzeCliente($scadenzaCliente, $idTable)
	{
		$cancella_nuova_scadenza_cliente_href = "<a class='tooltip' onclick='cancellaNuovaScadenzaCliente(";
		$cancella_icon = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";

		$thead = "";
		$tbody = "";

		if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0) {

			$tbody = "<tbody>";
			$thead =
			"<thead>" .
			"	<tr>" .
			"		<th width='80'>Data</th>" .
			"		<th width='80' align='right'>Importo</th>" .
			"		<th>&nbsp;</th>" .
			"	</tr>" .
			"</thead>";

			foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza)
			{
				$idCliente = $unaScadenza[ScadenzaCliente::ID_CLIENTE];
				$dataScadenza = strtotime(str_replace('/', '-', $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]));
				$numFatt = $unaScadenza[ScadenzaCliente::NUM_FATTURA];

				$bottoneCancella = $cancella_nuova_scadenza_cliente_href . $idCliente . "," .$dataScadenza . "," . $numFatt . $cancella_icon;

				$onModifyImporto = "onkeyup='modificaImportoScadenzaCliente(" . $idCliente . "," . $dataScadenza . "," . $numFatt . ",this.value)'";

				$tbody .=
				"<tr>" .
				"	<td>" . $unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE] . "</td>" .
				"	<td align='right'>" .
				"		<input type='text' size='15' maxlength='10' " . $onModifyImporto . " value='" . $unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE] . "'></input>" .
				"	</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$tbody .= "</tbody>";
			return "<table id='". $idTable . "' class='result'>" . $thead . $tbody . "</table>";
		}
		return "";
	}

	public function ricalcolaSaldi($db, $datRegistrazione)
	{
		$lavoroPianificato = LavoroPianificato::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		if ($array['lavoriPianificatiAttivati'] == "Si") {
			$lavoroPianificato->setDatRegistrazione(str_replace('/', '-', $datRegistrazione));
			$lavoroPianificato->settaDaEseguire($db);
		}
	}

	public function aggiungiDettagliCorrispettivo($db, $utility, $array)
	{
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

		$dettaglioRegistrazione->setCodConto($dettaglioRegistrazione->getCodConto() . " - " . $sottoconto->getDesSottoconto());
		$dettaglioRegistrazione->setIndDareAvere("D");
		$dettaglioRegistrazione->aggiungi();

		/**
		 * Dettaglio conto erario
		 */
		$dettaglioRegistrazione->setCodConto($array['contoErarioMercati']);
		$dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImpIva());
		$dettaglioRegistrazione->setIndDareAvere("A");
		$dettaglioRegistrazione->aggiungi();

		/**
		 * Dettaglio Cassa/Banca
		 */
		$dettaglioRegistrazione->setCodConto($array['contoCorrispettivoMercati']);
		$dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImponibile());
		$dettaglioRegistrazione->setIndDareAvere("A");
		$dettaglioRegistrazione->aggiungi();

		return $dettaglioRegistrazione;
	}

	public function creaCorrispettivo($utility, $registrazione, $dettaglioRegistrazione)
	{
		$db = Database::getInstance();
		$db->beginTransaction();
		$dettagli_ok = true;

		if ($registrazione->inserisci($db)) {

			foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
				if ($this->creaDettaglioCorrispettivonegozio($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)) {}
				else {
					$dettagli_ok = false;
					break;
				}
			}

			/***
			 * Ricalcolo i saldi dei conti
			 */
			if ($dettagli_ok) {
				$this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
				$db->commitTransaction();
				return true;
			}
			else {
				$db->rollbackTransaction();
				return false;
			}
		}
		else {
			$db->rollbackTransaction();
			return false;
		}
	}

	public function creaDettaglioCorrispettivonegozio($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)
	{
		$_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);	// il codconto del dettaglio contiene anche la descrizione
		$conto = explode(".", $_cc[0]);		// conto e sottoconto separati da un punto

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

	public function aggiornaDettagli($db,$utility,$registrazione,$dettaglioRegistrazione)
	{
	    foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio)
	    {
	        if ($unDettaglio[DettaglioRegistrazione::ID_REGISTRAZIONE] == 0)
	        {
	            $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
	            $dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
	            $dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);
	            
	            $_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
	            $conto = explode(".", $_cc[0]);
	            
	            $dettaglioRegistrazione->setCodConto($conto[0]);
	            $dettaglioRegistrazione->setCodSottoconto($conto[1]);
	            
	            if ($dettaglioRegistrazione->inserisci($db)) {}		// tutto ok
	            else return false;
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
















	/** *******************************************************************************
	 ** *******************************************************************************
	 ** *******************************************************************************
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
	public function aggiornaScadenza($db, $utility, $idScadenza, $idRegistrazione, $datascad, $importo,
			$descreg, $tipaddebito, $codneg, $fornitore, $numfatt, $staScadenza) {

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
		}
		else {
			$this->inserisciScadenza($db, $utility, $idRegistrazione, $datascad, $importo,
					$descreg, $tipaddebito, $codneg, $fornitore, $numfatt, $staScadenza);
		}
		return $scadenza_esistente;
	}

	/**
	 * Il metodo aggiorna i dati di una scadenza per un cliente
	 */
	public function aggiornaScadenzaCliente($db, $utility, $idScadenza, $idRegistrazione, $datascad, $importo,
			$descreg, $tipaddebito, $codneg, $cliente, $numfatt, $staScadenza) {

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
		}
		else {
			$this->inserisciScadenzaCliente($db, $utility, $idRegistrazione, $datascad, $importo,
					$descreg, $tipaddebito, $codneg, $cliente, $numfatt, $staScadenza);
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

	public function updateRegistrazione($db, $utility, $id_registrazione, $totaleDare,
			$descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $stareg,
			$codneg, $staScadenza, $idmercato) {

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
				'%cod_conto%' => substr(trim($conto),0,3),
				'%imp_registrazione%' => str_replace(",", ".", trim($importo))
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaCorrispettivo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		return $db->getData($sql);
	}

	public function isNew($db, $utility, $datareg, $codneg, $conto, $importo) {

		$array = $utility->getConfig();
		$replace = array(
				'%dat_registrazione%' => trim($datareg),
				'%cod_negozio%' => trim($codneg),
				'%cod_conto%' => substr(trim($conto),0,3),
				'%imp_registrazione%' => str_replace(",", ".", trim($importo))
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryTrovaCorrispettivo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

		if (pg_num_rows($db->execSql($sql)) > 0) {
			return false;
		}
		return true;
	}

	public function prelevaDatiScadenzeRegistrazione($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();

		$result = $this->leggiScadenzeRegistrazione($db, $utility, $_SESSION["idRegistrazione"]);

		if ($result) {
			if (pg_num_rows($result) > 1) {
				$_SESSION["numeroScadenzeRegistrazione"] = pg_num_rows($result);
				$_SESSION["elencoScadenzeRegistrazione"] = pg_fetch_all($result);
			}
			else {
				unset($_SESSION["numeroScadenzeRegistrazione"]);
				unset($_SESSION["elencoScadenzeRegistrazione"]);
			}
		}
		else {
			error_log(">>>>>> Errore prelievo scadenze registrazione (dettagli) : " . $_SESSION["idRegistrazione"] . " <<<<<<<<" );
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
