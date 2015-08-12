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

		/**
		 * Controllo formale dei campi facoltativi
		 */
		
		if ($_SESSION["numfatt"] != "") {
			
			if(ereg('[^0-9A-Za-z+_./-]',$_SESSION["numfatt"])) {
				$msg = $msg . "<br>&ndash; Formato numero fattura non valido.<br>&ndash; Caratteri ammessi 0-9 A-Z a-z + _ . / -";
				$esito = FALSE;				
			}
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
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='30'>%ml.idReg%</th>" .
			"		<th width='72'>%ml.datReg%</th>" .
			"		<th width='100'>%ml.numfatt%</th>" .
			"		<th width='400'>%ml.desReg%</th>" .
			"		<th width='270'>%ml.codcau%</th>" .
			"		<th width='100' colspan='3'>%ml.azioni%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll'>" .
			"	<table class='expandible'>" .
			"		<tbody>";
			
			$registrazioniTrovate = $_SESSION["registrazioniTrovate"];
			$numReg = 0;			
						
			foreach(pg_fetch_all($registrazioniTrovate) as $row) {

				if (trim($row['tipo']) == 'R') {					

					switch ($row['sta_registrazione']) {
						case ("00"): {
							$class = "class='parentAperto'";
							$bottoneModifica = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
							$bottoneCancella = "<a class='tooltip' onclick='cancellaRegistrazione(" . trim($row['id_registrazione']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
							break;
						}
						case ("02"): {
							$class = "class='parentErrato'";
							$bottoneModifica = "<a class='tooltip' href='../primanota/modificaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
							$bottoneCancella = "<a class='tooltip' onclick='cancellaRegistrazione(" . trim($row['id_registrazione']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
							break;
						}
						default: {
							$class = "class='parent'";
							$bottoneModifica = "&nbsp;";
							$bottoneCancella = "&nbsp;";								
							break;
						}
					}
					
					$numReg ++; 
					$risultato_ricerca = $risultato_ricerca .
					"<tr " . $class . " id='" . trim($row['id_registrazione']) . "'>" .
					"	<td width='40' class='tooltip' align='center'>" . trim($row['id_registrazione']) . "</td>" .
					"	<td width='80'  align='center'>" . trim($row['dat_registrazione']) . "</td>" .
					"	<td width='105'  align='center'>" . trim($row['num_fattura']) . "</td>" .
					"	<td width='410' align='left'>" . trim($row['des_registrazione']) . "</td>" .
					"	<td width='290'  align='left'>" . trim($row['cod_causale']) . " - " . trim($row['des_causale']) . "</td>" .
					"	<td width='30' id='icons'><a class='tooltip' href='../primanota/visualizzaRegistrazioneFacade.class.php?modo=start&idRegistrazione=" . trim($row['id_registrazione']) . "'><li class='ui-state-default ui-corner-all' title='%ml.visualizza%'><span class='ui-icon ui-icon-search'></span></li></a></td>" .
					"	<td width='30' id='icons'>" . $bottoneModifica . "</td>" .
					"	<td width='30' id='icons'>" . $bottoneCancella . "</td>" .
					"</tr>";						
					
				}
				elseif (trim($row['tipo']) == 'D') {

					$class = "class='child-" . trim($row['id_registrazione']) . "'";
					$id = "id='child'";
						
					$risultato_ricerca = $risultato_ricerca .
					"<tr " . $class . " " . $id . " >" .
					"	<td class='tooltip' align='right'>" . trim($row['ind_dareavere']) . "</td>" .						
					"	<td class='tooltip' align='right'> &euro; " . trim($row['imp_registrazione']) .  "</td>" .
					"	<td align='right'><i>" . trim($row['cod_conto']) . trim($row['cod_sottoconto']) . "</i></td>" .
					"	<td colspan='5' align='left'><i>" . trim($row['des_sottoconto']) . "</i></td>" .					
					"</tr>";
						
					$id = "id='child'";
					$class = "class='child-" . trim($row['id_registrazione']) . "'";
					$id_registrazione = "";
					$id_dettaglio_registrazione = trim($row['id_dettaglio_registrazione']);	
				}				
				
			}
			$_SESSION['numRegTrovate'] = $numReg;
			$risultato_ricerca = $risultato_ricerca . "</tbody></table></div>";			
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
				'%risultato_ricerca%' => $risultato_ricerca
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}	

?>