<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';


class GeneraMastrinoContoTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface {

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::GENERA_MASTRINO_TEMPLATE])) $_SESSION[self::GENERA_MASTRINO_TEMPLATE] = serialize(new GeneraMastrinoContoTemplate());
		return unserialize($_SESSION[self::GENERA_MASTRINO_TEMPLATE]);
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$form = $this->root . $array['template'] . self::PAGINA_GENERA_MASTRINO;
		$risultato_ricerca = "";

		if ($sottoconto->getQtaRegistrazioniTrovate() > 0) {

			$risultato_ricerca =
			"<table id='mastrino' class='display' width='100%'>" .
			"	<thead>" .
			"		<tr>" .
			"			<th></th>" .
			"			<th class='dt-left'>%ml.datReg%</th>" .
			"			<th class='dt-left'>%ml.descreg%</th>" .
			"			<th class='dt-right'>%ml.dare%</th>" .
			"			<th class='dt-right'>%ml.avere%</th>" .
			"			<th class='dt-right'>%ml.saldoprogressivo%</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>";

			$totaleDare = 0;
			$totaleAvere = 0;
			$saldo = 0;
			
			foreach($sottoconto->getRegistrazioniTrovate() as $row) {

				$class = "class=''";

				if ($row[$dettaglioRegistrazione::IND_DAREAVERE] == 'D') {
					$totaleDare = $totaleDare + $row[$dettaglioRegistrazione::IMP_REGISTRAZIONE];
					$impDare = number_format(round($row[$dettaglioRegistrazione::IMP_REGISTRAZIONE],2), 2, ',', '.');
					$impAvere = "";
				}
				elseif ($row[$dettaglioRegistrazione::IND_DAREAVERE] == 'A') {
					$totaleAvere = $totaleAvere + $row[$dettaglioRegistrazione::IMP_REGISTRAZIONE];
					$impDare = "";
					$impAvere = number_format(round($row[$dettaglioRegistrazione::IMP_REGISTRAZIONE],2), 2, ',', '.');
				}

				if (trim($row[$conto::TIP_CONTO]) == "Dare") {
					$saldo = $totaleDare - $totaleAvere;						
				}
				elseif (trim($row[$conto::TIP_CONTO]) == "Avere") {
					$saldo = $totaleAvere - $totaleDare;
				}
				
				/**
				 * Evidenzia la riga se il saldo è negativo
				 */
				if ($saldo < 0) {
					$class = "dt-ko";
				}
				
				$risultato_ricerca = $risultato_ricerca .
				"<tr>" .
				"	<td>" . $row[$registrazione::DAT_REGISTRAZIONE] . "</td>" .
				"	<td>" . date("d/m/Y",strtotime($row[$registrazione::DAT_REGISTRAZIONE])) . "</td>" .
				"	<td>" . trim($row[$registrazione::DES_REGISTRAZIONE]) . "</td>" .
				"	<td class='dt-right'>" . $impDare . "</td>" .
				"	<td class='dt-right'>" . $impAvere . "</td>" .
				"	<td class='dt-right " . $class . "'>" . number_format(round($saldo,2), 2, ',', '.') . "</td>" .
				"</tr>";
			}
			
			/**
			 * Aggiunto una riga di totalizzazione per le colonna Dare e Avere
			 */
			
			$class = "dt-ok";
			$risultato_ricerca = $risultato_ricerca .
			"<tr>" .
			"	<td>999999999</td>" .
			"	<td></td>" .
			"	<td></td>" .
			"	<td class='dt-right " . $class . "'>" . number_format(round($totaleDare,2), 2, ',', '.') . "</td>" .
			"	<td class='dt-right " . $class . "'>" . number_format(round($totaleAvere,2), 2, ',', '.') . "</td>" .
			"	<td></td>" .
			"</tr>";
			
			$risultato_ricerca = $risultato_ricerca . "</tbody></table>";
			$des_conto = trim($row[$conto::DES_CONTO]);
			$cat_conto = trim($row[$conto::CAT_CONTO]);
			$des_sottoconto = trim($row[$sottoconto::DES_SOTTOCONTO]);
			
			$bottoneEstraiPdf = self::ESTRAI_PDF; 
			
		}
		else {
			$bottoneEstraiPdf = "";
		}

		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
				'%azione%' => $_SESSION[self::AZIONE],
				'%codconto%' => $sottoconto->getCodConto(),
				'%codsottoconto%' => $sottoconto->getCodSottoconto(),
				'%confermaTip%' => $_SESSION[self::TITOLO_PAGINA],
				'%datareg_da%' => $sottoconto->getDataRegistrazioneDa(),
				'%datareg_a%' => $sottoconto->getDataRegistrazioneA(),
				'%villa-selected%' => ($sottoconto->getCodNegozio() == "VIL") ? "selected" : "",
				'%brembate-selected%' => ($sottoconto->getCodNegozio() == "BRE") ? "selected" : "",
				'%trezzo-selected%' => ($sottoconto->getCodNegozio() == "TRE") ? "selected" : "",
				'%codneg_sel%' => $sottoconto->getCodNegozio(),
				'%desconto%' => $des_conto,
				'%catconto%' => $cat_conto,
				'%tipoconto%' => $conto->getTipConto(),
				'%categoria%' => $cat_conto,
				'%dessottoconto%' => $des_sottoconto,
				'%bottoneEstraiPdf%' => $bottoneEstraiPdf,
				'%risultato_ricerca%' => $risultato_ricerca,
				'%saldiInclusi%' => $sottoconto->getSaldiInclusi(),
				'%saldiInclusichecked%' => ($sottoconto->getSaldiInclusi() == "S") ? "checked" : "",
				'%saldiEsclusichecked%' => ($sottoconto->getSaldiInclusi() == "N") ? "checked" : ""
		);

		$utility = Utility::getInstance();

		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>
