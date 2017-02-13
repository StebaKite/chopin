<?php

interface RiepiloghiBusinessInterface extends Nexus6BusinessInterface {
	
	public function ricercaCosti($utility, $db, $replace);
	public function ricercaRicavi($utility, $db, $replace);
	public function ricercaAttivo($utility, $db, $replace);
	public function ricercaPassivo($utility, $db, $replace);
	public function ricercaCostiMargineContribuzione($utility, $db, $replace);
	public function ricercaRicaviMargineContribuzione($utility, $db, $replace);
	public function ricercaCostiFissi($utility, $db, $replace);
	
	public function ricercaVociAndamentoCostiNegozio($utility, $db, $replace);
	public function ricercaVociAndamentoCostiNegozioRiferimento($utility, $db, $replace);
	public function ricercaVociAndamentoRicaviNegozio($utility, $db, $replace);
	public function ricercaVociAndamentoRicaviNegozioRiferimento($utility, $db, $replace);
	
	public function makeCostiTable($array);
	public function makeRicaviTable($array);
	public function makeAttivoTable();
	public function makePassivoTable();
	public function makeTabs($risultato_costi, $risultato_ricavi, $risultato_attivo, $risultato_passivo);
	public function makeTabsAndamentoNegozi($andamentoCostiTable, $andamentoRicaviTable, $andamentoUtilePerditaTable, $andamentoMctTable);
	public function nomeTabTotali($totaleRicavi, $totaleCosti);
	public function tabellaTotali($tipoTotale, $totaleRicavi, $totaleCosti);	

	public function getImportoVoce($vociCostoRif, $desConto, $mm_registrazione);

	public function makeDeltaCosti();
	public function makeDeltaRicavi();
	public function makeAndamentoCostiTable($vociAndamento);
	public function makeAndamentoRicaviTable($vociAndamento);
	public function makeAndamentoCostiDeltaTable($vociAndamento);
	public function makeAndamentoRicaviDeltaTable($vociAndamento);
	public function makeUtilePerditaTable($totaliAcquistiMesi, $totaliRicaviMesi);
	public function makeTableMargineContribuzioneAndamentoNegozi($totaliAcquistiMesi, $totaliRicaviMesi);
}

?>