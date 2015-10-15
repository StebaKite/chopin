<?php

require_once 'chopin.abstract.class.php';

abstract class PrimanotaAbstract extends ChopinAbstract {

	private static $_instance = null;
	
	public static $messaggio;
	
	// Query --------------------------------------------------------------- 
	
	public static $queryCreaRegistrazione = "/primanota/creaRegistrazione.sql";
	public static $queryCreaDettaglioRegistrazione = "/primanota/creaDettaglioRegistrazione.sql";
	public static $queryCreaScadenza = "/primanota/creaScadenza.sql";
	public static $queryCreaScadenzaCliente = "/primanota/creaScadenzaCliente.sql";
	
	public static $queryLeggiRegistrazione = "/primanota/leggiRegistrazione.sql";
	public static $queryLeggiDettagliRegistrazione = "/primanota/leggiDettagliRegistrazione.sql";
	public static $queryUpdateRegistrazione = "/primanota/updateRegistrazione.sql";
	public static $queryUpdateStatoRegistrazione = "/primanota/updateStatoRegistrazione.sql";
	public static $queryDeleteScadenza = "/primanota/deleteScadenza.sql";
	public static $queryDeleteScadenzaCliente = "/primanota/deleteScadenzaCliente.sql";
	
	public static $queryDeleteDettaglioRegistrazione = "/primanota/deleteDettaglioRegistrazione.sql";	
	public static $queryDeleteRegistrazione = "/primanota/deleteRegistrazione.sql";
	
	public static $queryLeggiScadenzeAperteFornitore = "/primanota/ricercaScadenzeAperteFornitore.sql";
	public static $queryLeggiScadenzeAperteCliente = "/primanota/ricercaScadenzeAperteCliente.sql";
	public static $queryLeggiScadenzeFornitore = "/primanota/ricercaScadenzeFornitore.sql";
	public static $queryLeggiScadenzeCliente = "/primanota/ricercaScadenzeCliente.sql";
	public static $queryUpdateStatoScadenza = "/primanota/updateStatoScadenzaFornitore.sql";
	public static $queryUpdateStatoScadenzaCliente = "/primanota/updateStatoScadenzaCliente.sql";
	
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
		/**
		 * Se la creazione della registrazione è andata bene vedo se inserire la scadenza per il fornitore o per il cliente
		*/
		if ($result) {

			$_SESSION['idRegistrazione'] = $db->getLastIdUsed();
			
			if ($fornitore != "null") {
				
				if ($datascad != "null") {
				
					$data = str_replace("'", "", $datascad);					// la datascad arriva con gli apici per il db
					$dataScadenza = strtotime(str_replace('/', '-', $data));	// cambio i separatori altrimenti la strtotime non funziona
						
					$data1 = str_replace("'", "", $datareg);					// la datareg arriva con gli apici per il db
					$dataRegistrazione = strtotime(str_replace('/', '-', $data1));
						
					$tipAddebito_fornitore = "";
					$staScadenza = "00"; 	// aperta
						
					if ($dataScadenza > $dataRegistrazione) {
				
						$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
						foreach(pg_fetch_all($result_fornitore) as $row) {
							$tipAddebito_fornitore = $row['tip_addebito'];
						}
				
						$this->inserisciScadenza($db, $utility, $_SESSION['idRegistrazione'], $datascad, $_SESSION["totaleDare"],
								$descreg, $tipAddebito_fornitore, $codneg, $fornitore, trim($numfatt), $staScadenza);
					}
				}
			}
			else {
				if ($cliente != "null") {

					$tipAddebito_cliente = "";
					$staScadenza = "00"; 	// aperta
								
					$result_cliente = $this->leggiIdCliente($db, $utility, $cliente);
					foreach(pg_fetch_all($result_cliente) as $row) {
						$tipAddebito_cliente = $row['tip_addebito'];
					}
								
					$this->inserisciScadenzaCliente($db, $utility, $_SESSION['idRegistrazione'], $datareg, $_SESSION["totaleDare"],
							$descreg, $tipAddebito_cliente, $codneg, $cliente, trim($numfatt), $staScadenza);
				}
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

		if ($result) {
			
			/**
			 * Se l'aggiornamento della registrazione è andata bene cancello la data scadenza del fornitore
			 * e vedo se inserirla
			 */
					
			if ($fornitore != "null") {

				$replace = array(
						'%id_registrazione%' => trim($id_registrazione)
				);
				$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenza;
				$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
				$result = $db->execSql($sql);
					
				$data = str_replace("'", "", $datascad);					// la datascad arriva con gli apici per il db
				$dataScadenza = strtotime(str_replace('/', '-', $data));	// cambio i separatori altrimenti la strtotime non funziona
					
				$data1 = str_replace("'", "", $datareg);					// la datareg arriva con gli apici per il db
				$dataRegistrazione = strtotime(str_replace('/', '-', $data1));
				
				if ($dataScadenza > $dataRegistrazione) {
				
					$result_fornitore = $this->leggiIdFornitore($db, $utility, $fornitore);
					foreach(pg_fetch_all($result_fornitore) as $row) {
						$tipAddebito_fornitore = $row['tip_addebito'];
					}
					$this->inserisciScadenza($db, $utility, $id_registrazione, $datascad, $totaleDare,
							$descreg, $tipAddebito_fornitore, $codneg, $fornitore, trim($numfatt), $staScadenza);
				}				
			}			
			else {
				
				if ($cliente != "null") {

					/**
					 * Se è un cliente cancello la scadenza cliente e la ricreo.
					 */
					
					$replace = array(
							'%id_registrazione%' => trim($id_registrazione)
					);
					$sqlTemplate = self::$root . $array['query'] . self::$queryDeleteScadenzaCliente;
					$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
					$result = $db->execSql($sql);
					
					$result_cliente = $this->leggiIdCliente($db, $utility, $cliente);
					foreach(pg_fetch_all($result_cliente) as $row) {
						$tipAddebito_cliente = $row['tip_addebito'];
					}
			
					$this->inserisciScadenzaCliente($db, $utility, $_SESSION['idRegistrazione'], $datareg, $_SESSION["totaleDare"],
							$descreg, $tipAddebito_cliente, $codneg, $cliente, trim($numfatt), $staScadenza);
				}
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
	
}

?>
