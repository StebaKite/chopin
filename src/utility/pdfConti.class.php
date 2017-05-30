<?php

require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'conto.class.php';

class PdfConti extends Pdf {

	public function getInstance()
	{
		if (!isset($_SESSION[self::PDF_CONTI])) $_SESSION[self::PDF_CONTI] = serialize(new PdfConti());
		return unserialize($_SESSION[self::PDF_CONTI]);
	}

	public function MastrinoContoTable($header, $data) {

		// Colors, line width and bold font
		$this->SetFillColor(28,148,196);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);

		// Header
		$w = array(20, 80, 30, 30, 30);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],10,$header[$i],1,0,'C',true);
			$this->Ln();

			// Color and font restoration
			$this->SetFillColor(224,235,255);
			$this->SetTextColor(0);
			$this->SetFont('');
			$this->SetFont('','',8);

			// Data
			$totaleDare = 0;
			$totaleAvere = 0;
			$saldo = 0;

			$fill = false;
			foreach($data as $row) {

				if ($row['ind_dareavere'] == 'D') {
					$totaleDare = $totaleDare + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
					$impDare = $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
					$euroAvere = "";
					$impAvere = "";
				}
				elseif ($row['ind_dareavere'] == 'A') {
					$totaleAvere = $totaleAvere + $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
					$impDare = "";
					$impAvere = $row[DettaglioRegistrazione::IMP_REGISTRAZIONE];
					$euroDare = "";
				}

				if (trim($row[Conto::TIP_CONTO]) == self::CONTO_IN_DARE) {
					$saldo = $totaleDare - $totaleAvere;
				}
				elseif (trim($row[Conto::TIP_CONTO]) == self::CONTO_IN_AVERE) {
					$saldo = $totaleAvere - $totaleDare;
				}

				$this->SetFont('','',10);
				$fill = !$fill;

				$this->Cell($w[0],6,iconv('UTF-8', 'windows-1252',date("d/m/Y",strtotime($row[Registrazione::DAT_REGISTRAZIONE]))),'LR',0,'L',$fill);
				$this->Cell($w[1],6,iconv('UTF-8', 'windows-1252',trim($row[Registrazione::DES_REGISTRAZIONE])),'LR',0,'L',$fill);
				$this->Cell($w[2],6,number_format($impDare, 2, ',', '.'),'LR',0,'R',$fill);
				$this->Cell($w[3],6,number_format($impAvere, 2, ',', '.'),'LR',0,'R',$fill);

				if ($saldo < 0) {
					$this->SetTextColor(255,0,0);
					$this->SetFont('','B',10);
				}

				$this->Cell($w[4],6,number_format($saldo, 2, ',', '.'),'LR',0,'R',$fill);
				$this->Ln();

				$this->SetFont('');
				$this->SetTextColor(0);
			}

			$this->SetFillColor(224,235,255);
			$this->SetTextColor(0);

			$this->SetFont('','B',10);
			$this->Cell($w[0],6,'','LR',0,'L',$fill);
			$this->Cell($w[1],6,'Totale ' . EURO ,'LR',0,'R',$fill);
			$this->Cell($w[2],6,number_format($totaleDare, 2, ',', '.'),'LR',0,'R',$fill);
			$this->Cell($w[3],6,number_format($totaleAvere, 2, ',', '.'),'LR',0,'R',$fill);
			$this->Cell($w[4],6,number_format($saldo, 2, ',', '.'),'LR',0,'R',$fill);
			$this->Ln();

			$this->Cell(array_sum($w),0,'','T');
	}
}

?>