<?php

require_once 'riepiloghi.abstract.class.php';

class EstraiPdfAndamentoNegozi extends RiepiloghiAbstract {

	private static $_instance = null;

	public $_datiMCT = array();
	public $_totaliCostiRicavi = array();
	
	public static $azioneEstraiPdfAndamentoNegozi = "../riepiloghi/estraiPdfAndamentoNegoziFacade.class.php?modo=start";

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
	
			self::$_instance = new EstraiPdfAndamentoNegozi();
	
			return self::$_instance;
	}
	
	public function start() {
	
		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'pdf.class.php';
	
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$_SESSION["logo"] = self::$root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";
	
		$pdf = Pdf::getInstance();
		$db = Database::getInstance();
		
		$pdf->AliasNbPages();

		/**
		 * Generazione del documento
		 */
		
		$pdf = $this->generaSezioneIntestazione($pdf);
		$pdf = $this->generaSezioneTabellaProgressivi($pdf, $utility, $db, $_SESSION["elencoVociAndamentoNegozio"]);
		$pdf = $this->generaSezioneTabellaUtilePerdita($pdf, $utility, $db, $_SESSION["totaliComplessiviAcquistiMesi"], $_SESSION["totaliComplessiviRicaviMesi"]);
		$pdf = $this->generaSezioneTabellaMctProgressivi($pdf, $utility, $db, $_SESSION["totaliAcquistiMesi"], $_SESSION["totaliRicaviMesi"]);
		
		$pdf->Output();
	}
	
	public function generaSezioneIntestazione($pdf) {

		unset($_SESSION["title"]);
		unset($_SESSION["title1"]);
		unset($_SESSION["title2"]);
		
		$_SESSION["title"] = "Progressivi Mensili Chopin";
		$_SESSION["title1"] = "Dal " . $_SESSION["datareg_da"] . " al " . $_SESSION["datareg_a"];
		
		$negozio = "";
		$negozio = ($_SESSION["codneg_sel"] == "VIL") ? "Villa D'Adda" : $negozio;
		$negozio = ($_SESSION["codneg_sel"] == "BRE") ? "Brembate" : $negozio;
		$negozio = ($_SESSION["codneg_sel"] == "TRE") ? "Trezzo" : $negozio;
		
		if ($_SESSION["codneg_sel"] != "") {
			$_SESSION["title2"] = "Negozio di " . $negozio;
		}
		
		return $pdf;
	}

	public function generaSezioneTabellaProgressivi($pdf, $utility, $db, $elencoVoci) {

		$codnegozio = "";
		$codnegozio = ($_SESSION["codneg_sel"] == "") ?"'VIL','TRE','BRE'" : "'" . $_SESSION["codneg_sel"] . "'";
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%codnegozio%' => $codnegozio
		);

		$this->ricercaVociAndamentoCostiNegozio($utility, $db, $replace);
		$this->ricercaVociAndamentoRicaviNegozio($utility, $db, $replace);
		
		$pdf->AddPage('L');
	
		$header = array("Costi", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->progressiviNegozioTable($header, $_SESSION["elencoVociAndamentoCostiNegozio"], 1);

		$pdf->AddPage('L');
		
		$header = array("Ricavi", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
		$pdf->progressiviNegozioTable($header, $_SESSION["elencoVociAndamentoRicaviNegozio"], -1);
		
		return $pdf;
	}
	
	public function generaSezioneTabellaUtilePerdita($pdf, $utility, $db, $totaliAcquistiMesi, $totaliRicaviMesi) {

		$pdf->AddPage('L');
		
		$header = array("Utile/Perdita", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
		$pdf->progressiviUtilePerditaTable($header, $totaliAcquistiMesi, $totaliRicaviMesi);

		return $pdf;
	}
	
	public function generaSezioneTabellaMctProgressivi($pdf, $utility, $db, $totaliAcquistiMesi, $totaliRicaviMesi) {

		$pdf->Cell(100,10,'','',0,'R',$fill);
		$pdf->Ln();
		
		$header = array("Margine di Contribuzione", "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic", "Totale");
		$pdf->SetFont('Arial','',9);
		$pdf->progressiviMctTable($header, $totaliAcquistiMesi, $totaliRicaviMesi);

		return $pdf;
	}
}	

?>
