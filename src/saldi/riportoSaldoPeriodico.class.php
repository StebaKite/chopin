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

		require_once 'utility.class.php';
		
		$riportoStatoPatrimoniale_Ok = FALSE;
		$riportoContoEconomico_Ok = FALSE;
		
		/**
		 * Determino il mese da estrarre rispetto alla data di esecuzione del lavoro pianificato
		 */

		$dataGenerazioneSaldo = $_SESSION["dataEsecuzioneLavoro"];	
		$dataEstrazioneRegistrazioni = date("Y/m/d", strtotime('-1 month', strtotime($_SESSION["dataEsecuzioneLavoro"])));
		
		$dataLavoro = explode("/", $dataEstrazioneRegistrazioni);
		$mesePrecedente = str_pad($dataLavoro[1], 2, "0", STR_PAD_LEFT);
		$descrizioneSaldo = "Riporto saldo di " . SELF::$mese[$mesePrecedente];
		
		$anno = ($mesePrecedente == 12) ? date("Y", strtotime('-1 year', strtotime($_SESSION["dataEsecuzioneLavoro"]))) : date("Y", strtotime($_SESSION["dataEsecuzioneLavoro"]));
		
		/**
		 * Riporto stato patrimoniale
		 */
		
		$utility = Utility::getInstance();
		
		$result = $this->prelevaStatoPatrimoniale($db, $utility);		

		if ($result) {
			
			$this->riportoStatoPatrimoniale($db, $pklavoro, $utility, $result, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo);

			$da = '01/' . $mesePrecedente . '/' . $anno;
			$a  = SELF::$ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;
			error_log("Riporto saldo stato patrimoniale, periodo : " . $da . " - " . $a);
			error_log("Data esecuzione riporto saldo : " . $dataGenerazioneSaldo);
			
			$riportoStatoPatrimoniale_Ok = TRUE;
		}
		
		/**
		 * Riporto conto economico.
		 * Il primo riporto dell'anno non viene fatto. I conti ripartono da zero.
		 */
		
		if (date("m/d", strtotime($_SESSION["dataEsecuzioneLavoro"])) != "01/01") {

			$result = $this->prelevaContoEconomico($db, $utility);
			
			if ($result) {

				$this->riportoContoEconomico($db, $pklavoro, $utility, $result, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo);
					
				$da = '01/' . $mesePrecedente . '/' . $anno;
				$a  = SELF::$ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;
				error_log("Riporto saldo conto economico, periodo : " . $da . " - " . $a);
				error_log("Data esecuzione riporto saldo : " . $dataGenerazioneSaldo);
				
				$riportoContoEconomico_Ok = TRUE;
			}				
		}
		
		/**
		 * Se uno dei due riporti è andato bene considero il lavoro eseguito  
		 */
		
		if (($riportoStatoPatrimoniale_Ok) or ($riportoContoEconomico_Ok)) { 
			$this->cambioStatoLavoroPianificato($db, $utility, $pklavoro, '10');			
			return TRUE;
		}
		else return FALSE;
	}
	
	private function riportoStatoPatrimoniale($db, $pklavoro, $utility, $statoPatrimoniale, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo) {

		require_once 'menubanner.template.php';
		
		$conti = pg_fetch_all($statoPatrimoniale);
			
		foreach($conti as $conto) {
				
			foreach(SELF::$negozi as $negozio){
					
				$replace = array(
						'%datareg_da%' => '01/' . $mesePrecedente . '/' . $anno,
						'%datareg_a%' => SELF::$ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno,
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
							 * L'attribuzione del segno tiene in considerazione sia il tipo di conto sia l'importo del saldo.
							 * Ad esempio: la cassa è un conto in Dare ma se il saldo risulta negativo viene riportato in Avere
							 * Lo stesso per un conto in Avere con un seldo maggiore di zero, viene riportato in Dare
							 */
													
							if (($row['tot_conto'] > 0) and ($row['tip_conto'] == 1)) $dareAvere = "D";	
							if (($row['tot_conto'] < 0) and ($row['tip_conto'] == 1)) $dareAvere = "A";

							if (($row['tot_conto'] > 0) and ($row['tip_conto'] == -1)) $dareAvere = "D";
							if (($row['tot_conto'] < 0) and ($row['tip_conto'] == -1)) $dareAvere = "A";
							
							$this->inserisciSaldo($db, $utility, $negozio, $conto['cod_conto'], $conto['cod_sottoconto'], $dataGenerazioneSaldo, $descrizioneSaldo, abs($row['tot_conto']), $dareAvere);
						}
					}
				}
			}
		}		
	}
	
	private function riportoContoEconomico($db, $pklavoro, $utility, $contoEconomico, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo) {

		require_once 'menubanner.template.php';
		
		$conti = pg_fetch_all($contoEconomico);
			
		foreach($conti as $conto) {
		
			foreach(SELF::$negozi as $negozio){
					
				$replace = array(
						'%datareg_da%' => '01/' . $mesePrecedente . '/' . $anno,
						'%datareg_a%' => SELF::$ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno,
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
	}
}

?>