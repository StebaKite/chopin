<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep3 extends StrumentiAbstract {

	private static $_instance = null;

	public static $azioneConferma = "../strumenti/cambiaContoStep3Facade.class.php?modo=go";
	
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
	
			self::$_instance = new CambiaContoStep3();
	
		return self::$_instance;
	}
	
	public function start() {

		require_once 'cambiaContoStep3.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
		
		$cambiaContoStep3Template = CambiaContoStep3Template::getInstance();
		$this->preparaPagina($cambiaContoStep3Template);
		
		// compone la pagina
		include($testata);
		$cambiaContoStep3Template->displayPagina();
		include($piede);
	}

	public function go() {
		
		require_once 'cambiaContoStep3.template.php';
		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'cambiaContoStep1.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		/**
		 * Sposto le registrazioni in sessione sul nuovo conto
		 */

		$cambiaContoStep3Template = CambiaContoStep3Template::getInstance();

		$db = Database::getInstance();
		$db->beginTransaction();
		
		$utility = Utility::getInstance();
		
		if ($this->spostaDettagliRegistrazioni($db, $utility)) {

			/**
			 * Rigenero i saldi a partire dal mese successivo a quello aggiornato dallo spostamento sino all'ultimo 
			 * già eseguito
			 */
				
			$array = $utility->getConfig();
			
			if ($array['lavoriPianificatiAttivati'] == "Si") {
			
				$datareg_da = strtotime(str_replace('/', '-', $_SESSION["datareg_da"]));
				$this->rigenerazioneSaldi($db, $utility, $datareg_da);
			}

			$db->commitTransaction();

			$_SESSION["messaggioCambioConto"] = "Operazione effettuata con successo";
			
			$cambiaContoStep1 = CambiaContoStep1::getInstance();
			$cambiaContoStep1->start();				
		}
		else {
			
			$db->rollbackTransaction();
				
			$this->preparaPagina($cambiaContoStep3Template);
			include($testata);
			$cambiaContoStep3Template->displayPagina();
			
			$_SESSION["messaggio"] = "Errore fatale durante lo spostamento dei dettagli" ;
			
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
				
			include(self::$piede);
				
		}
	}

	protected function spostaDettagliRegistrazioni($db, $utility) {
				
		$registrazioniTrovate = $this->caricaRegistrazioniConto($utility, $db);		
		$conto = split(" - ", $_SESSION["conto_sel_nuovo"]);
		
		foreach(pg_fetch_all($registrazioniTrovate) as $row) {

			$result = $this->updateDettaglioRegistrazione($db, $utility, $row['id_dettaglio_registrazione'], $conto[0], $conto[1]);
			if (!$result) return false;
		}
		return true;
	}	
	
	public function preparaPagina($ricercaRegistrazioneTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$_SESSION["azione"] = self::$azioneConferma;
		$_SESSION["titoloPagina"] = "%ml.cambioContoStep3%";
	}
}
	
?>