<?php

require_once 'nexus6.abstract.class.php';

abstract class AnagraficaAbstract extends Nexus6Abstract {

	public static $messaggio;

	// Query ---------------------------------------------------------------


	public static $queryCreaMercato = "/anagrafica/creaMercato.sql";
	public static $queryDeleteMercato = "/anagrafica/deleteMercato.sql";
	public static $queryUpdateMercato = "/anagrafica/updateMercato.sql";

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	// Metodi comuni di utilita della prima nota ---------------------------

	/**
	 * Questo metodo cerca un fornitore tramite il codice fornitore
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codfornitore
	 * @return unknown
	 */
	public function cercaCodiceFornitore($db, $utility, $codfornitore) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_fornitore%' => trim($codfornitore)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 * Questo metodo cerca un cliente tramite il suo codice di partita iva
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codpiva
	 * @return unknown
	 */
	public function cercaPartivaIvaCliente($db, $utility, $codpiva, $idcliente) {

	}

	/**
	 * Questo metodo cerca un cliente tramite il suo codice fiscale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codfisc
	 * @return unknown
	 */
	public function cercaCodiceFiscaleCliente($db, $utility, $codfisc) {

	}


	/**
	 * Questo metodo crea un nuovo mercato
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codmercato
	 * @param unknown $desmercato
	 * @param unknown $cittamercato
	 */
	public function inserisciMercato($db, $utility, $codmercato, $desmercato, $cittamercato, $codneg) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_mercato%' => trim($codmercato),
				'%des_mercato%' => trim($desmercato),
				'%citta_mercato%' => trim($cittamercato),
				'%cod_negozio%' => trim($codneg)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaMercato;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}

	/**
	 * Questo metodo cancella un mercato
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idmercato
	 */
	public function cancellaMercato($db, $utility, $idmercato) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_mercato%' => trim($idmercato)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteMercato;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	}

	public function updateMercato($db, $utility, $idmercato, $codmercato, $desmercato, $cittamercato, $codneg) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_mercato%' => trim($idmercato),
				'%cod_mercato%' => trim($codmercato),
				'%des_mercato%' => trim($desmercato),
				'%citta_mercato%' => trim($cittamercato),
				'%cod_negozio%' => trim($codneg),
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateMercato;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}
}

?>
