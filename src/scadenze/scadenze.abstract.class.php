<?php

require_once 'nexus6.abstract.class.php';

abstract class ScadenzeAbstract extends Nexus6Abstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryUpdateStatoScadenzaCliente = "/scadenze/updateStatoScadenzaCliente.sql";

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	// Metodi comuni di utilita della prima note ---------------------------

	public function leggiScadenze($db, $utility, $datascad_da, $datascad_a) {

		$array = $utility->getConfig();
		$replace = array(
				'%dat_scadenza_da%' => $datascad_da,
				'%dat_scadenza_a%' => $datascad_a
		);

		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaScadenze;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	public function cambiaStatoScadenzaCliente($db, $utility, $idscadenza, $statoScadenza) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_scadenza%' => trim($idscadenza),
				'%sta_scadenza%' => trim($statoScadenza)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoScadenzaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	}
}

?>