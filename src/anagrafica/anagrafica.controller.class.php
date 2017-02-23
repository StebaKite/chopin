<?php

require_once 'fornitore.class.php';

class AnagraficaController {

	// Oggetti
	
	const FORNITORE = "Obj_fornitore";
	
	public static $anagraficaFunction;
	
	public function __construct(AnagraficaBusinessInterface $anagraficaFunction) {
		$this->anagraficaFunction = $anagraficaFunction;
	}
	
	public function start() {
		
		if (isset($_REQUEST["codfornitore"])) {

			$fornitore = Fornitore::getInstance();
			
			$fornitore->set_des_fornitore($_REQUEST["desfornitore"]);
			$fornitore->set_des_indirizzo_fornitore($_REQUEST["indfornitore"]);
			$fornitore->set_des_citta_fornitore($_REQUEST["cittafornitore"]);
			$fornitore->set_cap_fornitore($_REQUEST["capfornitore"]);
			$fornitore->set_tip_addebito($_REQUEST["tipoaddebito"]);
			$fornitore->set_num_gg_scadenza_fattura($_REQUEST["numggscadenzafattura"]);			
			
			$_SESSION[self::FORNITORE] = serialize($fornitore);
		}
		
		if (isset($_REQUEST["codcliente"])) {
			
			$_SESSION["codcliente"] = $_REQUEST["codcliente"];
			
			if (isset($_REQUEST["descliente"])) $_SESSION["descliente"] = $_REQUEST["descliente"];
			if (isset($_REQUEST["indcliente"])) $_SESSION["indcliente"] = $_REQUEST["indcliente"];
			if (isset($_REQUEST["cittacliente"])) $_SESSION["cittacliente"] = $_REQUEST["cittacliente"];
			if (isset($_REQUEST["capcliente"])) $_SESSION["capcliente"] = $_REQUEST["capcliente"];
			if (isset($_REQUEST["codpiva"])) $_SESSION["codpiva"] = $_REQUEST["codpiva"];
			if (isset($_REQUEST["codfisc"])) $_SESSION["codfisc"] = $_REQUEST["codfisc"];
			if (isset($_REQUEST["catcliente"])) $_SESSION["catcliente"] = $_REQUEST["catcliente"];
			if (isset($_REQUEST["esitoPivaCliente"])) $_SESSION["esitoPivaCliente"] = $_REQUEST["esitoPivaCliente"];
			if (isset($_REQUEST["esitoCfisCliente"])) $_SESSION["esitoCfisCliente"] = $_REQUEST["esitoCfisCliente"];
		}
		
		if ($_REQUEST["modo"] == "start") {
			$this->anagraficaFunction->start();
		}
		
		if ($_REQUEST["modo"] == "go") {
			$this->anagraficaFunction->go();				
		}
	}	
}

?>