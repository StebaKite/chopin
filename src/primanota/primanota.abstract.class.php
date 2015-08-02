<?php

require_once 'chopin.abstract.class.php';

abstract class primanotaAbstract extends chopinAbstract {

	public static $messaggio;

	
	
	
	// Query --------------------------------------------------------------- 
	
	public static $queryCreaRegistrazione = "/primanota/creaRegistrazione.sql";
	public static $queryCreaDettaglioRegistrazione = "/primanota/creaDettaglioRegistrazione.sql";
	public static $queryCreaScadenza = "/primanota/creaScadenza.sql";
	
	
	
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
	public function inserisciRegistrazione($db, $utility, $descreg, $datascad, $numfatt, $causale, $fornitore, $cliente) {
		
		$array = $utility->getConfig();	
		$replace = array(
				'%des_registrazione%' => trim($descreg),
				'%dat_scadenza%' => trim($datascad),
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
	
}

?>
