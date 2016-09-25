<?php

require_once 'saldi.abstract.class.php';

/**
 * Questa classe è rieseguibile.
 * @author stefano
 *
 */
class PianificazioniLavoriPrimoSemestre extends SaldiAbstract  {

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

			self::$_instance = new PianificazioniLavoriPrimoSemestre();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start($db, $pklavoro) {

		require_once 'menubanner.template.php';
		require_once 'utility.class.php';
		
		/**
		 * Vengono inseriti i lavori per il primo semestre dell'anno prossimo.
		 * Questa esecuzione deve andare OBBLIGATORIAMENTE entro il primo giorno dell'anno. Presumibilmente il 31/12
		 */		
		
		$anno = date("Y") + 1;
		$fileEsecuzioneLavoro = "riportoSaldoPeriodico";
		$classeEsecuzioneLavoro = "RiportoSaldoPeriodico";
		$statoLavoro = "00";

		$utility = Utility::getInstance();
		
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-01-01', 'Riporto saldi ' .  SELF::$mese['01'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-02-01', 'Riporto saldi ' .  SELF::$mese['02'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-03-01', 'Riporto saldi ' .  SELF::$mese['03'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-04-01', 'Riporto saldi ' .  SELF::$mese['04'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-05-01', 'Riporto saldi ' .  SELF::$mese['05'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-06-01', 'Riporto saldi ' .  SELF::$mese['06'], $fileEsecuzioneLavoro, $classeEsecuzioneLavoro, $statoLavoro)) return FALSE;
		if (!$this->inserisciLavoroPianificato($db, $utility, $anno . '-06-30', 'Pianificazioni semestre 2', 'pianificazioniLavoriSecondoSemestre', 'PianificazioniLavoriSecondoSemestre', $statoLavoro)) return FALSE;
			
		echo "Pianificazione lavori del primo semestre anno " . $anno;			
		$this->cambioStatoLavoroPianificato($db, $utility, $pklavoro, '10');
		return TRUE;
	}	
}

?>