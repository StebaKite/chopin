<?php

require_once 'conto.class.php';
require_once 'sottoconto.class.php';
require_once 'causale.class.php';
require_once 'progressivoFattura.class.php';
require_once 'configurazioneCausale.class.php';

class ConfigurazioniController
{
	public $configurazioniFunction = null;
	private $request;

	// Oggetti

	const CONTO = "Obj_conto";
	const SOTTOCONTO = "Obj_sottoconto";
	const CAUSALE = "Obj_causale";
	const CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";
	const PROGRESSIVO_FATTURA = "Obj_progressivofattura";

	// Metodi

	public function __construct(ConfigurazioniBusinessInterface $configurazioniFunction) {
		$this->configurazioniFunction = $configurazioniFunction;
		$this->setRequest(null);
	}

	public function start() {

		if ($this->getRequest() == null) {
			if (isset($_REQUEST["modo"])) $this->setRequest($_REQUEST["modo"]);
			else $this->setRequest("start");
		}

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$causale = Causale::getInstance();
		$configurazioneCausale = ConfigurazioneCausale::getInstance();
		$progressivoFattura = ProgressivoFattura::getInstance();

		// Conti --------------------------------------------------
		
		if (isset($_REQUEST["codconto"])) {
			$conto->setCodConto($_REQUEST["codconto"]);
		}
		
		if (isset($_REQUEST["catconto_sel"])) {
 			$conto->setCatContoSel($_REQUEST["catconto_sel"]);
 			$conto->setTipContoSel($_REQUEST["tipconto_sel"]);
 		}
 		
 		if (isset($_REQUEST["codsottoconto"])) {
 			$sottoconto->setCodConto($_REQUEST["codconto"]);
 			$sottoconto->setCodSottoconto($_REQUEST["codsottoconto"]);
 			$sottoconto->setDesSottoconto($_REQUEST["dessottoconto"]);
 		}
 		
 		if (isset($_REQUEST["codconto_cre"])) {
 			$conto->setCodConto($_REQUEST["codconto_cre"]);
 			$conto->setDesConto($_REQUEST["desconto_cre"]);
 			$conto->setCatConto($_REQUEST["catconto_cre"]);
 			$conto->setTipConto($_REQUEST["dareavere_cre"]);
 			$conto->setIndPresenzaInBilancio($_REQUEST["indpresenza_cre"]);
 			$conto->setIndVisibilitaSottoconti($_REQUEST["indvissottoconti_cre"]);
 			$conto->setNumRigaBilancio($_REQUEST["numrigabilancio_cre"]);
 		}

 		if (isset($_REQUEST["codconto_mod"])) {
 			$conto->setCodConto($_REQUEST["codconto_mod"]);
 			$conto->setDesConto($_REQUEST["desconto_mod"]);
 			$conto->setCatConto($_REQUEST["catconto_mod"]);
 			$conto->setTipConto($_REQUEST["dareavere_mod"]);
 			$conto->setIndPresenzaInBilancio($_REQUEST["indpresenza_mod"]);
 			$conto->setIndVisibilitaSottoconti($_REQUEST["indvissottoconti_mod"]);
 			$conto->setNumRigaBilancio($_REQUEST["numrigabilancio_mod"]);
 		}

 		if (isset($_REQUEST["codconto_modgru"])) {
 			$sottoconto->setCodConto($_REQUEST["codconto_modgru"]);
 			$sottoconto->setCodSottoconto($_REQUEST["codsottoconto_modgru"]);
 			$sottoconto->setIndGruppo($_REQUEST["indgruppo_modgru"]);
 		}

 		if (isset($_REQUEST["codsottoconto_new"])) {
 			$sottoconto->setCodSottoconto($_REQUEST["codsottoconto_new"]);
 			$sottoconto->setDesSottoconto($_REQUEST["dessottoconto_new"]);
 			$sottoconto->setIndGruppo($_REQUEST["indgruppo_new"]);
 		}

 		if (isset($_REQUEST["codsottoconto_del"])) {
 			$sottoconto->setCodConto($_REQUEST["codconto_del"]);
 			$sottoconto->setCodSottoconto($_REQUEST["codsottoconto_del"]);
 		}

 		if (isset($_REQUEST["csot_mov"])) {
 			$sottoconto->setDataRegistrazioneDa($_REQUEST["dtda_mov"]);
 			$sottoconto->setDataRegistrazioneA($_REQUEST["dta_mov"]);
 			$sottoconto->setCodConto($_REQUEST["ccon_mov"]);
 			$sottoconto->setCodSottoconto($_REQUEST["csot_mov"]);
 			$sottoconto->setCodNegozio($_REQUEST["cneg_mov"]);
 			$sottoconto->setSaldiInclusi($_REQUEST["sal_mov"]);
 		}

 		if (isset($_REQUEST["datareg_da"])) {
 			$conto->setCatConto($_REQUEST["catconto"]);
 			$conto->setDesConto($_REQUEST["desconto"]);
 			$sottoconto->setDesSottoconto($_REQUEST["dessottoconto"]);
 		}

 		// Causali ---------------------------------------
 		
 		if (isset($_REQUEST["codcausale_cre"])) {
 			$causale->setCodCausale($_REQUEST["codcausale_cre"]);
 			$causale->setDesCausale($_REQUEST["descausale_cre"]);
 			$causale->setCatCausale($_REQUEST["catcausale_cre"]);
 			$configurazioneCausale->setCodCausale($_REQUEST["codcausale_cre"]);
 			$configurazioneCausale->setDesCausale($_REQUEST["descausale_cre"]);
 		}

 		if (isset($_REQUEST["codcausale_conf"])) {
 			$causale->setCodCausale($_REQUEST["codcausale_conf"]);
 			$configurazioneCausale->setCodCausale($_REQUEST["codcausale_conf"]);
 		}
 		
 		if (isset($_REQUEST["codconto_conf"])) {
 			$configurazioneCausale->setCodConto($_REQUEST["codconto_conf"]);
 		}
 		
 		if (isset($_REQUEST["codcausale_mod"])) {
 			$causale->setCodCausale($_REQUEST["codcausale_mod"]);
 			$causale->setDesCausale($_REQUEST["descausale_mod"]);
 			$causale->setCatCausale($_REQUEST["catcausale_mod"]);
 		}
 		
 		if (isset($_REQUEST["codcausale_del"])) {
 			$causale->setCodCausale($_REQUEST["codcausale_del"]);
 		}
 		
 		if (isset($_REQUEST["catcliente_mod"])) {
 			$progressivoFattura->setCatCliente($_REQUEST["catcliente_mod"]);
 			$progressivoFattura->setNegProgr($_REQUEST["codnegozio_mod"]);
 			$progressivoFattura->setNumFatturaUltimo($_REQUEST["numfatt_mod"]);
 			$progressivoFattura->setNotaTestaFattura($_REQUEST["notatesta_mod"]);
 			$progressivoFattura->setNotaPiedeFattura($_REQUEST["notapiede_mod"]);
 		}

		// Serializzo in sessione gli oggetti modificati

 		$_SESSION[self::CONTO] = serialize($conto);
 		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
 		$_SESSION[self::CAUSALE] = serialize($causale);
 		$_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
 		$_SESSION[self::PROGRESSIVO_FATTURA] = serialize($progressivoFattura);

		if ($this->getRequest() == "start") { $this->configurazioniFunction->start(); }
		if ($this->getRequest() == "go") 	{ $this->configurazioniFunction->go();}
	}

    public function getRequest(){
        return $this->request;
    }

    public function setRequest($request){
        $this->request = $request;
    }
}

?>
