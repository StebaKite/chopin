<?php

require_once 'configurazioni.abstract.class.php';

class GeneraMastrinoConto extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneGeneraMastrinoConto = "../configurazioni/generaMastrinoContoFacade.class.php?modo=go";
	public static $queryRicercaRegistrazioniConto = "/configurazioni/ricercaRegistrazioniConto.sql";

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

			self::$_instance = new GeneraMastrinoConto();

		return self::$_instance;
	}

	public function start() {}
	
	public function go() {
	
		require_once 'generaMastrinoConto.template.php';
		require_once 'ricercaConto.class.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];
	
		unset($_SESSION["registrazioniTrovate"]);
		unset($_SESSION['bottoneEstraiPdf']);
	
		$generaMastrinoContoTemplate = GeneraMastrinoContoTemplate::getInstance();
	
		if ($this->ricercaDati($utility)) {
	
			if (isset($_SESSION['registrazioniTrovate'])) {
				
				$this->preparaPagina($generaMastrinoContoTemplate);
				
				include($testata);
				$generaMastrinoContoTemplate->displayPagina();
				
				$_SESSION["messaggio"] = "Mastrino del Conto generato!";
				
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				echo $utility->tailTemplate($template);
				
				include($piede);				
			}
			else {

				$_SESSION["messaggioGeneraMastrino"] = "Nessuna registrazione trovata!";
				
				$ricercaConto = RicercaConto::getInstance();
				$ricercaConto->go();
			}
		}
		else {
	
			$this->preparaPagina($generaMastrinoContoTemplate);
	
			include(self::$testata);
			$generaMastrinoContoTemplate->displayPagina();
	
			$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		$filtro = "";
	
		if (($_SESSION['datareg_da'] != "") & ($_SESSION['datareg_a'] != "")) {
			$filtro = "AND registrazione.dat_registrazione between '" . $_SESSION['datareg_da'] . "' and '" . $_SESSION['datareg_a'] . "'" ;
		}

		if ($_SESSION['codneg_sel'] != "") {
			$filtro .= " AND registrazione.cod_negozio = '" . $_SESSION['codneg_sel'] . "'" ;
		}
		
		$replace = array(
				'%cod_conto%' => trim($_SESSION["codconto"]),
				'%cod_sottoconto%' => trim($_SESSION["codsottoconto"]),
				'%filtro_date%' => $filtro
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaRegistrazioniConto;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['registrazioniTrovate'] = $result;
			$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";				
		}
		else {
			unset($_SESSION['registrazioniTrovate']);
			unset($_SESSION['bottoneEstraiPdf']);			
		}
		return $result;
	}
	
	public function preparaPagina($generaMastrinoContoTemplate) {
	
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneGeneraMastrinoConto;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.mastrinoConto%";
	}
	
}

?>