<?php

require_once 'configurazioni.abstract.class.php';

class EstraiPdfMastrinoConto extends ConfigurazioniAbstract {

	private static $_instance = null;

	public static $azioneEstraiPdfMastrinoConto = "../configurazioni/estraiPdfMastrinoContoFacade.class.php?modo=go";
	public static $queryRicercaRegistrazioniConto = "/configurazioni/ricercaRegistrazioniConto.sql";
	public static $queryRicercaRegistrazioniContoConSaldi = "/configurazioni/ricercaRegistrazioniContoConSaldi.sql";
	
	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new EstraiPdfMastrinoConto();

		return self::$_instance;
	}

	public function start() {

		require_once 'utility.class.php';
		require_once 'pdf.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";

		$pdf = Pdf::getInstance();

		$pdf->AliasNbPages();

		/**
		 * Generazione del documento
		*/
		$pdf = $this->generaSezioneIntestazione($pdf);
		$pdf = $this->generaSezioneTabellaMastrinoConto($pdf, $utility);

		$pdf->Output();
	}

	public function generaSezioneIntestazione($pdf) {

		$_SESSION["title"] = "Registrazioni dal " . $_SESSION["datareg_da"] . " al " . $_SESSION["datareg_a"];		
		$_SESSION["title1"] = "";

		if ($_SESSION["codneg_sel"] != "CAS") {
			$negozio = "";
			$negozio = ($_SESSION["codneg_sel"] == "VIL") ? "Villa D'Adda" : $negozio;
			$negozio = ($_SESSION["codneg_sel"] == "BRE") ? "Brembate" : $negozio;
			$negozio = ($_SESSION["codneg_sel"] == "TRE") ? "Trezzo" : $negozio;
	
			if ($negozio != "") $_SESSION["title1"] = "Negozio di " . $negozio;
			else $_SESSION["title1"] = "Tutti i negozi";			
		}		
		
		$_SESSION["title2"] = $_SESSION["catconto"] . " : " . $_SESSION["desconto"] . " / " . $_SESSION["dessottoconto"];
		
		return $pdf;
	}

	public function generaSezioneTabellaMastrinoConto($pdf, $utility) {

		$pdf->AddPage();

		$header = array("Data", "Descrizione", "Dare", "Avere", "Saldo");
		$pdf->SetFont('Arial','',9);
		$pdf->MastrinoContoTable($header, $this->ricercaDati($utility));

		return $pdf;
	}

	public function ricercaDati($utility) {

		require_once 'database.class.php';

		$filtro = "";
		$filtroSaldo = "";		

		if (($_SESSION['datareg_da'] != "") & ($_SESSION['datareg_a'] != "")) {
			$filtro .= "AND registrazione.dat_registrazione between '" . $_SESSION['datareg_da'] . "' and '" . $_SESSION['datareg_a'] . "'" ;
			$filtroSaldo .= "AND saldo.dat_saldo = '" . $_SESSION['datareg_da'] . "'" ;
		}

		if ($_SESSION['codneg_sel'] != "") {
			$filtro .= " AND registrazione.cod_negozio = '" . $_SESSION['codneg_sel'] . "'" ;
			$filtroSaldo .= " AND saldo.cod_negozio = '" . $_SESSION['codneg_sel'] . "'" ;
		}
		
		$replace = array(
				'%cod_conto%' => trim($_SESSION["codconto"]),
				'%cod_sottoconto%' => trim($_SESSION["codsottoconto"]),
				'%filtro_date%' => $filtro,
				'%filtro_date_saldo%' => $filtroSaldo
		);
					
		$array = $utility->getConfig();
		
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaRegistrazioniContoConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaRegistrazioniConto;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
	
		// esegue la query
	
		$db = Database::getInstance();
		$result = $db->getData($sql);
		
		return pg_fetch_all($result);
	}
}

?>