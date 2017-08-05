<?php

require_once 'chopin.abstract.class.php';

abstract class StrumentiAbstract extends ChopinAbstract {

	private static $_instance = null;

	// Query ---------------------------------------------------------------

	public static $queryRicercaRegistrazioniConto = "/strumenti/ricercaRegistrazioniConto.sql";
	public static $queryUpdateDettaglioRegistrazione = "/strumenti/updateDettaglioRegistrazione.sql";

	/**
	 * Metodi comuni
	 */

	/**
	 * Questo metodo carica tutte le registrazioni che hanno almeno un dettaglio su un sottoconto specifico
	 * @param unknown $utility
	 * @param unknown $db
	 */
	public function caricaRegistrazioniConto($utility, $db) {

		$filtriRegistrazione = "";
		$filtriDettaglio = "";

		if ($_SESSION["codneg_sel"] != "") {
			$filtriRegistrazione .= "and reg.cod_negozio = '" . $_SESSION["codneg_sel"] . "'";
		}

		if ($_SESSION["conto_sel"] != "") {

			$conto = explode(" - ", $_SESSION["conto_sel"]);

			$filtriDettaglio .= "and detreg.cod_conto = '" . $conto[0] . "'";
			$filtriDettaglio .= "and detreg.cod_sottoconto = '" . $conto[1] . "'";
		}

		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%filtri-registrazione%' => $filtriRegistrazione,
				'%filtri-dettaglio%' => $filtriDettaglio,
		);

		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaRegistrazioniConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

 		$result = $db->execSql($sql);

		return $result;
	}

	public function updateDettaglioRegistrazione($db, $utility, $id_dettaglio_registrazione, $conto, $sottoconto) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_dettaglio_registrazione%' => trim($id_dettaglio_registrazione),
				'%cod_conto%' => trim($conto),
				'%cod_sottoconto%' => trim($sottoconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateDettaglioRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

		$result = $db->execSql($sql);
		return $result;
	}
}

?>
