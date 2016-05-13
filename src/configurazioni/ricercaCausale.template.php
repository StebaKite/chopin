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
			"<table id='causali' class='display'>" .
			"	<thead>" .
			"		<th>%ml.codcausale%</th>" .
			"		<th>%ml.descausale%</th>" .
			"		<th>%ml.catcausale%</th>" .
			"		<th>%ml.qtareg%</th>" .
			"		<th>%ml.qtaconti%</th>" .
			"		<th></th>" .
			"		<th></th>" .
			"		<th></th>" .
			"	</thead>" .
			"	<tbody>";
	
			$causaliTrovate = $_SESSION["causaliTrovate"];
			$numCausali = 0;
	
			foreach(pg_fetch_all($causaliTrovate) as $row) {
				
				if ($row['tot_conti_causale'] == 0) {
					$class = "class='errato'";
				}
				else {
					$class = "class=''";
				}				
				
				if ($row['tot_registrazioni_causale'] == 0) {
					$bottoneModifica = "<a class='tooltip' href='../configurazioni/modificaCausaleFacade.class.php?modo=start&codcausale=" . trim($row['cod_causale']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneConfigura = "<a class='tooltip' href='../configurazioni/configuraCausaleFacade.class.php?modo=start&codcausale=" . trim($row['cod_causale']) . "&descausale=" . trim($row['des_causale']) . "'><li class='ui-state-default ui-corner-all' title='%ml.configura%'><span class='ui-icon ui-icon-wrench'></span></li></a>";
					$bottoneCancella = "<a class='tooltip' onclick='cancellaCausale(" . trim($row['cod_causale']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
				}
				else {
					$bottoneModifica = "<a class='tooltip' href='../configurazioni/modificaCausaleFacade.class.php?modo=start&codcausale=" . trim($row['cod_causale']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneConfigura = "<a class='tooltip' href='../configurazioni/configuraCausaleFacade.class.php?modo=start&codcausale=" . trim($row['cod_causale']) . "&descausale=" . trim($row['des_causale']) . "'><li class='ui-state-default ui-corner-all' title='%ml.configura%'><span class='ui-icon ui-icon-wrench'></span></li></a>";
					$bottoneCancella = "&nbsp;";
				}

				$numCausali ++;
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td>" . trim($row['cod_causale']) . "</td>" .
				"	<td>" . trim($row['des_causale']) . "</td>" .
				"	<td>" . trim($row['cat_causale']) . "</td>" .
				"	<td>" . trim($row['tot_registrazioni_causale']) . "</td>" .
				"	<td>" . trim($row['tot_conti_causale']) . "</td>" .
				"	<td id='icons'>" . $bottoneModifica . "</td>" .
				"	<td id='icons'>" . $bottoneConfigura . "</td>" .
				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";		
			}
			$_SESSION['numCausaliTrovate'] = $numCausali;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table>";
		}
		else {
	
		}
	
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%causale%' => $_SESSION["causale"],
				'%confermaTip%' => $_SESSION["confermaTip"],
				'%risultato_ricerca%' => $risultato_ricerca
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>