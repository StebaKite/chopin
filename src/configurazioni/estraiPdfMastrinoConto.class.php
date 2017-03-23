<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'pdf.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';


class EstraiPdfMastrinoConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface
{
	
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::ESTRAI_PDF_MASTRINO])) $_SESSION[self::ESTRAI_PDF_MASTRINO] = serialize(new EstraiPdfMastrinoConto());
		return unserialize($_SESSION[self::ESTRAI_PDF_MASTRINO]);
	}

	public function start()
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$_SESSION["logo"] = $this->root . $array["logo"];
		$_SESSION["creator"] = "Nexus6";

		$pdf = Pdf::getInstance();

		$pdf->AliasNbPages();

		/**
		 * Generazione del documento
		*/
		
		$sottoconto = Sottoconto::getInstance();
		$conto = Conto::getInstance();
		
		$pdf = $this->generaSezioneIntestazione($pdf, $conto, $sottoconto);
		$pdf = $this->generaSezioneTabellaMastrinoConto($pdf, $utility, $sottoconto);

		$pdf->Output();
	}

	public function go() {$this->start();}
	
	public function generaSezioneIntestazione($pdf, $conto, $sottoconto) {
		
		$_SESSION["title"] = "Registrazioni dal " . $sottoconto->getDataRegistrazioneDa() . " al " . $sottoconto->getDataRegistrazioneA();

		$negozio = "";
		$negozio = ($sottoconto->getCodNegozio() == "VIL") ? "Villa D'Adda" : $negozio;
		$negozio = ($sottoconto->getCodNegozio() == "BRE") ? "Brembate" : $negozio;
		$negozio = ($sottoconto->getCodNegozio() == "TRE") ? "Trezzo" : $negozio;

		if ($negozio != "") $_SESSION["title1"] = "Negozio di " . $negozio;
		else $_SESSION["title1"] = "Tutti i negozi";
		
		$_SESSION["title2"] = $conto->getCatConto() . " : " . $conto->getDesConto() . " / " . $sottoconto->getDesSottoconto();
		
		return $pdf;
	}

	public function generaSezioneTabellaMastrinoConto($pdf, $utility, $sottoconto) {
		
		$pdf->AddPage();

		$header = array("Data", "Descrizione", "Dare", "Avere", "Saldo");
		$pdf->SetFont('Arial','',9);
		$pdf->MastrinoContoTable($header, $sottoconto->getRegistrazioniTrovate());

		return $pdf;
	}
}

?>