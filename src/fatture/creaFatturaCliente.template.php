<?php

require_once 'fattura.abstract.class.php';

class CreaFatturaClienteTemplate extends FatturaAbstract {

	public static $_instance = null;
	
	private static $pagina = "/fatture/creaFatturaCliente.form.html";

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

			self::$_instance = new CreaFatturaClienteTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {
		
	}

	public function displayPagina() {
	
		require_once 'utility.class.php';
	
		// Template --------------------------------------------------------------

		$thead_dettagli = "<tr></tr>";
		$tbody_dettagli = "<tr></tr>";
		
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = self::$root . $array['template'] . self::$pagina;

		/**
		 * Prepara la tabella dei dettagli inseriti
		 */
			
		if ($_SESSION['dettagliInseriti'] != "") {
		
			$thead_dettagli =
			"<tr>" .
			"<th class='dt-center'>Quantit&agrave;</th>" .
			"<th>Articolo</th>" .
			"<th class='dt-right'>Importo</th>" .
			"<th class='dt-right'>Totale</th>" .
			"<th class='dt-right'>Imponibile</th>" .
			"<th class='dt-right'>Iva</th>" .
			"<th class='dt-right'>% Aliq</th>" .
			"<th>&nbsp;</th>" .
			"</tr>";	
		
			$tbody_dettagli = "";
			$d_x_array = "";
		
			$d = explode(",", $_SESSION['dettagliInseriti']);
				
			foreach($d as $ele) {
		
				$e = explode("#",$ele);
				$id = $e[0];
		
				$dettaglio =
				"<tr id='" . trim($id) . "'>" .
				"<td class='dt-center'>" . $e[1] . "</td>" .
				"<td>" . $e[2] . "</td>" .
				"<td class='dt-right'>" . number_format($e[3], 2, ',', '.') . "</td>" .
				"<td class='dt-right'>" . number_format($e[4], 2, ',', '.') . "</td>" .
				"<td class='dt-right'>" . number_format($e[5], 2, ',', '.') . "</td>" .
				"<td class='dt-right'>" . number_format($e[6], 2, ',', '.') . "</td>" .
				"<td class='dt-right'>" . number_format($e[7]) . "</td>" .
				"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioFattura(" . trim($id) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
				"</tr>";
		
				$tbody_dettagli = $tbody_dettagli . $dettaglio;
		
				/**
				 * Prepara la valorizzazione dell'array di pagina per i dettagli inseriti
				 */
				$d_x_array = $d_x_array . "'" . $ele . "',";
			}
		}
				
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%numfat%' => $_SESSION["numfat"],
				'%datafat%' => $_SESSION["datafat"],
				'%tipoadd%' => $_SESSION["tipoadd"],
				'%ragsocbanca%' => str_replace("'", "&apos;", $_SESSION["ragsocbanca"]),
				'%ibanbanca%' => $_SESSION["ibanbanca"],
				'%contributo-checked%' => ($_SESSION["tipofat"] == "CONTRIBUTO") ? "checked" : "",
				'%vendita-checked%' => ($_SESSION["tipofat"] == "VENDITA") ? "checked" : "",
				'%assistito%' => $_SESSION["cognomenomeassistito"],
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%descli%' => $_SESSION["descli"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%thead_dettagli%' => $thead_dettagli,
				'%tbody_dettagli%' => $tbody_dettagli,
				'%dettagliInseriti%' => $_SESSION["dettagliInseriti"],
				'%arrayDettagliInseriti%' => $d_x_array,
				'%arrayIndexDettagliInseriti%' => $_SESSION["indexDettagliInseriti"],
				'%elenco_fornitori%' => $_SESSION["elenco_fornitori"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"],
				'%titolo%' => $_SESSION["titolo"]
				
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
		
?>	