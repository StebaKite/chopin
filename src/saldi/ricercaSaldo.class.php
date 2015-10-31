<?php

require_once 'saldo.abstract.class.php';

class RicercaCorrispettivo extends PrimanotaAbstract {

	private static $_instance = null;

	public static $azioneRicercaCorrispettivo = "../primanota/ricercaCorrispettivoFacade.class.php?modo=go";
	public static $queryRicercaCorrispettivo = "/primanota/ricercaCorrispettivo.sql";

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

			self::$_instance = new RicercaCorrispettivo();

		return self::$_instance;
	}

	public function start() {

		require_once 'ricercaCorrispettivo.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");
		$_SESSION["numfatt"] = "";
		$_SESSION["codneg_sel"] = "VIL";
		unset($_SESSION["registrazioniTrovate"]);

		$ricercaCorrispettivoTemplate = RicercaCorrispettivoTemplate::getInstance();
		$this->preparaPagina($ricercaCorrispettivoTemplate);

		// compone la pagina
		include($testata);
		$ricercaCorrispettivoTemplate->displayPagina();
		include($piede);
	}

	public function go() {

		require_once 'ricercaCorrispettivo.template.php';
		require_once 'utility.class.php';

		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$testata = self::$root . $array['testataPagina'];
		$piede = self::$root . $array['piedePagina'];

		$ricercaCorrispettivoTemplate = RicercaCorrispettivoTemplate::getInstance();

		if ($ricercaCorrispettivoTemplate->controlliLogici()) {
				
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($ricercaCorrispettivoTemplate);
					
				include(self::$testata);
				$ricercaCorrispettivoTemplate->displayPagina();

				/**
				 * Gestione del messaggio proveniente dalla cancellazione
				*/
				if (isset($_SESSION["messaggioCancellazione"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovate " . $_SESSION['numRegTrovate'] . " registrazioni";
					unset($_SESSION["messaggioCancellazione"]);
				}
				else {
					$_SESSION["messaggio"] = "Trovate " . $_SESSION['numRegTrovate'] . " registrazioni";
				}

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);

				if ($_SESSION['numRegTrovate'] > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}

				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($ricercaCorrispettivoTemplate);
					
				include(self::$testata);
				$ricercaCorrispettivoTemplate->displayPagina();

				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle registrazioni" ;

				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {

			$this->preparaPagina($ricercaCorrispettivoTemplate);

			include(self::$testata);
			$ricercaCorrispettivoTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);

			include(self::$piede);
		}
	}

	public function ricercaDati($utility) {

		require_once 'database.class.php';

		$filtriCorrispettivo = "";
		$filtriDettaglio = "";
		
		if ($_SESSION["cod_causale"] != "") {
			$filtriCorrispettivo .= "and reg.cod_causale = '" . $_SESSION["cod_causale"] . "'";
		}
		if ($_SESSION["numfatt"] != "") {
			$filtriCorrispettivo .= "and reg.num_fattura like '" . $_SESSION["numfatt"] . "%'";
		}
		if ($_SESSION["codneg_sel"] != "") {
			$filtriCorrispettivo .= "and reg.cod_negozio = '" . $_SESSION["codneg_sel"] . "'";
		}

		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%filtri-corrispettivo%' => $filtriCorrispettivo,
				'%filtri-dettaglio%' => $filtriDettaglio,
		);

		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaCorrispettivo;

		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

		// esegue la query

		$db = Database::getInstance();
		$result = $db->getData($sql);

		if (pg_num_rows($result) > 0) {
			$_SESSION['registrazioniTrovate'] = $result;
		}
		else {
			unset($_SESSION['registrazioniTrovate']);
			$_SESSION['numRegTrovate'] = 0;
		}

		return $result;
	}

	public function preparaPagina($ricercaCorrispettivoTemplate) {

		require_once 'utility.class.php';

		$_SESSION["azione"] = self::$azioneRicercaCorrispettivo;
		$_SESSION["confermaTip"] = "%ml.confermaRicercaCorrispettivo%";
		$_SESSION["titoloPagina"] = "%ml.ricercaCorrispettivo%";
	}
}
?>