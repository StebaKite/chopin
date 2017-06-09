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

		if (isset($_REQUEST["categoria"])) {
 			$conto->setCatConto($_REQUEST["categoria"]);
 			$conto->setTipConto($_REQUEST["tipoconto"]);
 		}

 		if (isset($_REQUEST["codconto"])) {
 			$conto->setCodConto($_REQUEST["codconto"]);
 			$conto->setDesConto($_REQUEST["desconto"]);
 			$conto->setCatConto($_REQUEST["categoria"]);
 			$conto->setTipConto($_REQUEST["dareavere"]);
 			$conto->setIndPresenzaInBilancio($_REQUEST["indpresenza"]);
 			$conto->setIndVisibilitaSottoconti($_REQUEST["indvissottoconti"]);
 			$conto->setNumRigaBilancio($_REQUEST["numrigabilancio"]);
 			$configurazioneCausale->setCodConto($_REQUEST["codconto"]);
 		}

 		if (isset($_REQUEST["codconto_mod"])) {
 			$conto->setCodConto($_REQUEST["codconto_mod"]);
 			$conto->setDesConto($_REQUEST["desconto_mod"]);
 			$conto->setCatConto($_REQUEST["categoria_mod"]);
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

 		if (isset($_REQUEST["codcontogenera"])) {
 			$sottoconto->setDataRegistrazioneDa($_REQUEST["datareg_da"]);
 			$sottoconto->setDataRegistrazioneA($_REQUEST["datareg_a"]);
 			$sottoconto->setCodConto($_REQUEST["codcontogenera"]);
 			$sottoconto->setCodSottoconto($_REQUEST["codsottocontogenera"]);
 			$sottoconto->setCodNegozio($_REQUEST["codneg_sel"]);
 			$sottoconto->setSaldiInclusi($_REQUEST["saldiInclusi"]);
 		}

 		if (isset($_REQUEST["datareg_da"])) {
 			$conto->setCatConto($_REQUEST["catconto"]);
 			$conto->setDesConto($_REQUEST["desconto"]);
 			$sottoconto->setDesSottoconto($_REQUEST["dessottoconto"]);
 		}

 		if (isset($_REQUEST["codsottoconto"])) {
 			$sottoconto->setCodSottoconto($_REQUEST["codsottoconto"]);
 			$sottoconto->setDesSottoconto($_REQUEST["dessottoconto"]);
 			$sottoconto->setCodConto($_REQUEST["codconto"]);
 			$sottoconto->setCodSottoconto($_REQUEST["codsottoconto"]);
 			$sottoconto->setIndGruppo($_REQUEST["indgruppo"]);
 		}

 		if (isset($_REQUEST["codcausale"])) {
 			$causale->setCodCausale($_REQUEST["codcausale"]);
 			$causale->setDesCausale($_REQUEST["descausale"]);
 			$causale->setCatCausale($_REQUEST["catcausale"]);
 			$configurazioneCausale->setCodCausale($_REQUEST["codcausale"]);
 			$configurazioneCausale->setDesCausale($_REQUEST["descausale"]);
 		}

 		if (isset($_REQUEST["catcliente"])) {
 			$progressivoFattura->setCatCliente($_REQUEST["catcliente"]);
 			$progressivoFattura->setNegProgr($_REQUEST["codnegozio"]);
 			$progressivoFattura->setNumFatturaUltimo($_REQUEST["numfatt"]);
 			$progressivoFattura->setNotaTestaFattura($_REQUEST["notatesta"]);
 			$progressivoFattura->setNotaPiedeFattura($_REQUEST["notapiede"]);
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
