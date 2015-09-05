<?php

require_once 'scadenze.abstract.class.php';

class RicercaScadenzeTemplate extends ScadenzeAbstract {

	private static $_instance = null;

	private static $pagina = "/scadenze/ricercaScadenze.form.html";

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

			self::$_instance = new RicercaScadenzeTemplate();

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
		
		if ($_SESSION["datascad_da"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di inizio ricerca";
			$esito = FALSE;
		}
		
		if ($_SESSION["datascad_a"] == "") {
			$msg = $msg . "<br>&ndash; Manca la data di fine ricerca";
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
		
		if (isset($_SESSION["scadenzeTrovate"])) {
		
			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='100'>%ml.datscadenza%</th>" .
			"		<th width='250'>%ml.notascadenza%</th>" .
			"		<th width='100'>%ml.tipaddebito%</th>" .
			"		<th width='100'>%ml.impscadenza%</th>" .
			"		<th width='25'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-scadenze'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$scadenzeTrovate = $_SESSION["scadenzeTrovate"];
			$numScadenze = 0;
		
			foreach(pg_fetch_all($scadenzeTrovate) as $row) {
		
				$class = "class=''";
				$bottoneVisualizza = "<a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizza%'><span class='ui-icon ui-icon-search'></span></li></a>";
		
				if (trim($row['nota_scadenza']) != "") {$notascadenza = trim($row['nota_scadenza']);}
				else {$notascadenza = "&ndash;&ndash;&ndash;";} 

				if (trim($row['tip_addebito']) != "") {$tipaddebito = trim($row['tip_addebito']);}
				else {$tipaddebito = "&ndash;&ndash;&ndash;";}
				
				$numScadenze ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr " . $class . " id='" . trim($row['id_scadenza']) . "'>" .
				"	<td width='108' class='tooltip' align='center'>" . trim($row['dat_scadenza']) . "</td>" .
				"	<td width='258' align='left'>" . $notascadenza . "</td>" .
				"	<td width='108' align='center'>" . $tipaddebito . "</td>" .
				"	<td width='108' align='right'>&euro;" . number_format(trim($row['imp_in_scadenza']), 2, ',', '.') . "</td>" .
				"	<td width='45' id='icons'>" . $bottoneVisualizza . "</td>" .
				"</tr>";
			}
			$_SESSION['numScadenzeTrovate'] = $numScadenze;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
		}
		else {
		
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%datascad_da%' => $_SESSION["datascad_da"],
				'%datascad_a%' => $_SESSION["datascad_a"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%risultato_ricerca%' => $risultato_ricerca
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
		}
	}

?>