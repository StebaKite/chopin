<?php

require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class ConfigurazioniController
{
	public static $configurazioniFunction = null;
	private $request;

	// Oggetti

	const CONTO = "Obj_conto";
	const SOTTOCONTO = "Obj_sottoconto";

	// Metodi

	public function __construct(ConfigurazioniBusinessInterface $configurazioniFunction) {
		$this->configurazioniFunction = $configurazioniFunction;
		$this->setRequest(null);
	}

	public function start() {

		if ($this->getRequest() == null) $this->setRequest($_REQUEST["modo"]);

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();

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
 		}
 		
		// Serializzo in sessione gli oggetti modificati

 		$_SESSION[self::CONTO] = serialize($conto);
 		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
 			
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
