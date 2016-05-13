<?php

require_once 'saldi.abstract.class.php';

class RicercaSaldiTemplate extends SaldiAbstract {

	private static $_instance = null;

	private static $pagina = "/saldi/ricercaSaldi.form.html";

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

			self::$_instance = new RicercaSaldiTemplate();

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

		if ($_SESSION["codneg_sel"] == "") {
			$msg = $msg . "<br>&ndash; Seleziona un negozio";
			$esito = FALSE;
		}
		
		if ($_SESSION["datarip_saldo"] == "") {
			$msg = $msg . "<br>&ndash; Seleziona una data di riporto saldo";
			$esito = FALSE;
		}
		
		/**
		 * Fine controlli
		 */				
		
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
		
		if (isset($_SESSION["saldiTrovati"])) {
			
			$risultato_ricerca = 
			"<table id='saldi' class='display'>" .
			"	<thead>" .
			"		<tr>" .
			"			<th>%ml.negozio%</th>" .
			"			<th>%ml.conto%</th>" .
			"			<th>%ml.codsottoconto%</th>" .
			"			<th>%ml.importo%</th>" .
			"			<th>%ml.dare%&ndash;%ml.avere%</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";
			
			$saldiTrovati = $_SESSION["saldiTrovati"];
			$numReg = 0;			
						
			foreach(pg_fetch_all($saldiTrovati) as $row) {

				$impSaldo = "&euro;" . number_format(round($row['imp_saldo'],2), 2, ',', '.');
				
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td class='dt-center'>" . trim($row['cod_negozio']) . "</td>" .
				"	<td class='dt-center'>" . trim($row['cod_conto']) . ' - ' . trim($row['cod_sottoconto']) . "</td>" .
				"	<td>" . trim($row['des_conto']) . ' - ' . trim($row['des_sottoconto']) . "</td>" .
				"	<td class='dt-right'>" . $impSaldo . "</td>" .
				"	<td class='dt-center'>" . trim($row['ind_dareavere']) . "</td>" .
				"</tr>";						
					
			}
			$risultato_ricerca = $risultato_ricerca . "</tbody></table>";			
		}
			
		$replace = array(
				'%titoloPagina%' => $_SESSION["titoloPagina"],
				'%azione%' => $_SESSION["azione"],
				'%confermaTip%' => $_SESSION["confermaTip"],				
				'%datarip_saldo%' => $_SESSION["datarip_saldo"],
				'%villa-selected%' => ($_SESSION["codneg_sel"] == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($_SESSION["codneg_sel"] == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($_SESSION["codneg_sel"] == "TRE") ? "selected" : "",
				'%elenco_dateRiportoSaldo%' => $_SESSION['elenco_date_riporto_saldo'],
				'%risultato_ricerca%' => $risultato_ricerca
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}	

?>