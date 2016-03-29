<?php

require_once 'chopin.abstract.class.php';

abstract class PrimanotaAbstract extends ChopinAbstract {

	private static $_instance = null;
	
	public static $messaggio;
	
	// Query --------------------------------------------------------------- 
	
	public static $queryCreaRegistrazione = "/primanota/creaRegistrazione.sql";
	public static $queryCreaDettaglioRegistrazione = "/primanota/creaDettaglioRegistrazione.sql";
	public static $queryCreaScadenza = "/primanota/creaScadenza.sql";
	public static $queryUpdateScadenza = "/primanota/updateScadenza.sql";
	public static $queryDeleteScadenzaRegistrazione = "/primanota/deleteScadenzaRegistrazione.sql";
	public static $queryCreaScadenzaCliente = "/primanota/creaScadenzaCliente.sql";
	
	public static $queryLeggiRegistrazione = "/primanota/leggiRegistrazione.sql";
	public static $queryLeggiDettagliRegistrazione = "/primanota/leggiDettagliRegistrazione.sql";
	public static $queryLeggiScadenzeRegistrazione = "/primanota/leggiScadenzeRegistrazione.sql";
	public static $queryUpdateRegistrazione = "/primanota/updateRegistrazione.sql";
	public static $queryUpdateStatoRegistrazione = "/primanota/updateStatoRegistrazione.sql";
	
	public static $queryDeleteDettaglioRegistrazione = "/primanota/deleteDettaglioRegistrazione.sql";	
	public static $queryDeleteRegistrazione = "/primanota/deleteRegistrazione.sql";
	
	public static $queryLeggiScadenzeAperteFornitore = "/primanota/ricercaScadenzeAperteFornitore.sql";
	public static $queryLeggiScadenzeAperteCliente = "/primanota/ricercaScadenzeAperteCliente.sql";
	public static $queryLeggiScadenzeFornitore = "/primanota/ricercaScadenzeFornitore.sql";
	public static $queryLeggiScadenzeCliente = "/primanota/ricercaScadenzeCliente.sql";
	public static $queryPrelevaScadenzaCliente = "/primanota/leggiScadenzaCliente.sql";
	public static $queryPrelevaScadenzaFornitore = "/primanota/leggiScadenzaFornitore.sql";
	public static $queryUpdateStatoScadenza = "/primanota/updateStatoScadenzaFornitore.sql";
	public static $queryUpdateStatoScadenzaCliente = "/primanota/updateStatoScadenzaCliente.sql";
	public static $queryLeggiFatturaFornitore = "/primanota/ricercaFatturaFornitore.sql";
	public static $queryLeggiFatturaCliente = "/primanota/ricercaFatturaCliente.sql";

	public static $queryDeleteScadenza = "/primanota/deleteScadenza.sql";
	public static $queryDeleteScadenzaCliente = "/primanota/deleteScadenzaCliente.sql";
	public static $queryPrelevaRegistrazioneOriginaleCliente = "/primanota/leggiRegistrazioneOriginaleCliente.sql";
	public static $queryPrelevaRegistrazioneOriginaleFornitore = "/primanota/leggiRegistrazioneOriginaleFornitore.sql";
	public static $queryTrovaCorrispettivo = "/primanota/trovaCorrispettivo.sql";
	
	public static $queryTrovaScadenzaFornitore = "/primanota/trovaScadenzaFornitore.sql";
	
	function __construct() {
	}
	
	private function  __clone() { }
	
	/**
	 * Singleton Pattern
	 */
	
	public static function getInstance() {
	
		if( !is_object(self::$_instance) )
	
			self::$_instance = new PrimanotaAbstract();
	
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
	
	// Metodi comuni di utilita della prima note ---------------------------
	
	/**
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $descreg
	 * @param unknown $datascad
	 * @param unknown $numfatt
	 * @param unknown $causale
	 * @param unknown $fornitore
	 * @param unknown $cliente
	 * @return unknown
	 */
	public function inserisciRegistrazione($db, $utility, $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $codneg, $stareg) {
		
		$array = $utility->getConfig();	
		$replace = array(
				'%des_registrazione%' => trim($descreg),
				'%dat_scadenza%' => trim($datascad),
				'%dat_registrazione%' => trim($datareg),
				'%dat_inserimento%' => date("Y-m-d H:i:s"),
				'%num_fattura%' => trim($numfatt),
				'%cod_causale%' => $causale,
				'%id_fornitore%' => $fornitore,
				'%id_cliente%' => $cliente,
				'%sta_registrazione%' => $stareg,				
				'%cod_negozio%' => $codneg
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);	
		
		if ($result) {
			$_SESSION['idRegistrazione'] = $db->getLastIdUsed();		// metto in sessione l'id generato dall'inserimento
		}
		return $result;
	}
	
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
	 * Il metodo inserisce una scadenza per un fornitore
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idRegistrazione
	 * @param unknown $datascad
	 * @param unknown $importo
	 * @param unknown $descreg
	 * @return unknown
	 */
	public function inserisciScadenza($db, $utility, $idRegistrazione, $datascad, $importo, 
			$descreg, $tipaddebito, $codneg, $fornitore, $numfatt, $staScadenza) {

		$array = $utility->getConfig();
		$replace = array(
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
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaScadenza;
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
		
		if ($scadenza_esistente) {
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
	public function leggiRegistrazione($db, $utility, $idregistrazione) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($idregistrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiRegistrazione;
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
			$codneg, $staScadenza) {
		
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
				'%id_cliente%' => $cliente
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
	 * @param unknown $id_dettaglioregistrazione
	 */
	public function cancellaRegistrazione($db, $utility, $id_registrazione) {
	
		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($id_registrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteRegistrazione;
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
	 * @param unknown $id_fornitore
	 * @param unknown $num_fattura
	 */
	public function cercaFatturaFornitore($db, $utility, $id_fornitore, $num_fattura, $dat_registrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_fornitore%' => trim($id_fornitore),
				'%num_fattura%' => trim($num_fattura),
				'%dat_registrazione%' => trim($dat_registrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiFatturaFornitore;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		return $db->getData($sql);
	}
	
	/**
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idcliente
	 * @param unknown $numfatt
	 */
	public function cercaFatturaCliente($db, $utility, $id_cliente, $num_fattura, $dat_registrazione) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_cliente%' => trim($id_cliente),
				'%num_fattura%' => trim($num_fattura),
				'%dat_registrazione%' => trim($dat_registrazione)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryLeggiFatturaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		return $db->getData($sql);		
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
	
}

?>
