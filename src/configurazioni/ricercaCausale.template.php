<?php

require_once 'configurazioni.abstract.class.php';

class RicercaCausaleTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/ricercaCausale.form.html";

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

			self::$_instance = new RicercaCausaleTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {
	
		require_once 'utility.class.php';
	
		// Template --------------------------------------------------------------
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = self::$root . $array['template'] . self::$pagina;
		$risultato_ricerca = "";
	
		if (isset($_SESSION["causaliTrovate"])) {
	
			$risultato_ricerca =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='70'>%ml.codcausale%</th>" .
			"		<th width='400'>%ml.descausale%</th>" .
			"		<th width='50'>%ml.qtareg%</th>" .
			"		<th width='50'>%ml.qtaconti%</th>" .
			"		<th width='83' colspan='3'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-causali'>" .
			"	<table class='expandible'>" .
			"		<tbody>";
	
			$causaliTrovate = $_SESSION["causaliTrovate"];
			$numCausali = 0;
	
			foreach(pg_fetch_all($causaliTrovate) as $row) {
	
				if ($row['tot_registrazioni_causale'] == 0) {
					$class = "class=''";
					$bottoneModifica = "<a class='tooltip' href='../configurazioni/modificaCausaleFacade.class.php?modo=start&codconto=" . trim($row['cod_conto']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneConfigura = "<a class='tooltip' href='../configurazioni/configuraCausaleFacade.class.php?modo=start&codconto=" . trim($row['cod_conto']) . "'><li class='ui-state-default ui-corner-all' title='%ml.configura%'><span class='ui-icon ui-icon-gear'></span></li></a>";
					$bottoneCancella = "<a class='tooltip' onclick='cancellaConto(" . trim($row['cod_conto']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
				}
				else {
					$class = "class=''";
					$bottoneModifica = "<a class='tooltip' href='../configurazioni/modificaCausaleFacade.class.php?modo=start&codconto=" . trim($row['cod_conto']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneConfigura = "<a class='tooltip' href='../configurazioni/configuraCausaleFacade.class.php?modo=start&codconto=" . trim($row['cod_conto']) . "'><li class='ui-state-default ui-corner-all' title='%ml.configura%'><span class='ui-icon ui-icon-gear'></span></li></a>";
					$bottoneCancella = "&nbsp;";
				}

				$numCausali ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr " . $class . " id='" . trim($row['cod_causale']) . "'>" .
				"	<td width='80' class='tooltip' align='center'>" . trim($row['cod_causale']) . "</td>" .
				"	<td width='410' align='left'>" . trim($row['des_causale']) . "</td>" .
				"	<td width='55'  align='right'>" . trim($row['tot_registrazioni_causale']) . "</td>" .
				"	<td width='60'  align='right'>" . trim($row['tot_conti_causale']) . "</td>" .
				"	<td width='30' id='icons'>" . $bottoneModifica . "</td>" .
				"	<td width='30' id='icons'>" . $bottoneConfigura . "</td>" .
				"	<td width='30' id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";		
			}
			$_SESSION['numCausaliTrovate'] = $numCausali;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";
		}
		else {
	
		}
	
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%codcausale%' => $_SESSION["codcausale"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%risultato_ricerca%' => $risultato_ricerca
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>