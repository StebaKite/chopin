<?php

require_once 'riepiloghi.abstract.class.php';

class EstraiPdfBilancio extends RiepiloghiAbstract implements RiepiloghiEstrazioni {

	private static $_instance = null;

	public static $azioneEstraiPdfBilancio = "../riepiloghi/estraiPdfBilancioFacade.class.php?modo=go";
	public static $queryCosti = "/riepiloghi/costi.sql";
	public static $queryCostiConSaldi = "/riepiloghi/costiConSaldi.sql";
	public static $queryRicavi = "/riepiloghi/ricavi.sql";
	public static $queryRicaviConSaldi = "/riepiloghi/ricaviConSaldi.sql";
	public static $queryAttivo = "/riepiloghi/attivo.sql";
	public static $queryPassivo = "/riepiloghi/passivo.sql";

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

			self::$_instance = new EstraiPdfBilancio();

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
		$pdf = $this->generaSezioneTabellaBilancio($pdf, $utility);

		if ($_SESSION["soloContoEconomico"] == "N") {
			$pdf = $this->generaSezioneIntestazione($pdf);
			$pdf = $this->generaSezioneTabellaBilancioEsercizio($pdf, $utility);
		}

		$pdf->Output();
	}

	public function generaSezioneIntestazione($pdf) {

		if ($_SESSION["codneg_sel"] != "CAS") {
			
			if ($_SESSION["codneg_sel"] != "") {
				$negozio = "";
				$negozio = ($_SESSION["codneg_sel"] == "VIL") ? "Villa D'Adda" : $negozio;
				$negozio = ($_SESSION["codneg_sel"] == "BRE") ? "Brembate" : $negozio;
				$negozio = ($_SESSION["codneg_sel"] == "TRE") ? "Trezzo" : $negozio;
				
				$_SESSION["title2"] = "Negozio di " . $negozio;
			}
			else {
				$_SESSION["title2"] = "Tutti i negozi";
			}
		}
		else {
			$_SESSION["title2"] = "";
		}		
		
		return $pdf;
	}

	public function generaSezioneTabellaBilancio($pdf, $utility) {

		require_once 'database.class.php';
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"],
				'%codnegozio%' => ($_SESSION["codneg_sel"] == "") ? "'VIL','TRE','BRE'" : "'" . $_SESSION["codneg_sel"] . "'"
		);
		
		$db = Database::getInstance();
		$fill = true;		
		
		/**
		 * Costi
		 */
		if ($this->ricercaCosti($utility, $db, $replace) > 0) {
		
			$pdf->AddPage();
			$pdf->SetFont('','B',12);
			$pdf->SetFillColor(171,224,245);
			$pdf->Cell($w[0],6,'COSTI' . str_repeat(' ',87) . 'Parziale ' . EURO . str_repeat(' ',19) . 'Totale ' . EURO,'',0,'L',$fill);
			$pdf->Ln();
			$pdf->Ln();
			
			$pdf->SetFont('Arial','',11);
		
			$pdf->BilancioTable(pg_fetch_all($_SESSION['costiBilancio']), 1);			
			$pdf->TotaleCostiTable($_SESSION['totaleCosti']);
		}		
		
		/**
		 * Ricavi
		 */		
		if ($this->ricercaRicavi($utility, $db, $replace) > 0) {
			
			$pdf->AddPage();		
			$pdf->SetFont('','B',12);
			$pdf->SetFillColor(171,224,245);
			$pdf->Cell($w[0],6,'RICAVI' . str_repeat(' ',87) . 'Parziale ' . EURO . str_repeat(' ',19) . 'Totale ' . EURO,'',0,'L',$fill);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Arial','',11);
		
			$pdf->BilancioTableRicavi(pg_fetch_all($_SESSION['ricaviBilancio']), -1);
			$pdf->TotaleRicaviTable(abs($_SESSION['totaleRicavi']));			
		}
		
		/**
		 * Riepilogo totali
		 */
		if (($_SESSION['numCostiTrovati'] > 0) or ($_SESSION['numRicaviTrovati'] > 0)) {
			
			if (abs($_SESSION['totaleRicavi']) >= abs($_SESSION['totaleCosti'])) {
				$pdf->BilancioCostiTable(abs($_SESSION['totaleRicavi']), abs($_SESSION['totaleCosti']));
			}
			else {
				if (abs($_SESSION['totaleRicavi']) < abs($_SESSION['totaleCosti'])) {
					$pdf->BilancioRicaviTable(abs($_SESSION['totaleRicavi']), abs($_SESSION['totaleCosti']));
				}				
			}		
		}
		
		return $pdf;
	}
	
	public function generaSezioneTabellaBilancioEsercizio($pdf, $utility) {
		
		require_once 'database.class.php';
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"],
				'%codnegozio%' => ($_SESSION["codneg_sel"] == "") ? "'VIL','TRE','BRE'" : "'" . $_SESSION["codneg_sel"] . "'"
		);
		
		$db = Database::getInstance();
		$fill = true;				

		/**
		 * Attivo
		 */
		if ($this->ricercaAttivo($utility, $db, $replace) > 0) {
			
			$pdf->AddPage();
			$pdf->SetFont('','B',12);
		    $pdf->SetFillColor(171,224,245);
			$pdf->Cell($w[0],6,"ATTIVITA'" . str_repeat(' ',82) . 'Parziale ' . EURO . str_repeat(' ',18) . 'Totale ' . EURO,'',0,'L',$fill);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFillColor(224,235,255);
			$pdf->SetFont('Arial','',11);
		
			$pdf->BilancioEsercizioTable(pg_fetch_all($_SESSION['attivoBilancio']));			
	 		$pdf->TotaleAttivoTable(abs($_SESSION['totaleAttivo']));
		}
		
		/**
		 * Passivo
		 */
		if ($this->ricercaPassivo($utility, $db, $replace) > 0) {
			
			$pdf->AddPage();
			$pdf->SetFont('','B',12);
			$pdf->SetFillColor(171,224,245);		
			$pdf->Cell($w[0],6,"PASSIVITA'" . str_repeat(' ',80) . 'Parziale ' . EURO . str_repeat(' ',18) . 'Totale ' . EURO,'',0,'L',$fill);
			$pdf->Ln();
			$pdf->Ln();			
			$pdf->SetFillColor(224,235,255);
			$pdf->SetFont('Arial','',11);
		
			$pdf->BilancioEsercizioTable(pg_fetch_all($_SESSION['passivoBilancio']));
			$pdf->TotalePassivoTable(abs($_SESSION['totalePassivo']));		
		}
		
		return $pdf;
	}		
}

?>