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

		self::$root = '/var/www/html';

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

		if ($this->isAnnoBisestile($anno)) {
			$ggMese = array(
					'01' => '31', '02' => '29', '03' => '31', '04' => '30', '05' => '31', '06' => '30',
					'07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31'
			);
		}
		else {
			$ggMese = array(
					'01' => '31', '02' => '28', '03' => '31', '04' => '30', '05' => '31', '06' => '30',
					'07' => '31', '08' => '31', '09' => '30', '10' => '31', '11' => '30', '12' => '31'
			);
		}
		
		/**
		 * Riporto stato patrimoniale
		 */
		
		$utility = Utility::getInstance();
		
		$result = $this->prelevaStatoPatrimoniale($db, $utility);		
		
		if ($result) {
			
			$this->riportoStatoPatrimoniale($db, $pklavoro, $utility, $result, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese);

			$da = '01/' . $mesePrecedente . '/' . $anno;
			$a  = $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;
			
			$riportoStatoPatrimoniale_Ok = TRUE;
		}
		
		/**
		 * Riporto conto economico.
		 * Il primo riporto dell'anno non viene fatto. I conti ripartono da zero.
		 */
		
		if (date("m/d", strtotime($_SESSION["dataEsecuzioneLavoro"])) != "01/01") {

			$result = $this->prelevaContoEconomico($db, $utility);
			
			if ($result) {

				$this->riportoContoEconomico($db, $pklavoro, $utility, $result, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese);
					
				$da = '01/' . $mesePrecedente . '/' . $anno;
				$a  = $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno;
				
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
	
	private function riportoStatoPatrimoniale($db, $pklavoro, $utility, $statoPatrimoniale, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese) {

		require_once 'menubanner.template.php';

		$array = $utility->getConfig();
				
		$conti = pg_fetch_all($statoPatrimoniale);

		/**
		 * Scansione di tutti i conti dello Stato Patrimoniale
		 */
		
		foreach($conti as $conto) {
		
			/**
			 * Per ciascun conto effettuo la totalizzazione delle registrazioni per ciascun negozio
			 */
			$dareAvere_conto = ($conto['tip_conto'] = "Avere") ? "A" : "D";		// prelevo il tipo del conto Dare/Avere
								
			foreach(SELF::$negozi as $negozio){
					
				$replace = array(
						'%datareg_da%' => '01/' . $mesePrecedente . '/' . $anno,
						'%datareg_a%' => $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno,
						'%codnegozio%' => $negozio,
						'%codconto%' => $conto['cod_conto'],
						'%codsottoconto%' => $conto['cod_sottoconto']
				);
					
				$array = $utility->getConfig();
				$sqlTemplate = self::$root . $array['query'] . self::$querySaldoConto;
		
				$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);				
				$result = $db->execSql($sql);
					
				if ($result) {
						
					$totale_conto = 0;	//default
					$dareAvere = "";	//default

					/**
					 * Faccio la somma algebrica di tutti i totali estratti.
					 * Normalmente dalla query di totalizzazione viene fuori una riga con un totale, ma nel caso
					 * di conti con importo negativo e segno contrario escono due righe.
					 *
					 */
								
					foreach(pg_fetch_all($result) as $row) {							
 						$totale_conto = $totale_conto + $row['tot_conto'];
					}
					
					/**
					 * L'attribuzione del segno viene fatto osservanto il totale ottenuto dalla somma algebrica degli importi
					 */

					$dareAvere = ($totale_conto > 0) ? "D" : "A";					
					$this->inserisciSaldo($db, $utility, $negozio, $conto['cod_conto'], $conto['cod_sottoconto'], $dataGenerazioneSaldo, $descrizioneSaldo, abs($totale_conto), $dareAvere);
				}
			}
		}		
	}
	
	private function riportoContoEconomico($db, $pklavoro, $utility, $contoEconomico, $mesePrecedente, $anno, $dataGenerazioneSaldo, $descrizioneSaldo, $ggMese) {

		require_once 'menubanner.template.php';
		
		$conti = pg_fetch_all($contoEconomico);
			
		foreach($conti as $conto) {
		
			foreach(SELF::$negozi as $negozio){
					
				$replace = array(
						'%datareg_da%' => '01/' . $mesePrecedente . '/' . $anno,
						'%datareg_a%' => $ggMese[$mesePrecedente] . '/' . $mesePrecedente . '/' . $anno,
						'%codnegozio%' => $negozio,
						'%codconto%' => $conto['cod_conto'],
						'%codsottoconto%' => $conto['cod_sottoconto']
				);
					
				$array = $utility->getConfig();
				$sqlTemplate = self::$root . $array['query'] . self::$querySaldoConto;
		
				$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
				$result = $db->execSql($sql);
					
				if (result) {
					foreach(pg_fetch_all($result) as $row) {
						
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