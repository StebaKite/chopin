<?php

interface Nexus6BusinessInterface {
	
	public static function getInstance();
	
	public function start();
	public function go();

	public function ricercaDati($utility);
	public function preparaPagina($template);
	
	public function setTestata($testata);
	public function setPiede($piede);
	public function setAzione($azione);
	public function setTestoAzione($testoAzione);
	public function setTitoloPagina($titoloPagina);
	public function setConfermaTip($tip);
	
	public function getTestata();
	public function getPiede();
	public function getAzione();
	public function getTestoAzione();
	public function getTitoloPagina();
	public function getConfermaTip();
	
	public function makeMenu($utility) : string;
	
	public function isAnnoBisestile($anno);
	public function sommaGiorniData($data, $carattereSeparatore, $giorniDaSommare);
	public function sommaGiorniDataYMD($data, $carattereSeparatore, $giorniDaSommare);
	
	public function caricaCausali($utility, $db, $categoria);
	public function caricaFornitori($utility, $db);
	public function caricaClienti($utility, $db);
	public function caricaConti($utility, $db);
	public function caricaTuttiConti($utility, $db);
	public function caricaMercati($utility, $db);
	public function caricaMercatiNegozio($utility, $db);
	
	public function leggiIdFornitore($db, $utility, $idfornitore);
	public function leggiIdCliente($db, $utility, $idcliente);
	public function leggiDescrizioneFornitore($db, $utility, $desfornitore) : string;
	public function leggiDescrizioneCliente($db, $utility, $descliente) : string;
	public function prelevaIdFornitore($db, $utility, $idfornitore);
		
	public function rigenerazioneSaldi($db, $utility, $dataRegistrazione, $project_root);
	public function cambioStatoLavoroPianificato($db, $utility, $pklavoro, $stato);
	
	public function leggiLavoriPianificati($db, $utility);
	public function leggiLavoriPianificatiBatchMode($db, $utility, $project_root);
	public function leggiLavoriPianificatiAnnoCorrente($db, $utility);
	public function eseguiLavoriPianificati($db, $lavoriPianificati, $project_root);
	public function eseguiLavoro($db, $row, $project_root);

	public function inserisciSottoconto($db, $utility, $codconto, $codsottoconto, $dessottoconto);
	public function cancellaSottoconto($db, $utility, $codconto, $codsottoconto);
	
	public function caricaCategorieCliente($utility, $db);
	public function getEnvironment($array);
	
	public function controllaScadenzeFornitoriSuperate($utility, $db) : string;
	public function controllaScadenzeClientiSuperate($utility, $db) : string;
	public function controllaRegistrazioniInErrore($utility, $db) : string;
	
	public function leggiRegistrazione($db, $utility, $idregistrazione);
	public function cancellaRegistrazione($db, $utility, $id_registrazione);
}

?>