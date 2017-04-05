<?php

require_once 'nexus6.abstract.class.php';

abstract class ConfigurazioniAbstract extends Nexus6Abstract {

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryUpdateSottoConto = "/configurazioni/updateSottoconto.sql";
	
	public static $queryLeggiCausale = "/configurazioni/leggiCausale.sql";
	public static $queryUpdateCausale = "/configurazioni/updateCausale.sql";
	public static $queryLeggiContiCausale = "/configurazioni/leggiContiCausale.sql";
	
	public static $queryLeggiContiDisponibili = "/configurazioni/leggiContiDisponibili.sql";
	public static $queryCreaConfigurazioneCausale = "/configurazioni/creaConfigurazioneCausale.sql";
	public static $queryDeleteConfigurazioneCausale = "/configurazioni/deleteConfigurazioneCausale.sql";
	public static $queryRicercaProgressivoFattura = "/configurazioni/ricercaProgressivoFattura.sql";

	public static $queryUpdateProgressivoFattura = "/configurazioni/updateProgressivoFattura.sql";

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	// Metodi comuni di utilita della prima note ---------------------------



	/**
	 * Questo metodo permette di aggiornare gli attributi di un sottoconto
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $conto
	 * @param unknown $sottoconto
	 */
	public function updateSottoconto($db, $utility, $codconto, $codsottoconto, $indgruppo) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_conto%' => trim($codconto),
				'%cod_sottoconto%' => trim($codsottoconto),
				'%ind_gruppo%' => trim($indgruppo)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateSottoConto;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}


	/**
	 * Questo metodo legge una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @return unknown
	 */
	public function leggiCausale($db, $utility, $codcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 * Questo metodo aggiorna una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @param unknown $descausale
	 * @param unknown $catcausale
	 * @return unknown
	 */
	public function updateCausale($db, $utility, $codcausale, $descausale, $catcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale),
				'%des_causale%' => trim($descausale),
				'%cat_causale%' => trim($catcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo legge tutti i conti configurati su una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @return unknown
	 */
	public function leggiContiCausale($db, $utility, $codcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiContiCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 * Questo metodo legge tutti i conti non ancrora associati alla causale corrente
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @return unknown
	 */
	public function leggiContiDisponibili($db, $utility, $codcausale) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiContiDisponibili;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	/**
	 * Questo metodo associa un conto ad una causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @param unknown $codconto
	 * @return unknown
	 */
	public function creaConfigurazioneCausale($db, $utility, $codcausale, $codconto) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale),
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaConfigurazioneCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	/**
	 * Questo metodo toglie il conto dalla causale
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codcausale
	 * @param unknown $codconto
	 * @return unknown
	 */
	public function deleteConfigurazioneCausale($db, $utility, $codcausale, $codconto) {

		$array = $utility->getConfig();
		$replace = array(
				'%cod_causale%' => trim($codcausale),
				'%cod_conto%' => trim($codconto)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteConfigurazioneCausale;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}

	public function leggiProgressivoFattura($db, $utility, $catcliente, $codneg) {

		$array = $utility->getConfig();

		$filtro = "";

		if ($catcliente != "") {
			$filtro .= " AND progressivo_fattura.cat_cliente = '" . $catcliente . "'";
		}

		if ($codneg != "") {
			$filtro .= " AND progressivo_fattura.neg_progr = '" . $codneg . "'";
		}

		$replace = array(
				'%filtri_progressivi_fattura%' => $filtro
		);

		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaProgressivoFattura;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	public function updateProgressivoFattura($db, $utility, $catcliente, $codneg, $numfatt, $notatesta, $notapiede) {

		$array = $utility->getConfig();
		$replace = array(
				'%cat_cliente%' => trim($catcliente),
				'%neg_progr%' => trim($codneg),
				'%num_fattura_ultimo%' => trim($numfatt),
				'%nota_testa_fattura%' => trim($notatesta),
				'%nota_piede_fattura%' => trim($notapiede)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateProgressivoFattura;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
}

?>
