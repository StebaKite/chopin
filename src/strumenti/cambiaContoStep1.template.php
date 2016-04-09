<?php

require_once 'strumenti.abstract.class.php';

class CambiaContoStep1Template extends StrumentiAbstract {

	private static $_instance = null;

	private static $pagina = "/strumenti/cambiaContoStep1.form.html";

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

			self::$_instance = new CambiaContoStep1Template();

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
		
		if ($_SESSION["datareg_da"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di inizio ricerca";
			$esito = FALSE;
		}

		if ($_SESSION["datareg_a"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di fine ricerca";
			$esito = FALSE;
		}

		if ($_SESSION["conto_sel"] == "") {
			$msg = $msg . "<br>&ndash; Manca il conto da spostare";
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
		$risultato_ricerca = "";
		$bottone_avanti = "";
		
		if (isset($_SESSION["registrazioniTrovate"])) {
			
			$risultato_ricerca = 
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='72'>%ml.datReg%</th>" .
			"		<th width='350'>%ml.desReg%</th>" .
			"		<th width='50'>%ml.staReg%</th>" .
			"		<th width='100'>%ml.impReg%</th>" .
			"		<th width='50'>%ml.indDareAvere%</th>" .
			"		<th width='100'>%ml.conto%</th>" .
			"		<th width='100'>%ml.sottoconto%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll'>" .
			"	<table class='result'>" .
			"		<tbody>";
			
			$registrazioniTrovate = $_SESSION["registrazioniTrovate"];
			$numReg = 0;			
						
			foreach(pg_fetch_all($registrazioniTrovate) as $row) {

				$numReg ++; 
				$risultato_ricerca .= 
				"<tr " . $class . ">" .
				"	<td width='80'  align='center'>" . trim($row['dat_registrazione']) . "</td>" .
				"	<td width='358' align='left'>" . trim($row['des_registrazione']) . "</td>" .
				"	<td width='58'  align='center'>" . trim($row['sta_registrazione']) . "</td>" .
				"	<td width='108'  align='center'>" . trim($row['imp_registrazione']) . "</td>" .
				"	<td width='58'  align='center'>" . trim($row['ind_dareavere']) . "</td>" .
				"	<td width='108'  align='center'>" . trim($row['cod_conto']) . "</td>" .
				"	<td width='108'  align='center'>" . trim($row['cod_sottoconto']) . "</td>" .
				"</tr>";						
			}
			$_SESSION['numRegTrovate'] = $numReg;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
			
			$bottone_avanti = "<button class='button' title='%ml.avantiTip%' >%ml.avanti%</button>";
		}
		else {
			
		}
			
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],				
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%risultato_ricerca%' => $risultato_ricerca,
				'%bottoneAvanti%' => $bottone_avanti
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}	

?>