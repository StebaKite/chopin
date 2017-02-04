<?php

require_once 'primanota.abstract.class.php';

class ImportaExcelCorrispettivoNegozioStep1 extends primanotaAbstract {

	private static $_instance = null;

	public static $azioneImportaExcelCorrispettivoNegozioStep1 = "../primanota/importaExcelCorrispettivoNegozioStep1Facade.class.php?modo=go";
	public static $azioneImportaExcelCorrispettivoNegozioStep2 = "../primanota/importaExcelCorrispettivoNegozioStep2Facade.class.php?modo=go";
	
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

			self::$_instance = new ImportaExcelCorrispettivoNegozioStep1();

			return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'importaExcelCorrispettivoNegozioStep1.template.php';
		require_once 'utility.class.php';

		$utility = Utility::getInstance();

		$importaExcelCorrispettivoNegozioStep1Template = ImportaExcelCorrispettivoNegozioStep1Template::getInstance();
		$this->preparaPaginaStep1($importaExcelCorrispettivoNegozioStep1Template);

		// Data del giorno preimpostata solo in entrata -------------------------

		$_SESSION["anno"] = date("Y");
		$_SESSION["mese"] = date("m") - 1;	// il mese è dimimuito di 1 per coincidere con i fogli del file excel
		$_SESSION["codneg"] = "VIL";
		if (!isset($_SESSION["datada"])) $_SESSION["datada"] = date("01/m/Y");
		if (!isset($_SESSION["dataa"]))  $_SESSION["dataa"] = date("d/m/Y");
		
		unset($_SESSION["file"]);
		unset($_SESSION["corrispettiviTrovati"]);
		unset($_SESSION["corrispettiviIncompleti"]);
			
		// Compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
		echo $utility->tailTemplate($template);		
		
		$importaExcelCorrispettivoNegozioStep1Template->displayPagina();
		
		if (isset($_SESSION["messaggioImportFileOk"])) {
			self::$replace = array('%messaggio%' => $_SESSION["messaggioImportFileOk"]);
			unset($_SESSION["messaggioImportFileOk"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioInfo), self::$replace);
			echo $utility->tailTemplate($template);
			}
		else {
			if (isset($_SESSION["messaggioImportFileErr"])) {
				self::$replace = array('%messaggio%' => $_SESSION["messaggioImportFileErr"]);
				unset($_SESSION["messaggioImportFileErr"]);
				$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
			}
		}		
		
		include(self::$piede);
	}

	public function go() {
		
		require_once 'importaExcelCorrispettivoNegozioStep1.template.php';
		require_once 'utility.class.php';
		require_once 'excel_reader2.php';		

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$importaExcelCorrispettivoNegozioStep1Template = ImportaExcelCorrispettivoNegozioStep1Template::getInstance();
		
		if ($importaExcelCorrispettivoNegozioStep1Template->controlliLogici()) {

			$users = shell_exec("who | cut -d' ' -f1 | sort | uniq");
				
			if (strpos($users, $array['usernameProdLogin']) === false) {
				$path = str_replace("%user%", "stefano", $array["filePath"]);
			}
			else {
				$path = str_replace("%user%", $array["usernameProdLogin"], $array["filePath"]);
			}
				
			$data=new Spreadsheet_Excel_Reader($path . "/" . $_SESSION["file"]);
			$sheets=$_SESSION["mese"];
			$mese = str_pad($_SESSION["mese"] + 1, 2, "0", STR_PAD_LEFT);

			$dataDa = strtotime(str_replace("/", "-", $_SESSION["datada"]));
			$dataA = strtotime(str_replace("/", "-", $_SESSION["dataa"]));
			
			$corrispettivi = array();
			$completi = 0;
			$incompleti = 0;
			
			// Ciclo righe
			for($i=1;$i<=$data->sheets[$sheets]['numRows'];$i++) {
					
				$corrispettivo = array();
			
				// Ciclo colonne
				for($j=1;$j<=$data->sheets[$sheets]['numCols'];$j++){
					if ($j <= 4) {
						if($data->sheets[$sheets]['cells'][$i][$j]!="") {
							if (is_numeric($data->sheets[$sheets]['cells'][$i][$j])) {
									
								$cella = $data->sheets[$sheets]['cells'][$i][$j];
								if ($j == 1) {
									$giorno = str_pad($data->sheets[$sheets]['cells'][$i][$j], 2, "0", STR_PAD_LEFT);
									$cella =   $giorno . "/" .$mese . "/" . $_SESSION["anno"];
									$datareg = strtotime(str_replace("/", "-", $cella));
								}
								if (($datareg >= $dataDa) and ($datareg <= $dataA)) {
									array_push($corrispettivo, $cella);
								}
							}
						}
					}
				}
				if (count($corrispettivo) == 4) {
					array_push($corrispettivi, $corrispettivo);
					unset($corrispettivo);
					$completi ++;
				}
				else {
					if (count($corrispettivo) > 0) $incompleti ++;					
				}
			}
			
			$_SESSION["corrispettiviTrovati"] = $corrispettivi;
			$_SESSION["corrispettiviIncompleti"] = $incompleti;
			
			if (($incompleti == 0) and ($completi > 0))  {
				$this->preparaPaginaStep2($importaExcelCorrispettivoNegozioStep1Template);
			}
			else {
				$this->preparaPaginaStep1($importaExcelCorrispettivoNegozioStep1Template);
			}
			
			// Compone la pagina
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
			
			$importaExcelCorrispettivoNegozioStep1Template->displayPagina();
			include(self::$piede);
		}
		else {
			
			$this->preparaPaginaStep1($importaExcelCorrispettivoNegozioStep1Template);
				
			// Compone la pagina
			$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
			$template = $utility->tailFile($utility->getTemplate(self::$testata), $replace);
			echo $utility->tailTemplate($template);
				
			$importaExcelCorrispettivoNegozioStep1Template->displayPagina();
			
			self::$replace = array('%messaggio%' => $_SESSION["messaggio"]);
			$template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
				
			include(self::$piede);
		}
	}

	public function preparaPaginaStep1($importaExcelCorrispettivoNegozioStep1Template) {
	
		$importaExcelCorrispettivoNegozioStep1Template->setAzione(self::$azioneImportaExcelCorrispettivoNegozioStep1);
		$importaExcelCorrispettivoNegozioStep1Template->setConfermaTip("%ml.confermaimportaExcelCorrispettivoNegozioStep1%");
		$importaExcelCorrispettivoNegozioStep1Template->setTitoloPagina("%ml.importaExcelCorrispettivoNegozioStep1%");	
	}

	public function preparaPaginaStep2($importaExcelCorrispettivoNegozioStep1Template) {
	
		$importaExcelCorrispettivoNegozioStep1Template->setAzione(self::$azioneImportaExcelCorrispettivoNegozioStep2);
		$importaExcelCorrispettivoNegozioStep1Template->setConfermaTip("%ml.confermaimportaExcelCorrispettivoNegozioStep2%");
		$importaExcelCorrispettivoNegozioStep1Template->setTitoloPagina("%ml.importaExcelCorrispettivoNegozioStep2%");
	}
}

?>
