<?php

require_once 'fattura.abstract.class.php';

class CreaFatturaAziendaConsortileTemplate extends FatturaAbstract {

	public static $_instance = null;
	
	private static $pagina = "/fatture/creaFatturaAziendaConsortile.form.html";

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

			self::$_instance = new CreaFatturaAziendaConsortileTemplate();

		return self::$_instance;
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {
		
	}

	public function displayPagina() {
	
		require_once 'utility.class.php';
	
		// Template --------------------------------------------------------------
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = self::$root . $array['template'] . self::$pagina;

		/**
		 * Prepara la tabella dei dettagli inseriti
		 */
			
		if ($_SESSION['dettagliInseriti'] != "") {
		
			$class_dettagli = "datiCreateSottile";
		
			$thead_dettagli =
			"<tr>" .
			"<th width='50' align='center'>Quantit&agrave;</th>" .
			"<th width='350' align='left'>Articolo</th>" .
			"<th width='50' align='right'>Importo</th>" .
			"<th width='50' align='right'>Totale</th>" .
			"<th width='50' align='right'>Imponibile</th>" .
			"<th width='50' align='right'>Iva</th>" .
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
				"<td align='center'>" . $e[1] . "</td>" .
				"<td align='left'>" . $e[2] . "</td>" .
				"<td align='right'>&euro;" . number_format($e[3], 2, ',', '.') . "</td>" .
				"<td align='right'>&euro;" . number_format($e[4], 2, ',', '.') . "</td>" .
				"<td align='right'>&euro;" . number_format($e[5], 2, ',', '.') . "</td>" .
				"<td align='right'>&euro;" . number_format($e[6], 2, ',', '.') . "</td>" .
				"<td id='icons'><a class='tooltip' onclick='cancellaDettaglioPagina(" . trim($id) . ")'><li class='ui-state-default ui-corner-all' title='Cancella'><span class='ui-icon ui-icon-trash'></span></li></a></td>" .
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
				'%empy_selected%' => ($_SESSION["meserif"] == "") ? "selected" : "",
				'%gen_selected%' => ($_SESSION["meserif"] == "Gennaio") ? "selected" : "",
				'%feb_selected%' => ($_SESSION["meserif"] == "Febbraio") ? "selected" : "",
				'%mar_selected%' => ($_SESSION["meserif"] == "Marzo") ? "selected" : "",
				'%apr_selected%' => ($_SESSION["meserif"] == "Aprile") ? "selected" : "",
				'%mag_selected%' => ($_SESSION["meserif"] == "Maggio") ? "selected" : "",
				'%giu_selected%' => ($_SESSION["meserif"] == "Giugno") ? "selected" : "",
				'%lug_selected%' => ($_SESSION["meserif"] == "Luglio") ? "selected" : "",
				'%ago_selected%' => ($_SESSION["meserif"] == "Agosto") ? "selected" : "",
				'%set_selected%' => ($_SESSION["meserif"] == "Settembre") ? "selected" : "",
				'%ott_selected%' => ($_SESSION["meserif"] == "Ottobre") ? "selected" : "",
				'%nov_selected%' => ($_SESSION["meserif"] == "Novembre") ? "selected" : "",
				'%dic_selected%' => ($_SESSION["meserif"] == "Dicembre") ? "selected" : "",			
				'%tipoadd%' => $_SESSION["tipoadd"],
				'%ragsocbanca%' => str_replace("'", "&apos;", $_SESSION["ragsocbanca"]),
				'%ibanbanca%' => $_SESSION["ibanbanca"],
				'%azione%' => $this->getAzione(),
				'%confermaTip%' => $this->getConfermaTip(),
				'%descli%' => $_SESSION["cliente"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%class_dettagli%' => $class_dettagli,
				'%thead_dettagli%' => $thead_dettagli,
				'%tbody_dettagli%' => $tbody_dettagli,
				'%dettagliInseriti%' => $_SESSION["dettagliInseriti"],
				'%arrayDettagliInseriti%' => $d_x_array,
				'%arrayIndexDettagliInseriti%' => $_SESSION["indexDettagliInseriti"],
				'%elenco_fornitori%' => $_SESSION["elenco_fornitori"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"]
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
		
?>
				
		
	
	