<?php

require_once 'saldi.abstract.class.php';

/**
 * Questa classe è rieseguibile.
 * @author stefano
 *
 */
class PianificazioniLavoriPrimoSemestre extends SaldiAbstract  {

	public static $messaggio;
	public static $queryCancellaPianificazioniSemestre = "/main/cancellaLavoriEseguiti.sql";
	public static $queryCreaLavoroPianificato = "/main/creaLavoroPianificato.sql";
	
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

			self::$_instance = new PianificazioniLavoriPrimoSemestre();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start($db, $pklavoro) {

		require_once 'menubanner.template.php';
		require_once 'utility.class.php';

		/**
		 * Cancello tutte le pianificazioni del semestre precedente che sono già state eseguite
		 */
		$utility = Utility::getInstance();
		
		$replace = array(
				'%datalavoro_da%' => '01-07-' . date("Y"),
				'%datalavoro_a%' =>  '31-12-' . date("Y")
		);
			
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryCancellaPianificazioniSemestre;
		
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
		
		/**
		 * Se tutti i lavori del semestre sono stati rimossi inserisco i nuovi lavori pianificati
		 */
		if ($result) {

			$anno = date("Y") + 1;
			$fileEsecuzioneLavoro = "riportoSaldoPeriodico";
			$classeEsecuzioneLavoro = "RiportoSaldoPeriodico";
			$statoLavoro = "00";
			
			if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-01-01', 'Riporto saldi ' .  SELF::$mese['01'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
			if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-02-01', 'Riporto saldi ' .  SELF::$mese['02'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
			if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-03-01', 'Riporto saldi ' .  SELF::$mese['03'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
			if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-04-01', 'Riporto saldi ' .  SELF::$mese['04'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
			if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-05-01', 'Riporto saldi ' .  SELF::$mese['05'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
			if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-06-01', 'Riporto saldi ' .  SELF::$mese['06'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
			if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-06-30', 'Pianificazioni semestre 2', 'pianificazioniLavoriSecondoSemestre', 'PianificazioniLavoriSecondoSemestre', $statoLavoro)) return FALSE;
				
			error_log("Pianificazione lavori del primo semestre anno " . $anno);			
			$this->cambioStatoLavoroPianificato($db, $utility, $pklavoro, '10');
			return TRUE;
		}
		return FALSE;
	}	
}

?>