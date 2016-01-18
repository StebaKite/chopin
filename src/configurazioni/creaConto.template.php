<?php

require_once 'configurazioni.abstract.class.php';

class CreaContoTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/creaConto.form.html";

	//-----------------------------------------------------------------------------

	function __construct() {
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new CreaContoTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {

		$esito = TRUE;
		$msg = "<br>";
		
		/**
		 * Controllo presenza dati obbligatori
		 */
		
		if ($_SESSION["codconto"] == "") {			
			$msg = $msg . "<br>&ndash; Manca il codice del conto";
			$esito = FALSE;
		}
		else {
			if (!is_numeric($_SESSION["codconto"])) {
				$msg = $msg . "<br>&ndash; Il codice conto deve essere numerico";
				$esito = FALSE;
			}
			else {
				if ($_SESSION["codconto"] < 100) {
					$msg = $msg . "<br>&ndash; Il codice conto deve essere maggiore di 100";
					$esito = FALSE;						
				}
			}
		}
		
		if ($_SESSION["desconto"] == "") {
			$msg = $msg . "<br>&ndash; Manca la descrizione del conto";
			$esito = FALSE;
		}

		if ($_SESSION["sottocontiInseriti"] == "") {
			$msg = $msg . "<br>&ndash; Mancano i sottoconti";
			$esito = FALSE;
		}

		// ----------------------------------------------
		
		if ($msg != "<br>") {
			$_SESSION["messaggio"] = $msg;
		}
		else {
			unset($_SESSION["messaggio"]);
		}
		
		return $esito;
	}

	public function displayPagina() {
	
		require_once 'utility.class.php';
	
		// Template --------------------------------------------------------------
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = self::$root . $array['template'] . self::$pagina;
	
		/**
		 * Prepara la tabella dei dettagli inseriti
		 */
			
		if ($_SESSION['sottocontiInseriti'] != "") {
	
			$class_dettagli = "datiCreateSottile";
	
			$thead_sottoconti =
			"<tr>" .
			"<th width='100' align='center'>Sottoconto</th>" .
			"<th width='400' align='left'>Descrizione</th>" .
			"<th width='18' >&nbsp;</th>" .
			"</tr>";
	
	
			$tbody_sottoconti = "";
			$d_x_array = "";
	
			$d = explode(",", $_SESSION['sottocontiInseriti']);
				
			foreach($d as $ele) {
	
				$e = explode("#",$ele);
				$codsottoconto = trim($e[0]);
	
				$sottoconto =
				"<tr id='" . $codsottoconto . "'>" .
				"<td width='110' align='center'>" . $e[0] . "</td>" .
				"<td width='409' align='left'>" . $e[1] . "</td>" .
				"<td width='25' id='icons'><a class='tooltip' onclick='cancellaSottocontoPagina(" . $codsottoconto . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
	
				$tbody_sottoconti = $tbody_sottoconti . $sottoconto;
	
				/**
				 * Prepara la valorizzazione dell'array di pagina per i dettagli inseriti
				 */
				$d_x_array = $d_x_array . "'" . $ele . "',";
			}
		}
			
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%codconto%' => $_SESSION["codconto"],
				'%desconto%' => str_replace("'", "&apos;", $_SESSION["desconto"]),
				'%class_dettagli%' => $class_dettagli,
				'%thead_sottoconti%' => $thead_sottoconti,
				'%tbody_sottoconti%' => $tbody_sottoconti,
				'%arraySottocontiInseriti%' => $d_x_array,
				'%arrayIndexSottocontiInseriti%' => $_SESSION["indexSottocontiInseriti"],
				'%sottocontiInseriti%' => $_SESSION['sottocontiInseriti']
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>