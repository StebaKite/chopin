<?php

require_once 'scadenze.abstract.class.php';

class RicercaScadenzeCliente extends ScadenzeAbstract {

	private static $_instance = null;

	public static $azioneRicercaScadenzeCliente = "../scadenze/ricercaScadenzeClienteFacade.class.php?modo=go";
	public static $queryRicercaScadenzeCliente = "/scadenze/ricercaScadenzeCliente.sql";
	
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

			self::$_instance = new RicercaScadenzeCliente();

		return self::$_instance;
	}

	public function start() {
		
		require_once 'ricercaScadenzeCliente.template.php';
		require_once 'utility.class.php';
		
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$_SESSION["datareg_da"] = date("d/m/Y");
		$_SESSION["datareg_a"] = date("d/m/Y");
		$_SESSION["codneg_sel"] = "VIL";
		
		unset($_SESSION["scadenzeClienteTrovate"]);
		unset($_SESSION['bottoneEstraiPdf']);
		unset($_SESSION['referer_function_name']);
		
		$ricercaScadenzeClienteTemplate = RicercaScadenzeClienteTemplate::getInstance();
		$this->preparaPagina($ricercaScadenzeClienteTemplate);
		
		// compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);

		$ricercaScadenzeClienteTemplate->displayPagina();
		include(self::$piede);
		
	}

	public function go() {
	
		require_once 'ricercaScadenzeCliente.template.php';
		require_once 'utility.class.php';
	
		// Template
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$ricercaScadenzeClienteTemplate = RicercaScadenzeClienteTemplate::getInstance();
	
		if ($ricercaScadenzeClienteTemplate->controlliLogici()) {
				
			if ($this->ricercaDati($utility)) {
					
				$this->preparaPagina($ricercaScadenzeClienteTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$ricercaScadenzeClienteTemplate->displayPagina();
	
				/**
				 * Gestione del messaggio proveniente dalla cancellazione
				*/
				if (isset($_SESSION["messaggioCancellazione"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioCancellazione"] . "<br>" . "Trovate " . $_SESSION['numScadenzeClienteTrovate'] . " scadenze";
					unset($_SESSION["messaggioCancellazione"]);
				}
				elseif (isset($_SESSION["messaggioModifica"])) {
					$_SESSION["messaggio"] = $_SESSION["messaggioModifica"] . "<br>" . "Trovate " . $_SESSION['numScadenzeClienteTrovate'] . " scadenze";
					unset($_SESSION["messaggioModifica"]);
				}				
				else {
					$_SESSION["messaggio"] = "Trovate " . $_SESSION['numScadenzeClienteTrovate'] . " scadenze";
				}
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
	
				if ($_SESSION['numScadenzeClienteTrovate'] > 0) {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				}
	
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
			else {
					
				$this->preparaPagina($ricercaScadenzeClienteTemplate);
					
				$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
				$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
				echo $utility->tailTemplate($template);

				$ricercaScadenzeClienteTemplate->displayPagina();
	
				$_SESSION["messaggio"] = "Errore fatale durante la lettura delle scadenze cliente" ;
	
				self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
					
				include(self::$piede);
			}
		}
		else {
	
			$this->preparaPagina($ricercaScadenzeClienteTemplate);
	
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"]) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION )));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);

			$ricercaScadenzeClienteTemplate->displayPagina();
	
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
			$filtro = "AND scadenza_cliente.dat_registrazione between '" . $_SESSION['datareg_da'] . "' and '" . $_SESSION['datareg_a'] . "'" ;
		}
		
		if ($_SESSION['codneg_sel'] != "") {
			$filtro .= " AND scadenza_cliente.cod_negozio = '" . $_SESSION['codneg_sel'] . "'" ;
		}

		if ($_SESSION['statoscad_sel'] != "") {
			$filtro .= " AND scadenza_cliente.sta_scadenza = '" . $_SESSION['statoscad_sel'] . "'" ;
		}
		
		$replace = array(
				'%filtro_date%' => $filtro
		);
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaScadenzeCliente;
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['scadenzeClienteTrovate'] = $result;
			$_SESSION['bottoneEstraiPdf'] = "<button id='pdf' class='button' title='%ml.estraipdfTip%'>%ml.pdf%</button>";
		}
		else {
			unset($_SESSION['scadenzeClienteTrovate']);
			$_SESSION['numScadenzeClienteTrovate'] = 0;
			unset($_SESSION['bottoneEstraiPdf']);			
		}
		return $result;
	}

	public function preparaPagina() {
	
		require_once 'utility.class.php';
	
		$_SESSION["azione"] = self::$azioneRicercaScadenzeCliente;
		$_SESSION["confermaTip"] = "%ml.cercaTip%";
		$_SESSION["titoloPagina"] = "%ml.ricercaScadenzeCliente%";
	}
}
		
?>