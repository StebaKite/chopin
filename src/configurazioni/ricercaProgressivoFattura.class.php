<?php

require_once 'configurazioni.abstract.class.php';

class RicercaProgressivoFattura extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneRicercaProgressivoFattura = "../configurazioni/ricercaProgressivoFatturaFacade.class.php?modo=go";
	public static $queryRicercaProgressivoFattura = "/configurazioni/ricercaProgressivoFattura.sql";

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

			self::$_instance = new RicercaProgressivoFattura();

		return self::$_instance;
	}

	public function start() {

		require_once 'ricercaProgressivoFattura.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		unset($_SESSION["progressiviTrovati"]);
		$_SESSION["catcliente"] = "";
		
		$ricercaProgressivoFatturaTemplate = RicercaProgressivoFatturaTemplate::getInstance();
		$this->preparaPagina($ricercaProgressivoFatturaTemplate);
		
		// compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$ricercaProgressivoFatturaTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {
	
		require_once 'ricercaProgressivoFattura.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		unset($_SESSION["clientiTrovati"]);
	
		$ricercaProgressivoFatturaTemplate = RicercaProgressivoFatturaTemplate::getInstance();
	
		if ($this->ricercaDati($utility)) {
	
			$this->preparaPagina($ricercaProgressivoFatturaTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
				
			$ricercaProgressivoFatturaTemplate->displayPagina();

			$_SESSION["messaggio"] = "Trovati " . $_SESSION['numProgressiviTrovati'] . " progressivi fattura";

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				
			if ($_SESSION['numProgressiviTrovati'] > 0) {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			}
			else {
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			}
				
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
		else {
	
			$this->preparaPagina($ricercaProgressivoFatturaTemplate);

			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
				
			$$ricercaProgressivoFatturaTemplate->displayPagina();
	
			$_SESSION["messaggio"] = "Errore fatale durante la lettura delle categorie clienti" ;
	
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
	
			include(self::$piede);
		}
	}
	
	public function ricercaDati($utility) {
	
		require_once 'database.class.php';
	
		$filtro = "";

		if ($_SESSION['catcliente'] != "") {
			$filtro .= "AND progressivo_fattura.cat_cliente = '" . $_SESSION['catcliente'] . "'";
		}
		
		$replace = array(
				'%filtri_progressivi_fattura%' => $filtro
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaProgressivoFattura;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['progressiviTrovati'] = $result;
		}
		else {
			unset($_SESSION['progressiviTrovati']);
			$_SESSION['numProgressiviTrovati'] = 0;
		}
		return $result;
	}
	
	public function preparaPagina($ricercaProgressivoFatturaTemplate) {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaProgressivoFattura;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaProgressivoFattura%";

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle categorie -------------------------------------------------------------
		
		$_SESSION['elenco_categorie_cliente'] = $this->caricaCategorieCliente($utility, $db);		
	}
}

?>