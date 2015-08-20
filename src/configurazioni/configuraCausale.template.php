<?php

require_once 'configurazioni.abstract.class.php';

class ConfiguraCausaleTemplate extends ConfigurazioniAbstract {

	private static $_instance = null;

	private static $pagina = "/configurazioni/configuraCausale.form.html";

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

			self::$_instance = new ConfiguraCausaleTemplate();

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
		$class = "class=''";		
	
		$form = self::$root . $array['template'] . self::$pagina;

		// Prepara la tabella dei conti configurati sulla causale
		
		if (isset($_SESSION["contiCausale"])) {
			
			$elencoContiCausale =
			"<div class='scroll-config-causali'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			foreach (pg_fetch_all($_SESSION["contiCausale"]) as $row) {

				$bottoneEscludi = "<a class='tooltip' href='escludiContoCausaleFacade.class.php?modo=start&codconto=" . $row['cod_conto'] . "'><li class='ui-state-default ui-corner-all' title='%ml.escludiContoTip%'><span class='ui-icon ui-icon-minus'></span></li></a>";
				
				$elencoContiCausale .= "<tr " . $class . ">";
				$elencoContiCausale .= "<td align='center' width='68'>" . $row['cod_conto'] . "</td>";
				$elencoContiCausale .= "<td align='left' width='360'>" . $row['des_conto'] . "</td>";
				$elencoContiCausale .= "<td width='20' id='icons'>" . $bottoneEscludi . "</td>";
				$elencoContiCausale .= "</tr>";
			}								
			$elencoContiCausale = $elencoContiCausale . "</tbody></table></div>";
		}
		else {
			$elencoContiCausale = "<p>La causale non ha conti configurati</p>";
		}

		// Prepara la tabella dei conti disponibili non ancora configurati sulla causale

		if (isset($_SESSION["contiDisponibili"])) {
				
			$elencoContiDisponibili =
			"<div class='scroll-config-causali'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			foreach (pg_fetch_all($_SESSION["contiDisponibili"]) as $row) {
		
				$bottoneIncludi = "<a class='tooltip' href='includiContoCausaleFacade.class.php?modo=start&codconto=" . $row['cod_conto'] . "'><li class='ui-state-default ui-corner-all' title='%ml.includiContoTip%'><span class='ui-icon ui-icon-plus'></span></li></a>";
		
				$elencoContiDisponibili .= "<tr " . $class . ">";
				$elencoContiDisponibili .= "<td width='25' id='icons'>" . $bottoneIncludi . "</td>";
				$elencoContiDisponibili .= "<td align='center' width='68'>" . $row['cod_conto'] . "</td>";
				$elencoContiDisponibili .= "<td align='left' width='356'>" . $row['des_conto'] . "</td>";
				$elencoContiDisponibili .= "</tr>";
			}
			$elencoContiDisponibili = $elencoContiDisponibili . "</tbody></table></div>";
		}
		else {
			$elencoContiDisponibili = "<p>Non ci sono conti disponibili da includere nella causale</p>";
		}
		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%codcausale%' => $_SESSION["codcausale"],
				'%descausale%' => $_SESSION["descausale"],
				'%elencoconticausale%' => $elencoContiCausale,
				'%elencocontidisponibili%' => $elencoContiDisponibili,
				
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>