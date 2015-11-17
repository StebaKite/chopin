<?php

require_once 'saldi.abstract.class.php';

/**
 * Questa classe è rieseguibile.
 * Se un saldo per un conto e una data c'è già in tabella viene aggiornato altrimenti viene inserito
 * @author stefano
 *
 */
class RiportoSaldoPeriodico extends SaldiAbstract {

	public static $messaggio;
	public static $querySaldoConto = "/saldi/saldoConto.sql";
	
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

	public function start($db, $pklavoro) {

		require_once 'menubanner.template.php';
		require_once 'utility.class.php';
		
		/**
		 * Prelevo tutti i conti, se ci sono conti faccio il riporto altrimenti esco
		 */
		$utility = Utility::getInstance();
		
		$result = $this->prelevaConti($db, $utility);

		if ($result) {

			$conti = pg_fetch_all($result);

			$dataGenerazioneSaldo = $_SESSION["dataEsecuzioneLavoro"];
			$dataEstrazioneRegistrazioni = date("Y/m/d", strtotime('-1 month', strtotime($_SESSION["dataEsecuzioneLavoro"])));
				
			$dataLavoro = explode("/", $dataEstrazioneRegistrazioni);
			$mesePrecedente = str_pad($dataLavoro[1], 2, "0", STR_PAD_LEFT);
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
					$sqlTemplate = self::$root . $array['query'] . self::$querySaldoConto;
						
					$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
					$result = $db->execSql($sql);

					$saldo = pg_fetch_all($result);					
					
					if (result) {
						foreach($saldo as $row) {

							/**
							 * Se il conto ha un totale movimenti = zero il saldo non viene riportato
							 */
							if ($row['tot_conto'] != 0) {
								
								/**
								 * tip_conto =  1 > Dare
								 * tip_conto = -1 > Avere
								 */
								$dareAvere = ($row['tip_conto'] == 1) ? "D" : "A";
								$this->inserisciSaldo($db, $utility, $negozio, $conto['cod_conto'], $conto['cod_sottoconto'], $dataGenerazioneSaldo, $descrizioneSaldo, abs($row['tot_conto']), $dareAvere);								
							}
						}
					}
				}
			}

			$da = '01/' . $mesePrecedente . '/' . date("Y");
			$a  = SELF::$ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . date("Y");
			error_log("Riporto saldo periodo : " . $da . " - " . $a);
			error_log("Data esecuzione riporto saldo : " . $dataGenerazioneSaldo);
				
			$this->cambioStatoLavoroPianificato($db, $utility, $pklavoro, '10');			
			return TRUE;				
		}
		return FALSE;
	}	
}

?>