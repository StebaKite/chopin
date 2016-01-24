<?php

require_once 'primanota.abstract.class.php';

class VisualizzaRegistrazioneTemplate extends PrimanotaAbstract {

	private static $_instance = null;

	private static $pagina = "/primanota/visualizzaRegistrazione.form.html";

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

			self::$_instance = new VisualizzaRegistrazioneTemplate();

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
	
		/**
		 * Prepara la tabella dei dettagli della registrazione da iniettare in pagina
		 */
	
		$result = $_SESSION["elencoDettagliRegistrazione"];
	
		$dettaglioregistrazione = pg_fetch_all($result);
		$tbodyDettagli = "";
	
		foreach ($dettaglioregistrazione as $row) {
	
			$tbodyDettagli = $tbodyDettagli .
			"<tr id='" . $row["id_dettaglio_registrazione"] . "'>" .
			"<td align='left'>" . $row["cod_conto"] . $row["cod_sottoconto"] . " - " . $row["des_sottoconto"] . "</td>" .
			"<td align='right'>&euro;" . number_format(trim($row["imp_registrazione"]), 2, ',', '.') . "</td>" .
			"<td align='center'>" . $row["ind_dareavere"] . "</td>" .
			"</tr>";
		}
		
		/**
		 * Prepara la tabella delle multiscadenze da iniettare in pagina
		 */
		
		$theadScadenze = "";
		$tbodyScadenze = "";
		
		if (isset($_SESSION["numeroScadenzeRegistrazione"])) {
		
			$class_scadenzesuppl = "datiCreateSottile";
		
			$theadScadenze =
			"<tr>" .
			"<th width='100' align='center'>Scadenza</th>" .
			"<th width='100' align='right'>Importo</th>" .
			"</tr>";
				
			$scadenzeregistrazione = $_SESSION["elencoScadenzeRegistrazione"];
				
			foreach ($scadenzeregistrazione as $row) {
					
				$tbodyScadenze .=
				"<tr>" .
				"<td align='center'>" . date("d/m/Y",strtotime($row['dat_scadenza'])) . "</td>" .
				"<td align='right'>" . number_format(round($row['imp_in_scadenza'],2), 2, ',', '.') . "</td>" .
				"</tr>";
			}
		}
		
		$replace = array(
				'%titoloPagina%' => $this->getTitoloPagina(),
				'%referer%' => $_SERVER["HTTP_REFERER"],
				'%datascad_da%' => $_SESSION["datascad_da"],
				'%datascad_a%' => $_SESSION["datascad_a"],
				'%confermaTip%' => $this->getConfermaTip(),
				'%idregistrazione%' => $_SESSION["idRegistrazione"],
				'%descreg%' => str_replace("'", "&apos;", $_SESSION["descreg"]),
				'%datascad%' => $_SESSION["datascad"],
				'%datareg%' => $_SESSION["datareg"],
				'%numfatt%' => $_SESSION["numfatt"],
				'%codneg_sel%' => $_SESSION["codneg_sel"],
				'%villa-checked%' => ($_SESSION["codneg"] == "VIL") ? "checked" : "",
				'%brembate-checked%' => ($_SESSION["codneg"] == "BRE") ? "checked" : "",
				'%trezzo-checked%' => ($_SESSION["codneg"] == "TRE") ? "checked" : "",
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%elenco_causali%' => $_SESSION["elenco_causali"],
				'%elenco_fornitori%' => $_SESSION["elenco_fornitori"],
				'%elenco_clienti%' => $_SESSION["elenco_clienti"],
				'%elenco_conti%' => $_SESSION["elenco_conti"],
				'%tbody_dettagli%' => $tbodyDettagli,
				'%class_scadenzesuppl%' => $class_scadenzesuppl,
				'%thead_scadenze%' => $theadScadenze,
				'%tbody_scadenze%' => $tbodyScadenze,				
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}	
}		
		
?>