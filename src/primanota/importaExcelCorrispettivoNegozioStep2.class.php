<?php

require_once 'primanota.abstract.class.php';

class ImportaExcelCorrispettivoNegozioStep2 extends primanotaAbstract {

	private static $_instance = null;

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

			self::$_instance = new ImportaExcelCorrispettivoNegozioStep2();
			return self::$_instance;
	}

	public function go() {

		require_once 'importaExcelCorrispettivoNegozioStep1.class.php';
		require_once 'utility.class.php';
		require_once 'database.class.php';
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		
		$array = $utility->getConfig();

		if (isset($_SESSION["corrispettiviTrovati"])) {

			$db->beginTransaction();				
			
			$negozio = $_SESSION["codneg"];
			$causale = $array["corrispettiviNegozio"];
			$stareg = "00";
			$corrispettiviTrovati = $_SESSION["corrispettiviTrovati"];
			$corrispettiviInseriti = 0;
			$corrispettiviIgnorati = 0;
				
			foreach($corrispettiviTrovati as $corrispettivo_row) {
				
				$numeroCella = 1;
				$datareg = "";
				$totale = "";
				$importo10 = "";
				$importo22 = "";
				$iva10 = "";
				$iva22 = "";
				$contoCorrispettivo = explode(" - ", $array['contoCorrispettivoNegozi']);
				
				foreach($corrispettivo_row as $corrispettivo_col) {
		
					/**
					 * Prelevo tutte le celle della riga
					 */
					
					switch ($numeroCella) {
						case 1:
							$datareg = $corrispettivo_col;
							break;
						case 2:
							$totale = $corrispettivo_col;
							break;
						case 3:	
							$importo10 = $corrispettivo_col;
							$iva10 = 1.10;
							break;
						case 4:
							$importo22 = $corrispettivo_col;
							$iva22 = 1.22;
							break;
					}
					$numeroCella ++;					
				}

				/**
				 * Controllo gli importi dei due reparti per vedere se creare il corrispettivo
				 */
				if ($importo10 > 0) {
					
					/**
					 * Controllo che il corrispettivo non sia già stato inserito
					 */
										
					if ($this->isNew($db, $utility, $datareg, $negozio, $contoCorrispettivo[0], $importo10)) {
						$corrispettiviInseriti ++;
						$dettagliInseriti = $this->generaDettagliCorrispettivo($array, $importo10, $iva10);
						if (!$this->creaCorrispettivoNegozio($db, $utility, $datareg, $negozio, $causale, $stareg, $dettagliInseriti)) {
							$_SESSION["messaggioImportFileOk"] = "Errore imprevisto, ripristino eseguito";
							break;
						}						
					}
					else $corrispettiviIgnorati ++;
				}
				
				if ($importo22 > 0) {
					
					if ($this->isNew($db, $utility, $datareg, $negozio, $contoCorrispettivo[0], $importo22)) {
						$corrispettiviInseriti ++;
						$dettagliInseriti = $this->generaDettagliCorrispettivo($array, $importo22, $iva22);
						if (!$this->creaCorrispettivoNegozio($db, $utility, $datareg, $negozio, $causale, $stareg, $dettagliInseriti)) {
							$_SESSION["messaggioImportFileErr"] = "Errore imprevisto, ripristino eseguito";
							break;
						}						
					}
					else $corrispettiviIgnorati ++;
				}
			}
			$db->commitTransaction();
			$_SESSION["messaggioImportFileOk"]  = "<br/>&ndash; corrispettivi inseriti " . $corrispettiviInseriti;
			$_SESSION["messaggioImportFileOk"] .= "<br/>&ndash; corrispettivi gi&agrave; esistenti " . $corrispettiviIgnorati;
		}
		
		$importaExcelCorrispettivoNegozioStep1 = ImportaExcelCorrispettivoNegozioStep1::getInstance();
		$importaExcelCorrispettivoNegozioStep1->start();
	}
	
	private function generaDettagliCorrispettivo($array, $importo, $aliquota) {

		$dettagliInseriti = array();
		$dettaglio = array();

		if ($_SESSION["contocassa"] == "S")
			$contoDare = explode(" - ", $array['contoCassa']);
		else
			$contoDare = explode(" - ", $array['contoBanca']);
						
		$contoErario = explode(" - ", $array['contoErarioNegozi']);
		$contoCorrispettivo = explode(" - ", $array['contoCorrispettivoNegozi']);

		/**
		 * Primo dettaglio registrazione
		 */
		
		array_push($dettaglio, $contoDare[0]);
		array_push($dettaglio, $importo);
		array_push($dettaglio, "D");
		
		array_push($dettagliInseriti, $dettaglio);		
		unset($dettaglio);
		
		/**
		 * Secondo dettaglio registrazione
		 */
		
		$dettaglio = array();
		
		$imponibile = round($importo / $aliquota, 2);
		$iva = round($imponibile * (round($aliquota / 10,1)), 2);

		// sistemazione della squadratura generata dagli arrotondamenti
		$differenza = round($importo - ($imponibile + $iva),2);
		if ($differenza < 0) $imponibile += $differenza;
		if ($differenza > 0) $iva -= $differenza;
		
		array_push($dettaglio, $contoErario[0]);
		array_push($dettaglio, $iva);
		array_push($dettaglio, "A");
		
		array_push($dettagliInseriti, $dettaglio);
		unset($dettaglio);
		
		/**
		 * Terzo dettaglio registrazione
		 */

		$dettaglio = array();
		
		array_push($dettaglio, $contoCorrispettivo[0]);
		array_push($dettaglio, $imponibile);
		array_push($dettaglio, "A");
		
		array_push($dettagliInseriti, $dettaglio);
		unset($dettaglio);
		
		return $dettagliInseriti;
	}

	private function creaCorrispettivoNegozio($db, $utility, $datareg, $codneg, $causale, $stareg, $dettagliInseriti) {
	
		$descreg = "Incasso corrispettivi negozio di " . $codneg;
		$codneg = "'" . $codneg . "'";
		$datareg = "'" . $datareg . "'";
	
		if ($this->inserisciRegistrazione($db, $utility, $descreg, 'null', $datareg, 'null', $causale, 'null', 'null', $codneg, $stareg, 'null')) {

			foreach($dettagliInseriti as $dettaglio) {
	
				$numEle = 1;
				foreach($dettaglio as $ele) {
						
					switch ($numEle) {
						case 1:
							$conto = substr($ele, 0, 3);
							$sottoConto = substr($ele, 3);
							break;
						case 2:
							$importo = $ele;
							break;
						case 3:
							$d_a = $ele;
							break;
					}
					$numEle ++;	
				}
				if (!$this->inserisciDettaglioRegistrazione($db, $utility, $_SESSION['idRegistrazione'], $conto, $sottoConto, $importo, $d_a)) {
					$db->rollbackTransaction();
					error_log("Errore inserimento dettaglio registrazione, eseguito Rollback");
					return FALSE;
				}
			}
	
			/**
			 * Rigenerazione dei saldi
			 */
			$array = $utility->getConfig();
				
			if ($array['lavoriPianificatiAttivati'] == "Si") {
				$this->rigenerazioneSaldi($db, $utility, strtotime(str_replace('/', '-', str_replace("'", "", $datareg))));
			}	
			return TRUE;
		}
		$db->rollbackTransaction();
		error_log("Errore inserimento registrazione, eseguito Rollback");
		return FALSE;
	}
	
}

?>
