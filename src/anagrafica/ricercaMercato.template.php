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
			"	<tbody>";
	
			foreach($mercato->getMercati() as $row) {
	
				if ($row[$mercato::QTA_REGISTRAZIONI_MERCATO] == 0) {
					$parms = trim($row['id_mercato']) . "#" . trim($row['cod_mercato']) . "#" . str_replace("'", "@", trim($row['des_mercato'])) . "#" . str_replace("'", "@", trim($row['citta_mercato'])) . "#" . trim($row['cod_negozio']);
					$bottoneModifica = "<a class='tooltip' onclick='modificaMercato(" . '"' . $parms . '"' . ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "<a class='tooltip' onclick='cancellaMercato(" . trim($row['id_mercato']) . "," . trim($row['cod_mercato']) . ")'><li class='ui-state-default ui-corner-all' title='%ml.cancella%'><span class='ui-icon ui-icon-trash'></span></li></a>";
				}
				else {
					$parms = trim($row['id_mercato']) . "#" . trim($row['cod_mercato']) . "#" . str_replace("'", "@", trim($row['des_mercato'])) . "#" . str_replace("'", "@", trim($row['citta_mercato'])) . "#" . trim($row['cod_negozio']);
					$bottoneModifica = "<a class='tooltip' onclick='modificaMercato(" . '"' . $parms . '"' . ")'><li class='ui-state-default ui-corner-all' title='%ml.modifica%'><span class='ui-icon ui-icon-pencil'></span></li></a>";
					$bottoneCancella = "&nbsp;";
				}
	
				$risultato_ricerca .= 
				"<tr>" .
				"	<td>" . trim($row['cod_mercato']) . "</td>" .
				"	<td>" . trim($row['des_mercato']) . "</td>" .
				"	<td>" . trim($row['citta_mercato']) . "</td>" .
				"	<td>" . trim($row['cod_negozio']) . "</td>" .
				"	<td>" . trim($row['tot_registrazioni_mercato']) . "</td>" .
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
				'%azione%' => $_SESSION[self::AZIONE_RICERCA_MERCATO],
				'%risultato_ricerca%' => $risultato_ricerca
		);
	
		$utility = Utility::getInstance();
	
		$template = $utility->tailFile($utility->getTemplate($form), $replace);
		echo $utility->tailTemplate($template);
	}
}
	
?>