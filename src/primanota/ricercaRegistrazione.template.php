<?php

require_once 'primanota.abstract.class.php';

class RicercaRegistrazioneTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/ricercaRegistrazione.form.html";

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

			self::$_instance = new RicercaRegistrazioneTemplate();

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
		
		if (isset($_SESSION["registrazioniTrovate"])) {
			
			$risultato_ricerca = 
			"<table id='registrazioni' class='display' width='100%'>" .
			"	<thead>" .
			"		<tr>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th></th>" .
			"			<th>%ml.datReg%</th>" .
			"			<th class='dt-left'>%ml.numfatt%</th>" .
			"			<th class='dt-left'>%ml.desReg%</th>" .
			"			<th class='dt-left'>%ml.codcau%</th>" .
			"			<th>&nbsp;</th>" .
			"			<th>&nbsp;</th>" .
			"			<th>&nbsp;</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";
			
			$registrazioniTrovate = $_SESSION["registrazioniTrovate"];
			$numReg = 0;			
						
			foreach(pg_fetch_all($registrazioniTrovate) as $row) {

				if (trim($row['tipo']) == 'R') {					

					switch ($row['sta_registrazione']) {
						case ("00"): {
							$class = "class='dt-ok'";
							$bottoneModifica = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
							$bottoneCancella = "<a class='tooltip' onclick='cancellaRegistrazione(" . trim($row['id_registrazione']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
							break;
						}
						case ("02"): {
							$class = "class='dt-ko'";
							$bottoneModifica = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
							$bottoneCancella = "<a class='tooltip' onclick='cancellaRegistrazione(" . trim($row['id_registrazione']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
							break;
						}
						default: {
							$class = "class='dt-chiuso'";
							$bottoneModifica = "&nbsp;";
							$bottoneCancella = "&nbsp;";								
							break;
						}
					}
					
					$numReg ++; 
					$risultato_ricerca = $risultato_ricerca .
					"<tr " . $class . ">" .
					"	<td>" . trim($row['id_registrazione']) . "</td>" .
					"	<td>" . trim($row['dat_registrazione_yyyymmdd']) . "</td>" .
					"	<td>" . trim($row['id_dettaglio_registrazione']) . "</td>" .					
					"	<td>" . trim($row['dat_registrazione']) . "</td>" .
					"	<td class='td-left'>" . trim($row['num_fattura']) . "</td>" .
					"	<td>" . trim($row['des_registrazione']) . "</td>" .
					"	<td>" . trim($row['cod_causale']) . " - " . trim($row['des_causale']) . "</td>" .
					"	<td id='icons'><a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizza%'><span class='ui-icon ui-icon-search'></span></li></a></td>" .
					"	<td id='icons'>" . $bottoneModifica . "</td>" .
					"	<td id='icons'>" . $bottoneCancella . "</td>" .
					"</tr>";						
					
				}
				elseif (trim($row['tipo']) == 'D') {
						
					$risultato_ricerca = $risultato_ricerca .
					"<tr>" .
					"	<td>" . trim($row['id_registrazione']) . "</td>" .
					"	<td>" . trim($row['dat_registrazione_yyyymmdd']) . "</td>" .
					"	<td>" . trim($row['id_dettaglio_registrazione']) . "</td>" .
					"	<td class='dt-right'>" . trim($row['ind_dareavere']) . "</td>" .						
					"	<td class='dt-right'>" . trim($row['imp_registrazione']) .  "</td>" .
					"	<td><i>" . trim($row['cod_conto']) . trim($row['cod_sottoconto']) . " - " . trim($row['des_sottoconto']) . "</i></td>" .
					"	<td></td>" .					
					"	<td></td>" .					
					"	<td></td>" .					
					"	<td></td>" .					
					"</tr>";
						
				}				
				
			}
			$_SESSION['numRegTrovate'] = $numReg;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table>";			
		}
		else {
			
		}
			
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],				
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%numfatt%' => $_SESSION["numfatt"],
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%risultato_ricerca%' => $risultato_ricerca
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}	

?>