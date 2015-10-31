<?php

require_once 'chopin.abstract.class.php';

abstract class SaldiAbstract extends ChopinAbstract {

	private static $_instance = null;
	
	public static $messaggio;
	
	// Query --------------------------------------------------------------- 
	
	public static $queryRicercaConto = "/saldi/ricercaConto.sql";
	public static $querySaldoConto = "/saldi/saldoConto.sql";
	public static $queryRicercaDateRiportoSaldi = "/saldi/ricercaDateRiportoSaldi.sql";
	public static $queryLeggiSaldi = "/saldi/ricercaSaldi.sql";	
	public static $queryCreaSaldo = "/saldi/creaSaldo.sql";	
	public static $queryPrelevaTuttiConti = "/configurazioni/leggiTuttiConti.sql";
	
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
	
	public function caricaDateRiportoSaldo($utility, $db) {
	
		$array = $utility->getConfig();
	
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaDateRiportoSaldi;
		$sql = $utility->getTemplate($sqlTemplate);
		$result = $db->getData($sql);

		foreach(pg_fetch_all($result) as $row) {
		
			if ($row['dat_saldo'] == $_SESSION["datarip_saldo"]) {
				$elencoDateRiportoSaldi .= "<option value='" . $row['dat_saldo'] . "' selected >" . date("d/m/Y",strtotime($row['dat_saldo'])) . "</option>";
			}
			else {
				$elencoDateRiportoSaldi .= "<option value='" . $row['dat_saldo'] . "'>" . date("d/m/Y",strtotime($row['dat_saldo'])) . "</option>";
			}
		}
		return $elencoDateRiportoSaldi;
	}
	
	public function leggiSaldi($db, $utility, $codneg, $datarip) {
	
		$replace = array(
				'%cod_negozio%' => $codneg,
				'%dat_saldo%' => $datarip
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiSaldi;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		
		$result = $db->getData($sql);
		
		if (pg_num_rows($result) > 0) {
			$_SESSION['saldiTrovati'] = $result;
			$_SESSION['numSaldiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['saldiTrovati']);
			$_SESSION['numSaldiTrovati'] = 0;
		}	
		return $result;
	}
	
	public function creaNuovoSaldo($db, $utility, $codneg, $codconto, $codsottoconto, $datsaldo, $dessaldo, $impsaldo, $dareavere) {
		
		$array = $utility->getConfig();
		$replace = array(
				'%cod_negozio%' => trim($codneg),
				'%cod_conto%' => trim($codconto),
				'%cod_sottoconto%' => trim($codsottoconto),
				'%dat_saldo%' => trim($datsaldo),
				'%des_saldo%' => trim($dessaldo),
				'%imp_saldo%' => trim($impsaldo),
				'%ind_dareavere%' => trim($dareavere)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaSaldo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		return $result;
	}
	
	public function caricaTuttiConti($utility, $db) {
	
		$array = $utility->getConfig();
	
		$sqlTemplate = self::$root . $array['query'] . self::$queryPrelevaTuttiConti;
		$sql = $utility->getTemplate($sqlTemplate);
		$result = $db->getData($sql);
	
		foreach(pg_fetch_all($result) as $row) {
			
			$conto = $row['cod_conto'] . '-' . $row['cod_sottoconto'];
			
			if ($conto == $_SESSION["codconto"]) {
				$elenco_conti .= "<option value='" . $row['cod_conto'] . '-' . $row['cod_sottoconto'] . "' selected >" . $row['cod_conto'] . '-' . $row['cod_sottoconto'] . "</option>" ;
			}
			else {
				$elenco_conti .= "<option value='" . $row['cod_conto'] . '-' . $row['cod_sottoconto'] . "'>" . $row['cod_conto'] . '-' . $row['cod_sottoconto'] . "</option>" ;
			}
		}
		return $elenco_conti;
	}
	
	
}

?>
