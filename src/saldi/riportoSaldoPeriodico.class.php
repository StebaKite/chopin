<?php

require_once 'chopin.abstract.class.php';

class RiportoSaldoPeriodico extends ChopinAbstract {

	private static $messaggio;
	private static $queryRicercaConto = "/saldi/ricercaConto.sql";
	private static $querySaldoCondo = "/saldi/saldoConto.sql";

	public static $ggMese = array(
			'01' => '31',
			'02' => '28',
			'03' => '31',
			'04' => '30',
			'05' => '31',
			'06' => '30',
			'07' => '31',
			'08' => '31',
			'09' => '30',
			'10' => '31',
			'11' => '30',
			'12' => '31'
	);

	public static $mese = array(
			'01' => 'gennaio',
			'02' => 'febbraio',
			'03' => 'marzo',
			'04' => 'aprile',
			'05' => 'maggio',
			'06' => 'giugno',
			'07' => 'luglio',
			'08' => 'agosto',
			'09' => 'settembre',
			'10' => 'ottobre',
			'11' => 'novembre',
			'12' => 'dicembre'
	);
	
	public static $negozi = array('VIL','BRE','TRE');
	
	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new RiportoSaldoPeriodico();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'menubanner.template.php';
		require_once 'utility.class.php';
		require_once 'database.class.php';
		
		/**
		 * Prelevo tutti i conti
		 */
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$db->beginTransaction();
				
		$array = $utility->getConfig();
		
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaConto;
		$sql = $utility->getTemplate($sqlTemplate);
		$result = $db->execSql($sql);

		if ($result) {

			$conti = pg_fetch_all($result);

			$dataLavoro = explode("/", $_SESSION["dataEsecuzioneLavoro"]);
						
			
			$mesePrecedente = str_pad($dataLavoro[1] - 1, 2, "0", STR_PAD_LEFT);
			$dataGenerazioneSaldo = $_SESSION["dataEsecuzioneLavoro"];
			$descrizioneSaldo = "Riporto saldo di " . SELF::$mese[$mesePrecedente];
			
			foreach($conti as $conto) {
			
				foreach(SELF::$negozi as $negozio){
			
					$replace = array(
							'%datareg_da%' => '01/' . $mesePrecedente . '/' . date("Y"),
							'%datareg_a%' => SELF::$ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . date("Y"),
							'%codnegozio%' => $negozio,
							'%codconto%' => $conto['cod_conto'],
							'%codsottoconto%' => $conto['cod_sottoconto']
					);
			
					$array = $utility->getConfig();
					$sqlTemplate = self::$root . $array['query'] . self::$querySaldoCondo;
						
					$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
					$result = $db->execSql($sql);

					$saldo = pg_fetch_all($result);					
					
					if (result) {
						foreach($saldo as $row) {
							$dareAvere = ($row['tip_conto'] == 1) ? "D" : "A";
							$this->inserisciSaldo($db, $utility, $negozio, $conto['cod_conto'], $conto['cod_sottoconto'], $dataGenerazioneSaldo, $descrizioneSaldo, $row['tot_conto'], $dareAvere);								
						}
					}
				}
			}
			$db->commitTransaction();				
		}
	}
}

?>