<?php

require_once 'nexus6.abstract.class.php';
require_once 'scadenze.presentation.interface.php';

abstract class ScadenzeAbstract extends Nexus6Abstract implements ScadenzePresentationInterface {

//	const VISUALIZZA_REGISTRAZIONE_HREF = "<a onclick='visualizzaRegistrazione(";
	
	private static $_instance = null;

	public static $messaggio;

	// Query ---------------------------------------------------------------

	public static $queryUpdateStatoScadenzaCliente = "/scadenze/updateStatoScadenzaCliente.sql";

	// Getters e Setters ---------------------------------------------------

	public function setMessaggio($messaggio) {
		self::$messaggio = $messaggio;
	}

	// ------------------------------------------------

	public function getMessaggio() {
		return self::$messaggio;
	}

	// Metodi comuni di utilita della prima note ---------------------------

	public function leggiScadenze($db, $utility, $datascad_da, $datascad_a) {

		$array = $utility->getConfig();
		$replace = array(
				'%dat_scadenza_da%' => $datascad_da,
				'%dat_scadenza_a%' => $datascad_a
		);

		$sqlTemplate = self::$root . $array['query'] . self::$queryRicercaScadenze;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->getData($sql);
		return $result;
	}

	public function cambiaStatoScadenzaCliente($db, $utility, $idscadenza, $statoScadenza) {

		$array = $utility->getConfig();
		$replace = array(
				'%id_scadenza%' => trim($idscadenza),
				'%sta_scadenza%' => trim($statoScadenza)
		);
		$sqlTemplate = self::$root . $array['query'] . self::$queryUpdateStatoScadenzaCliente;
		$sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
		$result = $db->execSql($sql);
	}
		
	public function makeTabellaReadOnlyRegistrazioneOriginale($registrazione)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$bottoneVisualizzaRegistrazione = self::VISUALIZZA_REGISTRAZIONE_HREF . $registrazione->getIdRegistrazione() . self::VISUALIZZA_ICON;
		
		$thead =
		"<thead>" .
		"	<tr>" .
		"		<th>%ml.datReg%</th>" .
		"		<th>%ml.descreg%</th>" .
		"		<th>%ml.negozio%</th>" .
		"		<th></th>" .
		"	</tr>" .
		"</thead>";
		
		$tbody =
		"<tbody>" .
		"	<tr>" .
		"		<td>" . date("d/m/Y",strtotime($registrazione->getDatRegistrazione())) . "</td>" .
		"		<td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
		"		<td>" . trim($registrazione->getCodNegozio()) . "</td>" .
		"		<td>" . $bottoneVisualizzaRegistrazione . "</td>" .
		"	</tr>".
		"</tbody>";
		
		return $thead . $tbody;
	}
	
	public function makeTabellaReadOnlyPagamento($registrazione)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$bottoneVisualizzaRegistrazione = self::VISUALIZZA_PAGAMENTO_HREF . $registrazione->getIdRegistrazione() . self::VISUALIZZA_ICON;
		
		$thead =
		"<thead>" .
		"	<tr>" .
		"		<th>%ml.datReg%</th>" .
		"		<th>%ml.descreg%</th>" .
		"		<th>%ml.negozio%</th>" .
		"		<th></th>" .
		"	</tr>" .
		"</thead>";
		
		$tbody =
		"<tbody>" .
		"	<tr>" .
		"		<td>" . date("d/m/Y",strtotime($registrazione->getDatRegistrazione())) . "</td>" .
		"		<td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
		"		<td>" . trim($registrazione->getCodNegozio()) . "</td>" .
		"		<td>" . $bottoneVisualizzaRegistrazione . "</td>" .
		"	</tr>".
		"</tbody>";
		
		return $thead . $tbody;
	}
	
	public function makeTabellaRegistrazioneOriginale($registrazione)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$bottoneVisualizzaRegistrazione = self::MODIFICA_REGISTRAZIONE_HREF . $registrazione->getIdRegistrazione() . self::MODIFICA_ICON;
		
		$thead =
		"<thead>" .
		"	<tr>" .
		"		<th>%ml.datReg%</th>" .
		"		<th>%ml.descreg%</th>" .
		"		<th>%ml.negozio%</th>" .
		"		<th></th>" .
		"	</tr>" .
		"</thead>";
		
		$tbody =
		"<tbody>" .
		"	<tr>" .
		"		<td>" . date("d/m/Y",strtotime($registrazione->getDatRegistrazione())) . "</td>" .
		"		<td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
		"		<td>" . trim($registrazione->getCodNegozio()) . "</td>" .
		"		<td>" . $bottoneVisualizzaRegistrazione . "</td>" .
		"	</tr>".
		"</tbody>";
		
		return $thead . $tbody;
	}
	
	public function makeTabellaPagamento($registrazione)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$bottoneVisualizzaRegistrazione = self::MODIFICA_PAGAMENTO_HREF . $registrazione->getIdRegistrazione() . self::MODIFICA_ICON;
		
		$thead =
		"<thead>" .
		"	<tr>" .
		"		<th>%ml.datReg%</th>" .
		"		<th>%ml.descreg%</th>" .
		"		<th>%ml.negozio%</th>" .
		"		<th></th>" .
		"	</tr>" .
		"</thead>";
		
		$tbody =
		"<tbody>" .
		"	<tr>" .
		"		<td>" . date("d/m/Y",strtotime($registrazione->getDatRegistrazione())) . "</td>" .
		"		<td>" . trim($registrazione->getDesRegistrazione()) . "</td>" .
		"		<td>" . trim($registrazione->getCodNegozio()) . "</td>" .
		"		<td>" . $bottoneVisualizzaRegistrazione . "</td>" .
		"	</tr>".
		"</tbody>";
		
		return $thead . $tbody;
	}	
}

?>