<?php

require_once 'riepiloghi.abstract.class.php';

class EstraiPdfBilancio extends RiepiloghiAbstract {

	private static $_instance = null;

	public static $azioneEstraiPdfBilancio = "../riepiloghi/estraiPdfBilancioFacade.class.php?modo=go";
	public static $queryCosti = "/riepiloghi/costi.sql";
	public static $queryRicavi = "/riepiloghi/ricavi.sql";
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
		if ($_SESSION["tipoBilancio"] == "Periodico") {
			$pdf = $this->generaSezioneIntestazione($pdf);
			$pdf = $this->generaSezioneTabellaBilancio($pdf, $utility);
		}
		elseif ($_SESSION["tipoBilancio"] == "Esercizio") {
			$pdf = $this->generaSezioneIntestazione($pdf);
			$pdf = $this->generaSezioneTabellaBilancioEsercizio($pdf, $utility);
		}
		

		$pdf->Output();
	}

	public function generaSezioneIntestazione($pdf) {

		$negozio = "";
		$negozio = ($_SESSION["codneg_sel"] == "VIL") ? "Villa D'Adda" : $negozio;
		$negozio = ($_SESSION["codneg_sel"] == "BRE") ? "Brembate" : $negozio;
		$negozio = ($_SESSION["codneg_sel"] == "TRE") ? "Trezzo" : $negozio;

		$_SESSION["title2"] = $_SESSION["catconto_sel"] . " - Negozio di " . $negozio;
		
		return $pdf;
	}

	public function generaSezioneTabellaBilancio($pdf, $utility) {

		require_once 'database.class.php';
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"],
				'%codnegozio%' => $_SESSION["codneg_sel"]
		);
		
		$db = Database::getInstance();
		
		$pdf->AddPage();
		$pdf->SetFont('','B',12);
		$pdf->Cell($w[0],6,'COSTI' . str_repeat(' ',87) . 'Parziale ' . EURO . str_repeat(' ',19) . 'Totale ' . EURO,0,'R');
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->SetFont('Arial','',11);
		$pdf->BilancioTable($this->ricercaCosti($utility, $db, $replace));

		/**
		 * Totali x Utile
		 */
		if ($_SESSION['totaleRicavi'] >= $_SESSION['totaleCosti']) {
			$pdf->BilancioCostiTable($_SESSION['totaleRicavi'], $_SESSION['totaleCosti']);
		}
		
		$pdf->AddPage();		
		$pdf->SetFont('','B',12);
		$pdf->Cell($w[0],6,'RICAVI' . str_repeat(' ',87) . 'Parziale ' . EURO . str_repeat(' ',19) . 'Totale ' . EURO,0,'R');
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','',11);
		$pdf->BilancioTable($this->ricercaRicavi($utility, $db, $replace));

		/**
		 * Totali x Perdita
		 */
		if ($_SESSION['totaleRicavi'] < $_SESSION['totaleCosti']) {
			$pdf->BilancioRicaviTable($_SESSION['totaleRicavi'], $_SESSION['totaleCosti']);
		}
		
		return $pdf;
	}
	
	public function generaSezioneTabellaBilancioEsercizio($pdf, $utility) {
		
		require_once 'database.class.php';
		
		$replace = array(
				'%datareg_da%' => $_SESSION["datareg_da"],
				'%datareg_a%' => $_SESSION["datareg_a"],
				'%catconto%' => $_SESSION["catconto_sel"],
				'%codnegozio%' => $_SESSION["codneg_sel"]
		);
		
		$db = Database::getInstance();

		/**
		 * Attivo
		 */
		$pdf->AddPage();
		$pdf->SetFont('','B',12);
		$pdf->Cell($w[0],6,"ATTIVITA'",0,'R');
		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetFont('Arial','',11);
		$pdf->BilancioEsercizioTable($this->ricercaAttivo($utility, $db, $replace));
		
		/**
		 * Passivo
		 */
		$pdf->AddPage();
		$pdf->SetFont('','B',12);
		$pdf->Cell($w[0],6,"PASSIVITA'",0,'R');
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','',11);
		$pdf->BilancioEsercizioTable($this->ricercaPassivo($utility, $db, $replace));
		

		return $pdf;
	}
	
	public function ricercaCosti($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryCosti;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['costiBilancio'] = $result;
		}
		else {
			unset($_SESSION['costiBilancio']);
			$_SESSION['numCostiTrovati'] = 0;
		}
		return pg_fetch_all($result);;
	}
	
	public function ricercaRicavi($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryRicavi;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['ricaviBilancio'] = $result;
		}
		else {
			unset($_SESSION['costiBilancio']);
			$_SESSION['numRicaviTrovati'] = 0;
		}
		return pg_fetch_all($result);;
	}
	
	public function ricercaAttivo($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryAttivo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['attivoBilancio'] = $result;
			$_SESSION['numAttivoTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['attivoBilancio']);
			$_SESSION['numAttivoTrovati'] = 0;
		}
		return pg_fetch_all($result);;
	}
	
	public function ricercaPassivo($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryPassivo;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['passivoBilancio'] = $result;
			$_SESSION['numPassivoTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['passivoBilancio']);
			$_SESSION['numPassivoTrovati'] = 0;
		}
		return pg_fetch_all($result);;
	}
		
}

?>