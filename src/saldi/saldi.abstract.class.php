<?php

require_once 'chopin.abstract.class.php';

abstract class SaldiAbstract extends ChopinAbstract {

	private static $_instance = null;
	
	public static $messaggio;
	
	// Query --------------------------------------------------------------- 
	
	private static $queryRicercaConto = "/saldi/ricercaConto.sql";
	private static $querySaldoConto = "/saldi/saldoConto.sql";
	
	
	
	
	function __construct() {
	}
	
	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new SaldiAbstract();
	
		return self::$_instance;
	}
	
	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}
	
	// ------------------------------------------------
	
	public function getMessaggio() {
		return self::$messaggio;
	}	
	
	/**
	 * Metodi comuni di utilita della prima note
	 */
	
	/**
	 * Se il saldo c'è già sulla tabella viene aggiornato altrimenti viene inserito
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codnegozio
	 * @param unknown $codconto
	 * @param unknown $codsottoconto
	 * @param unknown $datsaldo
	 * @param unknown $dessaldo
	 * @param unknown $impsaldo
	 * @param unknown $inddareavere
	 * @return unknown
	 */
	public function inserisciSaldo($db, $utility, $codnegozio, $codconto, $codsottoconto, $datsaldo, $dessaldo, $impsaldo, $inddareavere) {
	
		$result = $this->leggiSaldo($db, $utility, $codnegozio, $codconto, $codsottoconto, $datsaldo);
	
		if (pg_num_rows($result) > 0) {
			$array = $utility->getConfig();
			$replace = array(
					'%cod_negozio%' => $codnegozio,
					'%cod_conto%' => $codconto,
					'%cod_sottoconto%' => $codsottoconto,
					'%dat_saldo%' => $datsaldo,
					'%des_saldo%' => $dessaldo,
					'%imp_saldo%' => $impsaldo,
					'%ind_dareavere%' => $inddareavere
			);
			$sqlTemplate = self::$root . $array['query'] . self::$queryAggiornaSaldo;
		}
		else {
			$array = $utility->getConfig();
			$replace = array(
					'%cod_negozio%' => $codnegozio,
					'%cod_conto%' => $codconto,
					'%cod_sottoconto%' => $codsottoconto,
					'%dat_saldo%' => $datsaldo,
					'%des_saldo%' => $dessaldo,
					'%imp_saldo%' => $impsaldo,
					'%ind_dareavere%' => $inddareavere
			);
			$sqlTemplate = self::$root . $array['query'] . self::$queryCreaSaldo;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $codnegozio
	 * @param unknown $codconto
	 * @param unknown $codsottoconto
	 * @param unknown $datsaldo
	 * @return unknown
	 */
	public function leggiSaldo($db, $utility, $codnegozio, $codconto, $codsottoconto, $datsaldo) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%cod_negozio%' => $codnegozio,
				'%cod_conto%' => $codconto,
				'%cod_sottoconto%' => $codsottoconto,
				'%dat_saldo%' => $datsaldo
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiSaldo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
	/**
	 *
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $pklavoro
	 * @param unknown $stato
	 * @return unknown
	 */
	public function cambioStatoLavoroPianificato($db, $utility, $pklavoro, $stato) {
	
		$replace = array(
				'%sta_lavoro%' => $stato,
				'%pk_lavoro_pianificato%' => $pklavoro
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryCambioStatoLavoroPianificato;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	
		return $result;
	}

	public function prelevaConti($db, $utility) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaConto;
		
		$sql = $utility->getTemplate($sqlTemplate);
		$result = $db->execSql($sql);
		
		return $result;
	}
	
	
	
	
	
	
	








}

?>
