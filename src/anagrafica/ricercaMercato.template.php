<?php

require_once 'anagrafica.abstract.class.php';
require_once 'anagrafica.presentation.interface.php';
require_once 'utility.class.php';
require_once 'mercato.class.php';

class RicercaMercatoTemplate extends AnagraficaAbstract implements AnagraficaPresentationInterface {

	function __construct()
	{	
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_MERCATO_TEMPLATE])) $_SESSION[self::RICERCA_MERCATO_TEMPLATE] = serialize(new RicercaMercatoTemplate());
		return unserialize($_SESSION[self::RICERCA_MERCATO_TEMPLATE]);
	}

	public function inizializzaPagina() {}

	public function controlliLogici() {}

	public function displayPagina() {
	
		$mercato = Mercato::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	
		$form = $this->root . $array['template'] . self::PAGINA_RICERCA_MERCATO;
		$risultato_ricerca = "";
	
		if ($mercato->getQtaMercati() > 0) {
	
			$risultato_ricerca =
			"<div class='row'>" .
			"    <div class='col-sm-4'>" .
			"        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
			"    </div>" .
			"    <div class='col-sm-8'>" . $_SESSION[self::MSG] . "</div>" .
			"</div>" .
			"<br/>" .
			"<table class='table table-bordered table-hover'>" .
			"	<thead>" .
			"		<tr> " .
			"			<th class='dt-left'>%ml.codmercato%</th>" .
			"			<th class='dt-left'>%ml.desmercato%</th>" .
			"			<th class='dt-left'>%ml.cittamercato%</th>" .
			"			<th class='dt-left'>%ml.negozio%</th>" .
			"			<th class='dt-left'>%ml.qtareg%</th>" .
			"			<th class='dt-left'></th>" .
			"			<th class='dt-left'></th>" .
			"		</tr>" .
			"	</thead>" .
			"	<tbody id='myTable'>";
	
			foreach($mercato->getMercati() as $row) {
	
				if ($row[$mercato::QTA_REGISTRAZIONI_MERCATO] == 0) {
					$bottoneModifica = self::MODIFICA_MERCATO_HREF . trim($row['id_mercato']) . self::MODIFICA_ICON;
					$bottoneCancella = self::CANCELLA_MERCATO_HREF . trim($row['id_mercato']) . self::CANCELLA_ICON;
				}
				else {
					$bottoneModifica = self::MODIFICA_MERCATO_HREF . trim($row['id_mercato']) . self::MODIFICA_ICON;
					$bottoneCancella = "&nbsp;";
				}
	
				$risultato_ricerca .= 
				"<tr>" .
				"	<td>" . trim($row['cod_mercato']) . "</td>" .
				"	<td>" . trim($row['des_mercato']) . "</td>" .
				"	<td>" . trim($row['citta_mercato']) . "</td>" .
				"	<td>" . trim($row['cod_negozio']) . "</td>" .
				"	<td>" . trim($row['tot_registrazioni_mercato']) . "</td>" .
				"	<td>" . $bottoneModifica . "</td>" .
				"	<td>" . $bottoneCancella . "</td>" .
				"</tr>";
			}
			$risultato_ricerca .= "</tbody></table>";
		}
	
		$replace = array(
				'%titoloPagina%' => $_SESSION[self::TITOLO],
				'%azione%' => $_SESSION[self::AZIONE_RICERCA_MERCATO],
				'%risultato_ricerca%' => $risultato_ricerca
		);
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>