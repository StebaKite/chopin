<?php

require_once 'chopin.abstract.class.php';

abstract class RiepiloghiAbstract extends ChopinAbstract {

	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryCreaRegistrazione = "/riepilogho/estraiRegistrazioniBilancio.sql";
	public static $queryCosti = "/riepiloghi/costi.sql";
	public static $queryCostiConSaldi = "/riepiloghi/costiConSaldi.sql";
	public static $queryRicavi = "/riepiloghi/ricavi.sql";
	public static $queryRicaviConSaldi = "/riepiloghi/ricaviConSaldi.sql";
	public static $queryAttivo = "/riepiloghi/attivo.sql";
	public static $queryPassivo = "/riepiloghi/passivo.sql";
	public static $queryCostiMargineContribuzione = "/riepiloghi/costiMargineContribuzione.sql";
	public static $queryCostiMargineContribuzioneConSaldi = "/riepiloghi/costiMargineContribuzioneConSaldi.sql";
	public static $queryRicaviMargineContribuzione = "/riepiloghi/ricaviMargineContribuzione.sql";
	public static $queryRicaviMargineContribuzioneConSaldi = "/riepiloghi/ricaviMargineContribuzioneConSaldi.sql";
	public static $queryCostiFissi = "/riepiloghi/costiFissi.sql";
	public static $queryCostiFissiConSaldi = "/riepiloghi/costiFissiConSaldi.sql";
	public static $queryAndamentoCostiNegozio = "/riepiloghi/andamentoCostiNegozio.sql";
	public static $queryAndamentoRicaviNegozio = "/riepiloghi/andamentoRicaviNegozio.sql";
	
	function __construct() {
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new PrimanotaAbstract();

		return self::$_instance;
	}

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	/**
	 * Questo metodo estrae i costi 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaCosti($utility, $db, $replace) {
	
		$array = $utility->getConfig();
	
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCostiConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCosti;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['costiBilancio'] = $result;
			$_SESSION['numCostiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['costiBilancio']);
			$_SESSION['numCostiTrovati'] = 0;
		}
		return $result;
	}

	/**
	 * Questo metodo estrai i ricavi 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaRicavi($utility, $db, $replace) {
	
		$array = $utility->getConfig();
	
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicaviConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicavi;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['ricaviBilancio'] = $result;
			$_SESSION['numRicaviTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['ricaviBilancio']);
			$_SESSION['numRicaviTrovati'] = 0;
		}
		return $result;
	}
	
	/**
	 * Questo metodo estrae le attività
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
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
		return $result;
	}
	
	/**
	 * Questo metodo estrae le passività
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
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
		return $result;
	}
	
	/**
	 * Questo metodo estrae i costi variabili
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaCostiMargineContribuzione($utility, $db, $replace) {
	
		$array = $utility->getConfig();
	
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCostiMargineContribuzioneConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCostiMargineContribuzione;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['costoVariabile'] = $result;
		}
		else {
			unset($_SESSION['costoVariabile']);
		}
		return $result;
	}
	
	/**
	 * Questo metodo estrae i ricavi della vendita prodotti
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaRicaviMargineContribuzione($utility, $db, $replace) {
	
		$array = $utility->getConfig();
	
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicaviMargineContribuzioneConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryRicaviMargineContribuzione;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['ricavoVenditaProdotti'] = $result;
		}
		else {
			unset($_SESSION['ricavoVenditaProdotti']);
		}
		return $result;
	}

	/**
	 * Questo metodo estrae i costi fissi
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaCostiFissi($utility, $db, $replace) {
	
		$array = $utility->getConfig();
	
		if ($_SESSION['saldiInclusi'] == "S") {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCostiFissiConSaldi;
		}
		else {
			$sqlTemplate = self::$root . $array['query'] . self::$queryCostiFissi;
		}
	
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['costoFisso'] = $result;
		}
		else {
			unset($_SESSION['costoFisso']);
		}
		return $result;
	}

	/**
	 * Questo metodo estrae un riepilogo di totali per conto in Dare per mese 
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 * @return unknown
	 */
	public function ricercaVociAndamentoCostiNegozio($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryAndamentoCostiNegozio;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['elencoVociAndamentoCostiNegozio'] = $result;
			$_SESSION['numCostiTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['elencoVociAndamentoCostiNegozio']);
			$_SESSION['numCostiTrovati'] = 0;
		}
		return $result;
	}
	
	/**
	 * Questo metodo estrai un riepilogo di totali per conto in Avere per mese
	 * @param unknown $utility
	 * @param unknown $db
	 * @param unknown $replace
	 */
	public function ricercaVociAndamentoRicaviNegozio($utility, $db, $replace) {
	
		$array = $utility->getConfig();
		$sqlTemplate = self::$root . $array['query'] . self::$queryAndamentoRicaviNegozio;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
	
		if (pg_num_rows($result) > 0) {
			$_SESSION['elencoVociAndamentoRicaviNegozio'] = $result;
			$_SESSION['numRicaviTrovati'] = pg_num_rows($result);
		}
		else {
			unset($_SESSION['elencoVociAndamentoRicaviNegozio']);
			$_SESSION['numRicaviTrovati'] = 0;
		}
		return $result;
	}
	
	/**
	 * Questo metodo crea la tabella dei costi variabili di iniettare in pagina
	 * @return string
	 */
	public function makeCostiTable() {
		
		$risultato_costi = "";
		
		if (isset($_SESSION["costiBilancio"])) {
		
			$sottocontiCostiVariabili = ($array['sottocontiCostiVariabili'] != "") ? explode(",", $array['sottocontiCostiVariabili']) : "";
				
			$risultato_costi =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$costiBilancio = $_SESSION["costiBilancio"];
			$numReg = 0;
			$totaleCosti = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
				
			$totaleCostiVariabili = 0;
				
			foreach(pg_fetch_all($costiBilancio) as $row) {
		
				$totaleSottoconto = trim($row['tot_conto']);
				$totaleCosti += $totaleSottoconto;
		
				$numReg ++;
					
				$importo = number_format($totaleSottoconto, 2, ',', '.');
		
				if (trim($row['des_conto']) != $desconto_break ) {
		
					if ($desconto_break != "") {
		
						$totconto = number_format($totaleConto, 2, ',', '.');
		
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_costi .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_costi .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
		
						$totaleConto = 0;
					}
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_costi .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
		
					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_costi .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				$totaleConto += $totaleSottoconto;
			}
		
			$totconto = number_format($totaleConto, 2, ',', '.');
		
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_costi .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_costi .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
		
			$_SESSION['numCostiTrovati'] = $numReg;
			$risultato_costi = $risultato_costi . "</tbody>";
		
			$risultato_costi = $risultato_costi . "</table></div>";
		
			/**
			 * Metto in sessione il totale costi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totaleCosti'] = $totaleCosti;
		}
		return $risultato_costi;
	}
	
	public function makeRicaviTable() {
		
		$risultato_ricavi = "";

		if (isset($_SESSION["ricaviBilancio"])) {
		
			$sottocontiRicavi = ($array['sottocontiRicavi'] != "") ? explode(",", $array['sottocontiRicavi']) : "";
		
			$risultato_ricavi =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$ricaviBilancio = $_SESSION["ricaviBilancio"];
			$numReg = 0;
			$totaleRicavi = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
		
			$totaleRicavi = 0;
				
			foreach(pg_fetch_all($ricaviBilancio) as $row) {
					
				$totaleSottoconto = trim($row['tot_conto']);
				$totaleRicavi += $totaleSottoconto;
		
				$numReg ++;
		
				$importo = number_format(abs($totaleSottoconto), 2, ',', '.');
		
				if (trim($row['des_conto']) != $desconto_break ) {
						
					if ($desconto_break != "") {
							
						$totconto = number_format(abs($totaleConto), 2, ',', '.');
							
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_ricavi .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_ricavi .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
							
						$totaleConto = 0;
					}
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_ricavi .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
		
					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {
						
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_ricavi .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				$totaleConto += $totaleSottoconto;
			}
				
			$totconto = number_format(abs($totaleConto), 2, ',', '.');
		
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_ricavi .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_ricavi .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
		
			$_SESSION['numRicaviTrovati'] = $numReg;
			$risultato_ricavi = $risultato_ricavi . "</tbody>";
		
			$risultato_ricavi = $risultato_ricavi . "</table></div>";
				
			/**
			 * Metto in sessione il totale ricavi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totaleRicavi'] = $totaleRicavi;
		}
		return $risultato_ricavi;
	}
	
	public function makeAttivoTable() {
		
		$totaleAttivo = "";

		if (isset($_SESSION["attivoBilancio"])) {
		
			$risultato_attivo =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$attivoBilancio = $_SESSION["attivoBilancio"];
			$numReg = 0;
			$totaleAttivo = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
		
			foreach(pg_fetch_all($attivoBilancio) as $row) {
					
				$totaleSottoconto = trim($row['tot_conto']);
				$totaleAttivo += $totaleSottoconto;
		
				$numReg ++;
					
				$importo = number_format(abs($totaleSottoconto), 2, ',', '.');
		
				if (trim($row['des_conto']) != $desconto_break ) {
		
					if ($desconto_break != "") {
							
						$totconto = number_format(abs($totaleConto), 2, ',', '.');
							
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_attivo .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_attivo .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
							
						$totaleConto = 0;
					}
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_attivo .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
		
					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_attivo .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				$totaleConto += $totaleSottoconto;
			}
		
			$totconto = number_format(abs($totaleConto), 2, ',', '.');
		
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_attivo .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_attivo .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
		
			$_SESSION['numAttivoTrovati'] = $numReg;
			$risultato_attivo = $risultato_attivo . "</tbody>";
		
			$risultato_attivo = $risultato_attivo . "</table></div>";
		
			/**
			 * Metto in sessione il totale saldi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totaleAttivo'] = $totaleAttivo;
		}
		return $risultato_attivo;
	}
	
	public function makePassivoTable() {
		
		$totalePassivo = "";

		if (isset($_SESSION["passivoBilancio"])) {
		
			$risultato_passivo =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='300'>%ml.desconto%</th>" .
			"		<th width='550'>%ml.dessottoconto%</th>" .
			"		<th width='100'>%ml.importo%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$passivoBilancio = $_SESSION["passivoBilancio"];
			$numReg = 0;
			$totalePassivo = 0;
			$desconto_break = "";
			$ind_visibilita_sottoconti_break = "";
			$totaleConto = 0;
		
			foreach(pg_fetch_all($passivoBilancio) as $row) {
					
				$totaleSottoconto = trim($row['tot_conto']);
				$totalePassivo += $totaleSottoconto;
		
				$numReg ++;
					
				$importo = number_format(abs($totaleSottoconto), 2, ',', '.');
		
				if (trim($row['des_conto']) != $desconto_break ) {
		
					if ($desconto_break != "") {
							
						$totconto = number_format(abs($totaleConto), 2, ',', '.');
							
						if ($ind_visibilita_sottoconti_break == 'S') {
							$risultato_passivo .=
							"<tr>" .
							"	<td class='mark' colspan='2' align='right'></td>" .
							"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
						else {
							$risultato_passivo .=
							"<tr>" .
							"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
							"	<td class='enlarge' width='558' align='left'></td>" .
							"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
							"</tr>";
						}
							
						$totaleConto = 0;
					}
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_passivo .=
						"<tr>" .
						"	<td class='enlarge' width='308' align='left'>" . trim($row['des_conto']) . "</td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
		
					$desconto_break = trim($row['des_conto']);
					$ind_visibilita_sottoconti_break = $row['ind_visibilita_sottoconti'];
				}
				else {
		
					if ($row['ind_visibilita_sottoconti'] == 'S') {
						$risultato_passivo .=
						"<tr>" .
						"	<td width='308' align='left'></td>" .
						"	<td width='558' align='left'>" . trim($row['des_sottoconto']) . "</td>" .
						"	<td width='108' align='right'>&euro; " . $importo . "</td>" .
						"</tr>";
					}
				}
				$totaleConto += $totaleSottoconto;
			}
		
			$totconto = number_format(abs($totaleConto), 2, ',', '.');
		
			if ($ind_visibilita_sottoconti_break == 'S') {
				$risultato_passivo .=
				"<tr>" .
				"	<td class='mark' colspan='2' align='right'></td>" .
				"	<td class='mark' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
			else {
				$risultato_passivo .=
				"<tr>" .
				"	<td class='enlarge' width='308' align='left'>" . $desconto_break . "</td>" .
				"	<td class='enlarge' width='558' align='left'></td>" .
				"	<td class='enlarge' width='108' align='right'>&euro; " . $totconto . "</td>" .
				"</tr>";
			}
		
			$_SESSION['numPassivoTrovati'] = $numReg;
			$risultato_passivo = $risultato_passivo . "</tbody>";
		
			$risultato_passivo = $risultato_passivo . "</table></div>";
		
			/**
			 * Metto in sessione il totale saldi perchè servirà all'estrazione in PDF per stampare la tabella dei totali
			 */
			$_SESSION['totalePassivo'] = $totalePassivo;
		}
		return $risultato_passivo;
	}	
	
	public function makeTabs($risultato_costi, $risultato_ricavi, $risultato_attivo, $risultato_passivo) {

		$tabs = "";
		
		if (($risultato_costi != "") || ($risultato_ricavi != "") || ($risultato_attivo != "") || ($risultato_passivo != "")) {
				
			/**
			 * Annotazione provvisoria per 2015, con il 2016 puoi buttarla via con la tab-6
			 */
			$nota = "<p>Il bilancio di esercizio, <b>per il 2015</b>, viene generato partendo dal primo saldo disponibile: il <b>01/07/2015</b><br> " .
					"<p>La funzione preleva un parametro dal config 'primoSaldoDisponibile = 01/07/2015' , in situazioni normali questo parametro non è " .
					"valorizzato consentendo alla funzione il prelievo del primo saldo dell'anno al 01/01/2015</p>" .
					"<p>Il bilancio periodico invece è funzionante e può essere estratto sempre tenendo presente la data del primo saldo o le " .
					"eventuali successive.</p>";
				
			$costoVariabile = $_SESSION['costoVariabile'];
			$ricavoVendita  = $_SESSION['ricavoVenditaProdotti'];
			$costoFisso     = $_SESSION['costoFisso'];
				
			foreach(pg_fetch_all($costoVariabile) as $row) {
				$totaleCostiVariabili = trim($row['totalecostovariabile']);
			}
		
			foreach(pg_fetch_all($ricavoVendita) as $row) {
				$totaleRicavi = trim($row['totalericavovendita']);
			}
		
			foreach(pg_fetch_all($costoFisso) as $row) {
				$totaleCostiFissi = trim($row['totalecostofisso']);
			}
		
			/**
			 * Calcolo del Break Eaven Point
			 *
			 * Il calcolo del BEP per un’azienda che realizza prodotti
			 * si ottiene imponendo l’eguaglianza fra il fatturato totale e i costi totali ovvero
			 *
			 *             Fatturato totale = Costi totali
			 *
			 * Metodo analitico: scrivendo le formule1 che esprimono i costi totali ed i ricavi,
			 * con qualche passaggio matematico è possibile determinare che si intersecano se:
			 *
			 *              BEP = CF / (1 – (CV / FAT))
			 *
			 * Dove:
			 *
			 * FAT è il fatturato
			 * CF sono i costi fissi
			 * CV sono i costi variabili e quindi CV/FAT è l’incidenza dei costi variabili sul fatturato
			 * CT sono i costi totali e quindi CT = CF + CV
			 *
			 */
		
			$margineTotale = abs($totaleRicavi) - $totaleCostiVariabili;
			$marginePercentuale = ($margineTotale * 100 ) / abs($totaleRicavi);
				
			$incidenzaCostiVariabiliSulFatturato = 1 - ($totaleCostiVariabili / abs($totaleRicavi));
			$bep = $totaleCostiFissi / round($incidenzaCostiVariabiliSulFatturato, 2);
				
			$margineContribuzione =
			"<table class='result'>" .
			"	<tbody>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Fatturato</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Costi variabili</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabili), 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Margine totale</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format($margineTotale, 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Margine percentuale</td>" .
			"			<td width='108' align='right' class='mark'>" . number_format($marginePercentuale, 2, ',', '.') . " &#37;</td>" .
			"		</tr>" .
			"   </tbody>" .
			"</table>" ;
				
			$tabellaBep =
			"<table class='result'>" .
			"	<tbody>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Fatturato</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Costi fissi</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiFissi), 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Costi variabili</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCostiVariabili), 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>Incidenza costi variabili sul fatturato</td>" .
			"			<td width='108' align='right' class='mark'> " . number_format($incidenzaCostiVariabiliSulFatturato, 2, ',', '.') . "</td>" .
			"		</tr>" .
			"		<tr height='30'>" .
			"			<td width='308' align='left' class='mark'>BEP</td>" .
			"			<td width='108' align='right' class='mark'>&euro; " . number_format($bep, 2, ',', '.') . "</td>" .
			"		</tr>" .
			"   </tbody>" .
			"</table>" ;


			$notaMdc =
			"<p>Si definisce margine di contribuzione unitario la differenza tra il prezzo di vendita unitario ed il costo variabile unitario.</p>" .
			"<p>Quando il margine di contribuzione del periodo è uguale al totale dei costi fissi del periodo si raggiunge il punto di pareggio.</p>" .
			"<p>Quando il margine di contribuzione è maggiore dei costi fissi si genera l'utile.</p>" .
			"<p>Il concetto di margine di contribuzione può essere utilizzato per una riclassificazione del conto economico utile a valutare l'effetto sul reddito di variazioni del volume di vendita o del fatturato. Tale riclassificazione si ottiene deducendo dai ricavi i costi variabili.</p>" .
			"<p><strong>Ricavi - costi variabili= margine di contribuzione lordo di primo livello</strong></p><br>" ;
							
			$notaBep =
			"<p>Il calcolo del BEP per un’azienda che realizza prodotti si ottiene imponendo l’eguaglianza fra il fatturato totale e i costi totali ovvero : " .
			"<strong>Fatturato totale = Costi totali</strong></p>" .
			"<p>Metodo analitico: scrivendo le formule1 che esprimono i costi totali ed i ricavi, con qualche passaggio matematico è possibile determinare che si intersecano se: </p>" .
			"<p><strong>BEP = CF / (1 – (CV / FAT))</strong></p>" .
			"<ul>" .
			"<li>FAT è il fatturato</li>" .
			"<li>CF sono i costi fissi</li>" .
			"<li>CV sono i costi variabili e quindi CV/FAT è l’incidenza dei costi variabili sul fatturato</li>" .
			"<li>CT sono i costi totali e quindi CT = CF + CV</li>" .
			"</ul><br>" ;
				
			$tabs  = "	<div class='tabs'>";
			$tabs .= "		<ul>";
				
			if ($risultato_costi != "")   { $tabs .= "<li><a href='#tabs-1'>Costi</a></li>"; }
			if ($risultato_ricavi != "")  { $tabs .= "<li><a href='#tabs-2'>Ricavi</a></li>"; }
			if ($risultato_attivo != "")  { $tabs .= "<li><a href='#tabs-3'>Attivo</a></li>"; }
			if ($risultato_passivo != "") { $tabs .= "<li><a href='#tabs-4'>Passivo</a></li>"; }
				
			$tabs .= "<li><a href='#tabs-5'>" . strtoupper($this->nomeTabTotali(abs($totaleRicavi), abs($totaleCosti))) . "</a></li>";
			$tabs .= "<li><a href='#tabs-6'>MCT</a></li>";
			$tabs .= "<li><a href='#tabs-7'>BEP</a></li>";
			$tabs .= "<li><a href='#tabs-8'>Nota importante</a></li>";
			$tabs .= "</ul>";
				
			if ($risultato_costi != "")   { $tabs .= "<div id='tabs-1'>" . $risultato_costi . "</div>"; }
			if ($risultato_ricavi != "")  { $tabs .= "<div id='tabs-2'>" . $risultato_ricavi . "</div>"; }
			if ($risultato_attivo != "")  { $tabs .= "<div id='tabs-3'>" . $risultato_attivo . "</div>"; }
			if ($risultato_passivo != "") { $tabs .= "<div id='tabs-4'>" . $risultato_passivo . "</div>"; }
				
			$tabs .= "<div id='tabs-5'>" . $this->tabellaTotali($this->nomeTabTotali(abs($totaleRicavi), abs($totaleCosti)), abs($totaleRicavi), abs($totaleCosti)) . "</div>";
			$tabs .= "<div id='tabs-6'>" . $notaMdc . $margineContribuzione . "</div>";
			$tabs .= "<div id='tabs-7'>" . $notaBep . $tabellaBep . "</div>";
			$tabs .= "<div id='tabs-8'>" . $nota . "</div>";
			$tabs .= "</div>";
		}
		return $tabs;
	}

	public function makeTabsAndamentoNegozi($andamentoCostiTable, $andamentoRicaviTable) {

		$tabs = "";
		
		if (($andamentoCostiTable != "") || ($andamentoRicaviTable != "")) {
		
			$tabs  = "	<div class='tabs'>";
			$tabs .= "		<ul>";
		
			if ($andamentoCostiTable != "")   { $tabs .= "<li><a href='#tabs-1'>Costi</a></li>"; }
			if ($andamentoRicaviTable != "")  { $tabs .= "<li><a href='#tabs-2'>Ricavi</a></li>"; }
		
			$tabs .= "		</ul>";
		
			if ($andamentoCostiTable != "")   { $tabs .= "<div id='tabs-1'>" . $andamentoCostiTable . "</div>"; }
			if ($andamentoRicaviTable != "")  { $tabs .= "<div id='tabs-2'>" . $andamentoRicaviTable . "</div>"; }
		
			$tabs .= "</div>";
		}
		return $tabs;
	}
	
	public function nomeTabTotali($totaleRicavi, $totaleCosti) {
	
		if ($totaleRicavi > $totaleCosti) {
			$nomeTabTotali = "Utile";
		}
		elseif ($totaleRicavi < $totaleCosti) {
			$nomeTabTotali = "Perdita";
		}
		else {
			$nomeTabTotali = "Pareggio";
		}
		return $nomeTabTotali;
	}
	
	public function tabellaTotali($tipoTotale, $totaleRicavi, $totaleCosti) {
	
		if ($tipoTotale == "Utile") {
				
			$risultato_esercizio = "<table class='result'><tbody>";
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
			"</tr>";
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
			"</tr>";
				
			$utile = $totaleRicavi - $totaleCosti;
				
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format($utile, 2, ',', '.') . "</td>" .
			"</tr>";
				
			$risultato_esercizio .= "</tbody></table>" ;
		}
		elseif ($tipoTotale == "Perdita") {
				
			$risultato_esercizio = "<table class='result'><tbody>";
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
			"</tr>";
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
			"</tr>";
	
			$perdita = $totaleRicavi - $totaleCosti;
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Perdita del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>&euro; " . number_format($perdita, 2, ',', '.') . "</td>" .
			"</tr>";
	
			$risultato_esercizio .= "</tbody></table>" ;
	
		}
		else {
				
			$risultato_esercizio = "<table class='result'><tbody>";
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Ricavi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format(abs($totaleRicavi), 2, ',', '.') . "</td>" .
			"</tr>";
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Totale Costi</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format(abs($totaleCosti), 2, ',', '.') . "</td>" .
			"</tr>";
	
			$pareggio = $totaleRicavi - $totaleCosti;
	
			$risultato_esercizio .=
			"<tr height='30'>" .
			"	<td width='308' align='left' class='mark'>Utile del Periodo</td>" .
			"	<td width='108' align='right' class='mark'>" . number_format($pareggio, 2, ',', '.') . "</td>" .
			"</tr>";
	
			$risultato_esercizio .= "</tbody></table>" ;
		}
		return $risultato_esercizio;
	}
	
	public function makeAndamentoCostiTable() {

		$risultato_andamento = "";
		
		if (isset($_SESSION["elencoVociAndamentoCostiNegozio"])) {
		
			$risultato_andamento =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='200'>%ml.desconto%</th>" .
			"		<th width='50'>%ml.gen%</th>" .
			"		<th width='50'>%ml.feb%</th>" .
			"		<th width='50'>%ml.mar%</th>" .
			"		<th width='50'>%ml.apr%</th>" .
			"		<th width='50'>%ml.mag%</th>" .
			"		<th width='50'>%ml.giu%</th>" .
			"		<th width='50'>%ml.lug%</th>" .
			"		<th width='50'>%ml.ago%</th>" .
			"		<th width='50'>%ml.set%</th>" .
			"		<th width='50'>%ml.ott%</th>" .
			"		<th width='50'>%ml.nov%</th>" .
			"		<th width='50'>%ml.dic%</th>" .
			"		<th width='50'>%ml.totale%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
		
			$vociAndamento = $_SESSION["elencoVociAndamentoCostiNegozio"];
			$desconto_break = "";
			$totaliMesi = array(0,0,0,0,0,0,0,0,0,0,0,0);		// dodici mesi
			$totaliComplessiviMesi = array(0,0,0,0,0,0,0,0,0,0,0,0);		// dodici mesi
					
			foreach(pg_fetch_all($vociAndamento) as $row) {

				$totconto = $row['tot_conto'];
				
				if (trim($row['des_conto']) != $desconto_break ) {
						
					if ($desconto_break != "") {
						
						/**
						 * A rottura creo le colonne accumulate e inizializzo l'array
						 */
						$totale_conto = 0;
						
						for ($i = 1; $i < 13; $i++) {
							if ($totaliMesi[$i] == 0) $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
							else $risultato_andamento .= "<td width='58' align='right'>" . number_format($totaliMesi[$i], 0, ',', '.') . "</td>";
							$totale_conto = $totale_conto + $totaliMesi[$i];
						}
						$risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totale_conto, 0, ',', '.') . "</td>";
						
						$risultato_andamento .= "</tr>";
						for ($i = 1; $i < 13; $i++) {$totaliMesi[$i] = 0;}
						
						$risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
						$totaliMesi[$row['mm_registrazione']] = $totconto;
						$totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;						
					}
					else {						
						$risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
						$totaliMesi[$row['mm_registrazione']] = $totconto;
						$totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
					}		
					$desconto_break = trim($row['des_conto']);						
				}
				else {
					$totaliMesi[$row['mm_registrazione']] = $totconto;
					$totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;
				}				
			}
			
			/**
			 * Ultima riga
			 */

			$totale_conto = 0;
				
			for ($i = 1; $i < 13; $i++) {
				if ($totaliMesi[$i] == 0) $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
				else $risultato_andamento .= "<td width='58' align='right'>" . number_format($totaliMesi[$i], 0, ',', '.') . "</td>";
				$totale_conto = $totale_conto + $totaliMesi[$i];
			}
			$risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totale_conto, 0, ',', '.') . "</td>";

			$risultato_andamento .= "</tr>";
			$risultato_andamento .= "<tr><td class='enlarge' width='208' align='left'>%ml.totale%</td>";
				
			/**
			 * Totali mensili finali
			 */
			
			for ($i = 1; $i < 13; $i++) {
				if ($totaliComplessiviMesi[$i] == 0) $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
				else $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totaliComplessiviMesi[$i], 0, ',', '.') . "</td>";
				$totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
			}
			$risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format($totale_anno, 0, ',', '.') . "</td>";			
			$risultato_andamento .= "</tr></tbody></table></div>";			
		}				
		return $risultato_andamento;
	}	

	public function makeAndamentoRicaviTable() {
	
		$risultato_andamento = "";
	
		if (isset($_SESSION["elencoVociAndamentoRicaviNegozio"])) {
	
			$risultato_andamento =
			"<table class='result'>" .
			"	<thead>" .
			"		<th width='200'>%ml.desconto%</th>" .
			"		<th width='50'>%ml.gen%</th>" .
			"		<th width='50'>%ml.feb%</th>" .
			"		<th width='50'>%ml.mar%</th>" .
			"		<th width='50'>%ml.apr%</th>" .
			"		<th width='50'>%ml.mag%</th>" .
			"		<th width='50'>%ml.giu%</th>" .
			"		<th width='50'>%ml.lug%</th>" .
			"		<th width='50'>%ml.ago%</th>" .
			"		<th width='50'>%ml.set%</th>" .
			"		<th width='50'>%ml.ott%</th>" .
			"		<th width='50'>%ml.nov%</th>" .
			"		<th width='50'>%ml.dic%</th>" .
			"		<th width='50'>%ml.totale%</th>" .
			"	</thead>" .
			"</table>" .
			"<div class='scroll-bilancio'>" .
			"	<table class='result'>" .
			"		<tbody>";
	
			$vociAndamento = $_SESSION["elencoVociAndamentoRicaviNegozio"];
			$desconto_break = "";
			$totaliMesi = array(0,0,0,0,0,0,0,0,0,0,0,0);		// dodici mesi
			$totaliComplessiviMesi = array(0,0,0,0,0,0,0,0,0,0,0,0);		// dodici mesi
					
			foreach(pg_fetch_all($vociAndamento) as $row) {
	
				$totconto = $row['tot_conto'];
	
				if (trim($row['des_conto']) != $desconto_break ) {
	
					if ($desconto_break != "") {
	
						/**
						 * A rottura creo le colonne accumulate e inizializzo l'array
						 */
						$totale_conto = 0;
	
						for ($i = 1; $i < 13; $i++) {
							if ($totaliMesi[$i] == 0) $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
							else $risultato_andamento .= "<td width='58' align='right'>" . number_format(abs($totaliMesi[$i]), 0, ',', '.') . "</td>";
							$totale_conto = $totale_conto + $totaliMesi[$i];
						}
						$risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totale_conto), 0, ',', '.') . "</td>";
	
						$risultato_andamento .= "</tr>";
						for ($i = 1; $i < 13; $i++) {$totaliMesi[$i] = 0;}
	
						$risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
						$totaliMesi[$row['mm_registrazione']] = $totconto;
						$totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;						
					}
					else {
						$risultato_andamento .= "<tr><td width='208' align='left'>" . trim($row['des_conto']) . "</td>";
						$totaliMesi[$row['mm_registrazione']] = $totconto;
						$totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;						
					}
					$desconto_break = trim($row['des_conto']);
				}
				else {
					$totaliMesi[$row['mm_registrazione']] = $totconto;
					$totaliComplessiviMesi[$row['mm_registrazione']] += $totconto;						
				}
			}
			
			/**
			 * Ultima riga
			 */
			 
			$totale_conto = 0;
			
			for ($i = 1; $i < 13; $i++) {
				if ($totaliMesi[$i] == 0) $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
				else $risultato_andamento .= "<td width='58' align='right'>" . number_format(abs($totaliMesi[$i]), 0, ',', '.') . "</td>";
				$totale_conto = $totale_conto + $totaliMesi[$i];
			}
			$risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totale_conto), 0, ',', '.') . "</td>";
				
			$risultato_andamento .= "</tr>";
			$risultato_andamento .= "<tr><td class='enlarge' width='208' align='left'>%ml.totale%</td>";
				
			/**
			 * Totali mensili finali
			 */
			
			for ($i = 1; $i < 13; $i++) {
				if ($totaliComplessiviMesi[$i] == 0) $risultato_andamento .= "<td width='58' align='right'>&ndash;&ndash;&ndash;</td>";
				else $risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totaliComplessiviMesi[$i]), 0, ',', '.') . "</td>";
				$totale_anno = $totale_anno + $totaliComplessiviMesi[$i];
			}
			$risultato_andamento .= "<td class='mark' width='58' align='right'>" . number_format(abs($totale_anno), 0, ',', '.') . "</td>";			
			$risultato_andamento .= "</tr></tbody></table></div>";			
		}
		return $risultato_andamento;
	}
}		

?>
		
	