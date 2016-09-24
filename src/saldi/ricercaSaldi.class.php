<?php

require_once 'saldi.abstract.class.php';

class RicercaSaldi extends SaldiAbstract {

	private static $_instance = null;

	public static $azioneRicercaSaldi = "../saldi/ricercaSaldiFacade.class.php?modo=go";
	public static $queryRicercaSaldi = "/saldi/ricercaSaldi.sql";

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

			self::$_instance = new RicercaSaldi();

		return self::$_instance;
	}

	public function start() {

		require_once 'ricercaSaldi.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["codneg_sel"] = "VIL";
		unset($_SESSION["saldiTrovati"]);

		$ricercaSaldiTemplate = RicercaSaldiTemplate::getInstance();
		$this->preparaPagina($ricercaSaldiTemplate);

		// compone la pagina
		$replace = array('%amb%' => $_SESSION["ambiente"]);
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$ricercaSaldiTemplate->displayPagina();
		include(self::$piede);
	}

	public function go() {

		require_once 'ricercaSaldi.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$ricercaSaldiTemplate = RicercaSaldiTemplate::getInstance();

		if ($ricercaSaldiTemplate->controlliLogici()) {
				
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($ricercaSaldiTemplate);
					
				$replace = array('%amb%' => $_SESSION["ambiente"]);
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$ricercaSaldiTemplate->displayPagina();

				/**
				 * Gestione del messaggio proveniente dalla cancellazione
				*/
				if (isset($_SESSION["messaggioCancellazione"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovati " . $_SESSION['numSaldiTrovati'] . " saldi";
					unset($_SESSION["messaggioCancellazione"]);
				}
				else {
					$_SESSION["messaggio"] = "Trovati " . $_SESSION['numSaldiTrovati'] . " saldi";
				}

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);

				if ($_SESSION['numSaldiTrovati'] > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}

				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($ricercaSaldiTemplate);
					
				$replace = array('%amb%' => $_SESSION["ambiente"]);
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$ricercaSaldiTemplate->displayPagina();

				$_SESSION["messaggio"] = "Errore fatale durante la lettura dei saldi" ;

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {

			$this->preparaPagina($ricercaSaldiTemplate);

			$replace = array('%amb%' => $_SESSION["ambiente"]);
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$ricercaSaldiTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {

		require_once 'database.class.php';

		$db = Database::getInstance();
		
		return $this->leggiSaldi($db, $utility, $_SESSION["codneg_sel"], $_SESSION["datarip_saldo"]);
	}

	public function preparaPagina($ricercaSaldiTemplate) {

		require_once 'database.class.php';
		require_once 'utility.class.php';

		$_SESSION["azione"] = self::$azioneRicercaSaldi;
		$_SESSION["confermaTip"] = "%ml.confermaRicercaSaldi%";
		$_SESSION["titoloPagina"] = "%ml.ricercaSaldi%";

		$db = Database::getInstance();
		$utility = Utility::getInstance();
		
		// Prelievo delle date riporto saldo -------------------------------------------------------------
		
		$_SESSION['elenco_date_riporto_saldo'] = $this->caricaDateRiportoSaldo($utility, $db);		
	}
}
?>