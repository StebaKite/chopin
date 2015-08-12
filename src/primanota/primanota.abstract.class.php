<?php

require_once 'chopin.abstract.class.php';

abstract class PrimanotaAbstract extends ChopinAbstract {

	private static $_instance = null;
	
	public static $messaggio;
	
	// Query --------------------------------------------------------------- 
	
	public static $queryCreaRegistrazione = "/primanota/creaRegistrazione.sql";
	public static $queryCreaDettaglioRegistrazione = "/primanota/creaDettaglioRegistrazione.sql";
	public static $queryCreaScadenza = "/primanota/creaScadenza.sql";
	
	public static $queryLeggiRegistrazione = "/primanota/leggiRegistrazione.sql";
	public static $queryLeggiDettagliRegistrazione = "/primanota/leggiDettagliRegistrazione.sql";
	public static $queryUpdateRegistrazione = "/primanota/updateRegistrazione.sql";
	public static $queryUpdateStatoRegistrazione = "/primanota/updateStatoRegistrazione.sql";
	public static $queryDeleteScadenza = "/primanota/deleteScadenza.sql";
	public static $queryDeleteDettaglioRegistrazione = "/primanota/deleteDettaglioRegistrazione.sql";	
	public static $queryDeleteRegistrazione = "/primanota/deleteRegistrazione.sql";

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
	public function inserisciRegistrazione($db, $utility, $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente) {
		
		$array = $utility->getConfig();	
		$replace = array(
				'%des_registrazione%' => trim($descreg),
				'%dat_scadenza%' => trim($datascad),
				'%dat_registrazione%' => trim($datareg),
				'%num_fattura%' => trim($numfatt),
				'%cod_causale%' => $causale,
				'%id_fornitore%' => $fornitore,
				'%id_cliente%' => $cliente
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);	
		/**
		 * Se la creazione della registrazione è andata bene vedo se inserire la scadenza
		*/
		if ($result) {

			$_SESSION['idRegistrazione'] = $db->getLastIdUsed();
				
			$oggi = date("d-m-Y");
			$dataOggi = strtotime($oggi);
			$dt = str_replace("'", "", $datascad);					// la datascad arriva con gli apici per il db
			$dataScadenza = strtotime(str_replace('/', '-', $dt));	// cambio i separatori altrimenti la strtotime non funziona
						
			if (($fornitore != "") && ($dataScadenza > $dataOggi)) {
				$this->inserisciScadenza($db, $utility, $_SESSION['idRegistrazione'], $datascad, $_SESSION["totaleDare"], $descreg);
			} 
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
	 * 
	 * @param unknown $db
	 * @param unknown $utility
	 * @param unknown $idRegistrazione
	 * @param unknown $datascad
	 * @param unknown $importo
	 * @param unknown $descreg
	 * @return unknown
	 */
	public function inserisciScadenza($db, $utility, $idRegistrazione, $datascad, $importo, $descreg) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($idRegistrazione),
				'%dat_scadenza%' => trim($datascad),
				'%imp_in_scadenza%' => trim($importo),
				'%nota_in_scadenza%' => trim($descreg)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryCreaScadenza;
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
	public function updateRegistrazione($db, $utility, $id_registrazione, $totaleDare, $descreg, $datascad, $datareg, $numfatt, $causale, $fornitore, $cliente, $stareg) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_registrazione%' => trim($id_registrazione),
				'%des_registrazione%' => trim($descreg),
				'%dat_scadenza%' => trim($datascad),
				'%dat_registrazione%' => trim($datareg),
				'%sta_registrazione%' => trim($stareg),
				'%num_fattura%' => trim($numfatt),
				'%cod_causale%' => $causale,
				'%id_fornitore%' => $fornitore,
				'%id_cliente%' => $cliente
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateRegistrazione;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);

		/**
		 * Se l'aggiornamentoa della registrazione è andata bene cancello la data scadenza e vedo se inserirla 
		 */
		if ($result) {
			$replace = array(
					'%id_registrazione%' => trim($id_registrazione)
			);
			$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenza;
			$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
			$result = $db->execSql($sql);
			
			$oggi = date("d-m-Y");
			$dataOggi = strtotime($oggi);
			$dt = str_replace("'", "", $datascad);					// la datascad arriva con gli apici per il db
			$dataScadenza = strtotime(str_replace('/', '-', $dt));	// cambio i separatori altrimenti la strtotime non funziona
		
			if (($fornitore != "") && ($dataScadenza > $dataOggi)) {
				$this->inserisciScadenza($db, $utility, $id_registrazione, $datascad, $totaleDare, $descreg);
			}
			return TRUE;
		}
		return FALSE;
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
	
}

?>
