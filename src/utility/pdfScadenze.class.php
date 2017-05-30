<?php

require_once 'pdf.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'scadenzaFornitore.class.php';

class PdfScadenze extends Pdf {

	public function getInstance()
	{
		if (!isset($_SESSION[self::PDF_SCADENZE])) $_SESSION[self::PDF_SCADENZE] = serialize(new PdfScadenze());
		return unserialize($_SESSION[self::PDF_SCADENZE]);
	}

	public function ScadenzeTable($header, $data) {

		// Colors, line width and bold font
		$this->SetFillColor(28,148,196);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);

		// Header
		$w = array(20, 80, 90, 25, 25, 25);
		for($i=0;$i<count($header);$i++) {
			$this->Cell($w[$i],10,$header[$i],1,0,'C',true);
		}

		$this->Ln();

		// Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		$this->SetFont('','',8);

		// Data

		$idfornitore_break = "";
		$datscadenza_break = "";
		$totale_fornitore = 0;
		$totale_scadenze = 0;

		$fill = false;
		foreach($data as $row) {

			if (($idfornitore_break == "") && ($datscadenza_break == "")) {
				$idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
				$datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
				$desfornitore = trim($row[Fornitore::DES_FORNITORE]);
				$datscadenza  = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
			}

			if ((trim($row[ScadenzaFornitore::ID_FORNITORE]) != $idfornitore_break) | (trim($row[ScadenzaFornitore::DAT_SCADENZA]) != $datscadenza_break)) {

				$this->SetFont('','B',12);
				$this->Cell($w[0],10,'','LR',0,'L',$fill);
				$this->Cell($w[1],10,'','LR',0,'L',$fill);
				$this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
				$this->Cell($w[3],10,'','LR',0,'L',$fill);
				$this->Cell($w[4],10,'','LR',0,'C',$fill);
				$this->Cell($w[5],10, EURO . number_format($totale_fornitore, 2, ',', '.'),'LR',0,'R',$fill);
				$this->Ln();
				$fill = !$fill;

				$desfornitore = trim($row[Fornitore::DES_FORNITORE]);
				$datscadenza  = trim($row[ScadenzaFornitore::DAT_SCADENZA]);
				$idfornitore_break = trim($row[ScadenzaFornitore::ID_FORNITORE]);
				$datscadenza_break = trim($row[ScadenzaFornitore::DAT_SCADENZA]);

				$totale_scadenze += $totale_fornitore;
				$totale_fornitore = 0;
			}

			if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_APERTA) {
				$stascadenza = self::SCADENZA_DAPAGARE;
				$c1 = "0";
				$c2 = "0";
				$c3 = "0";
			}
			if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
				$stascadenza = self::SCADENZA_PAGATA;
				$c1 = "0";
				$c2 = "0";
				$c3 = "0";
			}
			if (trim($row[ScadenzaFornitore::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
				$stascadenza = self::SCADENZA_POSTICIPATA;
				$c1 = "51";
				$c2 = "153";
				$c3 = "255";
			}

			$this->SetFont('','',10);
			$this->Cell($w[0],6,iconv('UTF-8', 'windows-1252',$datscadenza),'LR',0,'L',$fill);
			$this->Cell($w[1],6,iconv('UTF-8', 'windows-1252',$desfornitore),'LR',0,'L',$fill);
			$this->Cell($w[2],6,iconv('UTF-8', 'windows-1252',$row[ScadenzaFornitore::NOTA_SCADENZA]),'LR',0,'L',$fill);
			$this->Cell($w[3],6,iconv('UTF-8', 'windows-1252',$row[ScadenzaFornitore::TIP_ADDEBITO]),'LR',0,'C',$fill);
			$this->SetFont('','',10);
			$this->SetTextColor($c1, $c2, $c3);
			$this->Cell($w[4],6,iconv('UTF-8', 'windows-1252',$stascadenza),'LR',0,'C',$fill);
			$this->Cell($w[5],6, EURO . number_format($row[ScadenzaFornitore::IMP_IN_SCADENZA], 2, ',', '.'),'LR',0,'R',$fill);
			$this->SetFont('','B',8);
			$this->SetTextColor(0);
			$this->Ln();
			$fill = !$fill;

			$desfornitore = "";
			$datscadenza = "";
			$totale_fornitore += trim($row[ScadenzaFornitore::IMP_IN_SCADENZA]);
		}

		$this->SetFont('','B',12);
		$this->Cell($w[0],10,'','LR',0,'L',$fill);
		$this->Cell($w[1],10,'','LR',0,'L',$fill);
		$this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
		$this->Cell($w[3],10,'','LR',0,'L',$fill);
		$this->Cell($w[4],10,'','LR',0,'C',$fill);
		$this->Cell($w[5],10, EURO . number_format($totale_fornitore, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Ln();
		$fill = !$fill;

		$totale_scadenze += $totale_fornitore;

		$this->SetFont('','B',12);
		$this->Cell($w[0],15,'','LR',0,'L',$fill);
		$this->Cell($w[1],15,'','LR',0,'L',$fill);
		$this->Cell($w[2],15,'Totale Scadenze','LR',0,'R',$fill);
		$this->Cell($w[3],15,'','LR',0,'L',$fill);
		$this->Cell($w[4],15,'','LR',0,'C',$fill);
		$this->Cell($w[5],15, EURO . number_format($totale_scadenze, 2, ',', '.'),'LR',0,'R',$fill);
		$this->Ln();
		$fill = !$fill;

		$this->Cell(array_sum($w),0,'','T');
	}

	/**
	 * Tabella per stampa scadenziario Clienti
	 */
	public function ScadenzeClientiTable($header, $data) {

		// Colors, line width and bold font
		$this->SetFillColor(28,148,196);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B',10);

		// Header
		$w = array(20, 80, 90, 25, 25, 25);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],10,$header[$i],1,0,'C',true);
			$this->Ln();

			// Color and font restoration
			$this->SetFillColor(224,235,255);
			$this->SetTextColor(0);
			$this->SetFont('');
			$this->SetFont('','',8);

			// Data

			$idcliente_break = "";
			$datregistrazione_break = "";
			$totale_cliente = 0;
			$totale_scadenze = 0;

			$fill = false;
			foreach($data as $row) {

				if (($idcliente_break == "") && ($datregistrazione_break == "")) {
					$idcliente_break = trim($row[ScadenzaCliente::ID_CLIENTE]);
					$datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
					$descliente = trim($row[Cliente::DES_CLIENTE]);
					$datregistrazione  = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
				}

				if ((trim($row[ScadenzaCliente::ID_CLIENTE]) != $idcliente_break) |
						(trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]) != $datregistrazione_break)) {

							$this->SetFont('','B',12);
							$this->Cell($w[0],10,'','LR',0,'L',$fill);
							$this->Cell($w[1],10,'','LR',0,'L',$fill);
							$this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
							$this->Cell($w[3],10,'','LR',0,'L',$fill);
							$this->Cell($w[4],10,'','LR',0,'C',$fill);
							$this->Cell($w[5],10, EURO . number_format($totale_cliente, 2, ',', '.'),'LR',0,'R',$fill);
							$this->Ln();
							$fill = !$fill;

							$descliente = trim($row[Cliente::DES_CLIENTE]);
							$datregistrazione  = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);
							$idcliente_break = trim($row[ScadenzaCliente::ID_CLIENTE]);
							$datregistrazione_break = trim($row[ScadenzaCliente::DAT_REGISTRAZIONE]);

							$totale_scadenze += $totale_cliente;
							$totale_cliente = 0;
						}

						if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_APERTA) {
							$stascadenza = "Da Incassare";
							$c1 = "0";
							$c2 = "0";
							$c3 = "0";
						}
						if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_CHIUSA) {
							$stascadenza = "Incassata";
							$c1 = "0";
							$c2 = "0";
							$c3 = "0";
						}
						if (trim($row[ScadenzaCliente::STA_SCADENZA]) == self::SCADENZA_RIMANDATA) {
							$stascadenza = "Posticipata";
							$c1 = "51";
							$c2 = "153";
							$c3 = "255";
						}

						$this->SetFont('','',10);
						$this->Cell($w[0],6,iconv('UTF-8', 'windows-1252',$datregistrazione),'LR',0,'L',$fill);
						$this->Cell($w[1],6,iconv('UTF-8', 'windows-1252',$descliente),'LR',0,'L',$fill);
						$this->Cell($w[2],6,iconv('UTF-8', 'windows-1252',$row[ScadenzaCliente::NOTA]),'LR',0,'L',$fill);
						$this->Cell($w[3],6,iconv('UTF-8', 'windows-1252',$row[ScadenzaCliente::TIP_ADDEBITO]),'LR',0,'C',$fill);
						$this->SetFont('','',10);
						$this->SetTextColor($c1, $c2, $c3);
						$this->Cell($w[4],6,iconv('UTF-8', 'windows-1252',$stascadenza),'LR',0,'C',$fill);
						$this->Cell($w[5],6, EURO . number_format($row[ScadenzaCliente::IMP_REGISTRAZIONE], 2, ',', '.'),'LR',0,'R',$fill);
						$this->SetFont('','B',8);
						$this->SetTextColor(0);
						$this->Ln();
						$fill = !$fill;

						$descliente = "";
						$datregistrazione = "";
						$totale_cliente += trim($row[ScadenzaCliente::IMP_REGISTRAZIONE]);
			}

			$this->SetFont('','B',12);
			$this->Cell($w[0],10,'','LR',0,'L',$fill);
			$this->Cell($w[1],10,'','LR',0,'L',$fill);
			$this->Cell($w[2],10,'Totale','LR',0,'R',$fill);
			$this->Cell($w[3],10,'','LR',0,'L',$fill);
			$this->Cell($w[4],10,'','LR',0,'C',$fill);
			$this->Cell($w[5],10, EURO . number_format($totale_cliente, 2, ',', '.'),'LR',0,'R',$fill);
			$this->Ln();
			$fill = !$fill;

			$totale_scadenze += $totale_cliente;

			$this->SetFont('','B',12);
			$this->Cell($w[0],10,'','LR',0,'L',$fill);
			$this->Cell($w[1],10,'','LR',0,'L',$fill);
			$this->Cell($w[2],10,'Totale Incassi','LR',0,'R',$fill);
			$this->Cell($w[3],10,'','LR',0,'L',$fill);
			$this->Cell($w[4],10,'','LR',0,'C',$fill);
			$this->Cell($w[5],10, EURO . number_format($totale_scadenze, 2, ',', '.'),'LR',0,'R',$fill);
			$this->Ln();
			$fill = !$fill;

			$this->Cell(array_sum($w),0,'','T');
	}
}

?>