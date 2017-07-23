<?php

require_once 'nexus6.abstract.class.php';

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
	public static $queryLeggiScadenzeAperteCliente = "/primanota/ricercaScadenzeAperteCliente.sql";
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

	public function makeTabellaDettagliRegistrazione($dettaglioRegistrazione)
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
			"		<th width='500'>Conto</th>" .
			"		<th width='150' align='right'>Importo</th>" .
			"		<th align='center'>D/A</th>" .
			"		<th>&nbsp;</th>" .
			"	</tr>" .
			"</thead>";

			foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio)
			{
				$codConto = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);
				$bottoneCancella = $cancella_dettaglio_nuova_registrazione_href . $codConto[0] . $cancella_icon;

				$tbody .=
				"<tr>" .
				"	<td>" . $unDettaglio[DettaglioRegistrazione::COD_CONTO] . "</td>" .
				"	<td align='right'>" . $unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE] . "</td>" .
				"	<td align='center'>" . $unDettaglio[DettaglioRegistrazione::IND_DAREAVERE] . "</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$tbody .= "</tbody>";
		}
		return "<table id='dettagli_cre' class='result'>" . $thead . $tbody . "</table>";
	}


	public function makeTabellaScadenzeFornitore($scadenzaFornitore)
	{
		$cancella_nuova_scadenza_fornitore_href = "<a class='tooltip' onclick='cancellaNuovaScadenzaFornitore(";
		$cancella_icon = ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";

		$thead = "";
		$tbody = "";

		if ($scadenzaFornitore->getQtaScadenze() > 0) {

			$tbody = "<tbody>";
			$thead =
			"<thead>" .
			"	<tr>" .
			"		<th width='80'>Data</th>" .
			"		<th width='80' align='right'>Importo</th>" .
			"		<th>&nbsp;</th>" .
			"	</tr>" .
			"</thead>";

			foreach ($scadenzaFornitore->getScadenze() as $unaScadenza)
			{
				$datScadenza = $unaScadenza[ScadenzaFornitore::DAT_SCADENZA];
				$bottoneCancella = $cancella_nuova_scadenza_fornitore_href . $datScadenza . $cancella_icon;

				$tbody .=
				"<tr>" .
				"	<td>" . $unaScadenza[ScadenzaFornitore::DAT_SCADENZA] . "</td>" .
				"	<td align='right'>" . $unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA] . "</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$tbody .= "</tbody>";
		}
		return "<table id='scadenzesuppl_cre' class='result'>" . $thead . $tbody . "</table>";
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
	 *

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idRegistrazione
	 * @param unknown $conto
	 * @param unknown $sottoConto
	 * @param unknown $importo
	 * @param unknown $d_a
	 * @return unknown
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
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idScadenza
	 * @param unknown $idRegistrazione
	 * @param unknown $datascad
	 * @param unknown $importo
	 * @param unknown $descreg
	 * @param unknown $tipaddebito
	 * @param unknown $codneg
	 * @param unknown $fornitore
	 * @param unknown $numfatt
	 * @param unknown $staScadenza
	 * @return unknown
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
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idScadenza
	 * @param unknown $idRegistrazione
	 * @param unknown $datascad
	 * @param unknown $importo
	 * @param unknown $descreg
	 * @param unknown $tipaddebito
	 * @param unknown $codneg
	 * @param unknown $cliente
	 * @param unknown $numfatt
	 * @param unknown $staScadenza
	 * @return unknown
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
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idScadenza
	 * @return unknown
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
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idScadenza
	 * @return unknown
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
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idScadenza
	 * @return unknown
	 */
	public function cancellaScadenzaRegistrazione($db, $utility, $idScadenza) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_scadenza%' => trim($idScadenza),
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenzaRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 * Il metodo inserisce una scadenza per il cliente
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idRegistrazione
	 * @param unknown $datareg
	 * @param unknown $importo
	 * @param unknown $descreg
	 * @param unknown $tipaddebito
	 * @param unknown $codneg
	 * @param unknown $cliente
	 * @param unknown $numfatt
	 * @param unknown $staScadenza
	 * @return unknown
	 */
	public function inserisciScadenzaCliente($db, $utility, $idRegistrazione, $datareg, $importo,
			$descreg, $tipaddebito, $codneg, $cliente, $numfatt, $staScadenza) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($idRegistrazione),
				'%dat_registrazione%' => trim($datareg),
				'%imp_registrazione%' => trim($importo),
				'%nota%' => trim($descreg),
				'%tip_addebito%' => trim($tipaddebito),
				'%cod_negozio%' => trim($codneg),
				'%id_cliente%' => $cliente,
				'%num_fattura%' => trim($numfatt),
				'%sta_scadenza%' => trim($staScadenza)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaScadenzaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idregistrazione
	 * @return unknown
	 */
	public function leggiDettagliRegistrazione($db, $utility, $idregistrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($idregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiDettagliRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idregistrazione
	 * @return unknown
	 */
	public function leggiScadenzeRegistrazione($db, $utility, $idregistrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($idregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiScadenzeRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_registrazione
	 * @param unknown $stareg
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_registrazione
	 * @param unknown $totaleDare
	 * @param unknown $descreg
	 * @param unknown $datascad
	 * @param unknown $datareg
	 * @param unknown $numfatt
	 * @param unknown $causale
	 * @param unknown $fornitore
	 * @param unknown $cliente
	 * @param unknown $stareg
	 * @return boolean
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_dettaglioregistrazione
	 */
	public function cancellaDettaglioRegistrazione($db, $utility, $id_dettaglioregistrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_dettaglio_registrazione%' => trim($id_dettaglioregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteDettaglioRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @return unknown
	 */
	public function prelevaScadenzeAperteFornitore($db, $utility, $idfornitore) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($idfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiScadenzeAperteFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idcliente
	 * @return unknown
	 */
	public function prelevaScadenzeAperteCliente($db, $utility, $idcliente) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiScadenzeAperteCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @param unknown $idregistrazione
	 * @return unknown
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idcliente
	 * @param unknown $idincasso
	 * @return unknown
	 */
	public function leggiScadenzaCliente($db, $utility, $idcliente, $idincasso) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($idcliente),
				'%id_incasso%' => trim($idincasso)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaScadenzaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @param unknown $idpagamento
	 * @return unknown
	 */
	public function leggiScadenzaFornitore($db, $utility, $idfornitore, $idpagamento) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($idfornitore),
				'%id_pagamento%' => trim($idpagamento)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaScadenzaFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @param unknown $idpagamento
	 * @return unknown
	 */
	public function scadenzaFornitore($db, $utility, $idregistrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($idregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryScadenzaFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idregistrazione
	 * @return unknown
	 */
	public function scadenzaCliente($db, $utility, $idregistrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($idregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryScadenzaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idRegistrazione
	 * @param unknown $datascad
	 * @param unknown $codneg
	 * @param unknown $idfornitore
	 * @param unknown $numfatt
	 * @return unknown
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idRegistrazione
	 * @param unknown $datascad
	 * @param unknown $codneg
	 * @param unknown $idfornitore
	 * @param unknown $numfatt
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idfornitore
	 * @param unknown $numeroFattura
	 * @param unknown $statoScadenza
	 * @param unknown $idregistrazione\
	 */
	public function cambiaStatoScadenzaFornitore($db, $utility, $idfornitore, $numeroFattura, $statoScadenza, $idregistrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => (int)$idfornitore,
				'%num_fattura%' => trim($numeroFattura),
				'%sta_scadenza%' => trim($statoScadenza),
				'%id_registrazione%' => trim($idregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoScadenza;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idcliente
	 * @param unknown $numeroFattura
	 * @param unknown $statoScadenza
	 * @param unknown $idregistrazione
	 */
	public function cambiaStatoScadenzaCliente($db, $utility, $idcliente, $numeroFattura, $statoScadenza, $idregistrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => (int)$idcliente,
				'%num_fattura%' => trim($numeroFattura),
				'%sta_scadenza%' => trim($statoScadenza),
				'%id_registrazione%' => trim($idregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoScadenzaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_registrazione
	 * @param unknown $stareg
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_registrazione
	 */
	public function cancellaScadenzaFornitore($db, $utility, $id_registrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($id_registrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenza;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		return $db->execSql($sql);
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_registrazione
	 */
	public function cancellaScadenzaCliente($db, $utility, $id_registrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($id_registrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenzaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		return $db->execSql($sql);
	}

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_cliente
	 * @param unknown $num_fattura
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $id_fornitore
	 * @param unknown $num_fattura
	 */
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

	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $datareg
	 * @param unknown $codneg
	 * @param unknown $conto
	 * @param unknown $importo
	 */
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
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $cod_fornitore
	 * @return resultset
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
