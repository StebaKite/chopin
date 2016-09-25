<?php

require_once 'saldi.abstract.class.php';

/**
 * Questa classe è rieseguibile.
 * @author stefano
 *
 */
class PianificazioniLavoriSecondoSemestre extends SaldiAbstract  {

	public static $messaggio;
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

			self::$_instance = new PianificazioniLavoriSecondoSemestre();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start($db, $pklavoro) {

		require_once 'menubanner.template.php';
		require_once 'utility.class.php';

		/**
		 * Vengono pianificati i lavori per il secondo semestre dell'anno in corso.
		 * Questa esecuzione viene eseguita l'ultimo giorno del primo semestre, presumibilmente il 30/6
		 */
		
		$anno = date("Y");
		$fileEsecuzioneLavoro = "riportoSaldoPeriodico";
		$classeEsecuzioneLavoro = "RiportoSaldoPeriodico";
		$statoLavoro = "00";

		$utility = Utility::getInstance();		
		
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-07-01', 'Riporto saldi ' .  SELF::$mese['07'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-08-01', 'Riporto saldi ' .  SELF::$mese['08'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-09-01', 'Riporto saldi ' .  SELF::$mese['09'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-10-01', 'Riporto saldi ' .  SELF::$mese['10'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-11-01', 'Riporto saldi ' .  SELF::$mese['11'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-12-01', 'Riporto saldi ' .  SELF::$mese['12'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-12-30', 'Pianificazioni semestre 1', 'pianificazioniLavoriPrimoSemestre', 'PianificazioniLavoriPrimoSemestre', $statoLavoro)) return FALSE;

		echo "Pianificazione lavori del secondo semestre anno " . $anno;
		$this->cambioStatoLavoroPianificato($db, $utility, $pklavoro, '10');
		
		return TRUE;
	}
}

?>