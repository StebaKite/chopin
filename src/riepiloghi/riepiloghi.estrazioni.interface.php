<?php

interface RiepiloghiEstrazioni extends RiepiloghiPresentation {
	
	public function generaSezioneIntestazione($pdf);
	public function generaSezioneTabellaProgressivi($pdf, $utility, $db, $elencoVoci);
	public function generaSezioneTabellaBilancio($pdf, $utility);
	public function generaSezioneTabellaBilancioEsercizio($pdf, $utility);
	public function generaSezioneTabellaCosti($pdf, $utility, $db, $totaliCostiRicavi);
	public function generaSezioneTabellaRicavi($pdf, $utility, $db, $totaliCostiRicavi);
	public function generaSezioneTabellaTotali($pdf, $utility, $db, $totaliCostiRicavi);
	public function generaSezioneTabellaAttivo($pdf, $utility, $db);
	public function generaSezioneTabellaPassivo($pdf, $utility, $db);
	public function generaSezioneTabellaMct($pdf, $utility, $db, $datiMCT);
	public function generaSezioneTabellaBep($pdf, $utility, $db, $datiMCT);
	public function get_datiMCT();
	public function set_datiMCT($datiMCT);
	public function get_totaliCostiRicavi();
	public function set_totaliCostiRicavi($totaliCostiRicavi);	
}

?>