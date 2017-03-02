<?php

require_once 'fornitore.class.php';
require_once 'cliente.class.php';

class AnagraficaController {

	public static $anagraficaFunction = null;

	// Oggetti

	const FORNITORE = "Obj_fornitore";
	const CLIENTE = "Obj_cliente";

	// Metodi

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

			$cliente = Cliente::getInstance();
			$cliente->setCodCliente($_REQUEST["codcliente"]);
			$cliente->setDesCliente($_REQUEST["descliente"]);
			$cliente->setDesIndirizzoCliente($_REQUEST["indcliente"]);
			$cliente->setDesCittaCliente($_REQUEST["cittacliente"]);
			$cliente->setCapCliente($_REQUEST["capcliente"]);
			$cliente->setCodPiva($_REQUEST["codpiva"]);
			$cliente->setCodFisc($_REQUEST["codfisc"]);
			$cliente->setCatCliente($_REQUEST["catcliente"]);
			$cliente->setEsitoPivaCliente($_REQUEST["esitoPivaCliente"]);
			$cliente->setEsitoCfisCliente($_REQUEST["esitoCfisCliente"]);

			$_SESSION[self::CLIENTE] = serialize($cliente);
		}

		if (isset($_REQUEST["codfisc"])) {
		
			$cliente = Cliente::getInstance();
			$cliente->setCodFisc($_REQUEST["codfisc"]);
			$_SESSION[self::CLIENTE] = serialize($cliente);
		}

		if (isset($_REQUEST["codpiva"])) {
		
			$cliente = Cliente::getInstance();
			$cliente->setCodPiva($_REQUEST["codpiva"]);
			$cliente->setDesCliente($_REQUEST["descliente"]);
			$_SESSION[self::CLIENTE] = serialize($cliente);
		}
		
		if ($_REQUEST["modo"] == "start") { $this->anagraficaFunction->start(); }
		if ($_REQUEST["modo"] == "go") { $this->anagraficaFunction->go();}
	}
}

?>
