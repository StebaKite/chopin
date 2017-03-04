<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.presentation.interface.php';
require_once 'utility.class.php';
require_once 'fornitore.class.php';

class RicercaFornitoreTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {
	
	function __construct() {
		
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance() {
		if (!isset($_SESSION[self::RICERCA_FORNITORE_TEMPLATE])) $_SESSION[self::RICERCA_FORNITORE_TEMPLATE] = serialize(new RicercaFornitoreTemplate());
		return unserialize($_SESSION[self::RICERCA_FORNITORE_TEMPLATE]);
	}

	// template ------------------------------------------------

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {
		
		// Template --------------------------------------------------------------
		
		$fornitore = Fornitore::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_FORNITORE;
		$risultato_ricerca = "";
		
		if ($fornitore->getQtaFornitori() > 0) {
		
			$risultato_ricerca =
			"	<thead>" .
			"		<tr>" .
			"			<th>%ml.codfornitore%</th>" .
			"			<th>%ml.desfornitore%</th>" .
			"			<th>%ml.desindirizzofornitore%</th>" .
			"			<th>%ml.descittafornitore%</th>" .
			"			<th>%ml.capfornitore%</th>" .
			"			<th>%ml.tipaddebito%</th>" .
			"			<th>%ml.numggscafatt%</th>" .
			"			<th>%ml.qtareg%</th>" .
			"			<th>&nbsp;</th>" .
			"			<th>&nbsp;</th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody>" ;
		
			foreach($fornitore->getFornitori() as $row) {
		
				if ($row[self::QTA_REGISTRAZIONI_FORNITORE] == 0) {
					$class = "class=''";
				}
				else {
					$class = "class=''";
				}
		
				if ($row[self::QTA_REGISTRAZIONI_FORNITORE] == 0) {
					$bottoneModifica = self::MODIFICA_FORNITORE_HREF . trim($row['id_fornitore']) . self::MODIFICA_FORNITORE_ICON;
					$bottoneCancella = self::CANCELLA_FORNITORE_HREF . trim($row['id_fornitore']) . "," . trim($row['cod_fornitore']) . self::CANCELLA_FORNITORE_ICON;
				}
				else {
					$bottoneModifica = self::MODIFICA_FORNITORE_HREF . trim($row['id_fornitore']) . self::MODIFICA_FORNITORE_ICON;
					$bottoneCancella = "&nbsp;";
				}
		
				$risultato_ricerca .= 
				"<tr>" .
				"	<td>" . trim($row[$fornitore::COD_FORNITORE]) . "</td>" .
				"	<td>" . trim($row[$fornitore::DES_FORNITORE]) . "</td>" .
				"	<td>" . trim($row[$fornitore::DES_INDIRIZZO_FORNITORE]) . "</td>" .
				"	<td>" . trim($row[$fornitore::DES_CITTA_FORNITORE]) . "</td>" .				
				"	<td>" . trim($row[$fornitore::CAP_FORNITORE]) . "</td>" .
 				"	<td>" . trim($row[$fornitore::TIP_ADDEBITO]) . "</td>" .
 				"	<td>" . trim($row[$fornitore::NUM_GG_SCADENZA_FATTURA]) . "</td>" .
 				"	<td>" . trim($row[$fornitore::QTA_REGISTRAZIONI_FORNITORE]) . "</td>" .
 				"	<td id='icons'>" . $bottoneModifica . "</td>" .
 				"	<td id='icons'>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$risultato_ricerca .= "</tbody>";
		}
		else {
		
		}
		
		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO],
				'%risultato_ricerca%' => $risultato_ricerca
		);
		
		$utility = Utility::getInstance();
		
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}

?>