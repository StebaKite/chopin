<?php

require_once 'primanota.abstract.class.php';

class creaRegistrazioneTemplate extends primanotaAbstract {
	
	private static $pagina = "/primanota/creaRegistrazione.form.html";
	
	//-----------------------------------------------------------------------------

	function __construct() {
		self::$root = $_SERVER['DOCUMENT_ROOT'];
	}

	// Setters & Getters  ----------------------------------------------------------


	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {
		
		$esito = TRUE;
		$msg = "<br>";
		
		if ($_SESSION["descreg"] == "") {			
			$msg = $msg . "<br>&ndash; Manca la descrizione"; 			
			$esito = FALSE;
		}
		
		if ($_SESSION["causale"] == "") {
			$msg = $msg . "<br>&ndash; Manca la causale";
			$esito = FALSE;
		}		
		
		if ($_SESSION["dettInseriti"] == "") {
			$msg = $msg . "<br>&ndash; Mancano i dettagli della registrazione";
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

		require_once 'database.class.php';
		require_once 'utility.class.php';
		
		// Template --------------------------------------------------------------

		$utility = new utility();
		$array = $utility->getConfig();

 		$form = self::$root . $array['template'] . self::$pagina;
		
 		if ($_SESSION['dettInseriti'] != "") {
 			
 			$class_dettagli = "datiCreateSottile";
 			
 			$thead_dettagli = 
 	 			"<tr>" .
	 			"<th width='350' align='left'>Conto</th>" .
 				"<th width='100' align='right'>Importo</th>" .
 				"<th width='50' align='center'>D/A</th>" .
 				"<th>&nbsp;</th>" .
 				"</tr>";


 			$tbody_dettagli = "";
 			
 			$d = explode(",", $_SESSION['dettInseriti']);
			
			foreach($d as $ele) {
				
				$e = explode("#",$ele);
				
				$dettaglio = 								
					"<tr id='" . $e[0] . "'>" .
					"<td align='left'>" . $e[0] . "</td>" .
					"<td align='right'>" . $e[1] . "</td>" .
					"<td align='center'>" . $e[2] . "</td>" .
					"<td id='icons'><a class='tooltip' onclick='cancellaDettaglio(" . $e[0] . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
					"</tr>";
				
				$tbody_dettagli = $tbody_dettagli . $dettaglio;
			}
 		}
 		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%descreg%' => $_SESSION["descreg"],
				'%datascad%' => $_SESSION["datascad"],
				'%numfatt%' => $_SESSION['numfatt'],
				'%class_dettagli%' => $class_dettagli,	
				'%thead_dettagli%' => $thead_dettagli,	
				'%tbody_dettagli%' => $tbody_dettagli,	
				'%elenco_causali%' => $_SESSION['elenco_causali'],
				'%elenco_fornitori%' => $_SESSION['elenco_fornitori'],
				'%elenco_clienti%' => $_SESSION['elenco_clienti'],
				'%elenco_conti%' => $_SESSION['elenco_conti']
		);

		$utility = new utility();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);		
	}	
}	

?>
