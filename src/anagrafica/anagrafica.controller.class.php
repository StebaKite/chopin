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

		$fornitore = Fornitore::getInstance();
		$cliente = Cliente::getInstance();
		
		if (isset($_REQUEST["codfornitore"])) {
			$fornitore->setDesFornitore($_REQUEST["desfornitore"]);
			$fornitore->setDesIndirizzoFornitore($_REQUEST["indfornitore"]);
			$fornitore->setDesCittaFornitore($_REQUEST["cittafornitore"]);
			$fornitore->setCapFornitore($_REQUEST["capfornitore"]);
			$fornitore->setTipAddebito($_REQUEST["tipoaddebito"]);
			$fornitore->setNumGgScadenzaFattura($_REQUEST["numggscadenzafattura"]);
		}

		if (isset($_REQUEST["codcliente"])) {
			$cliente->setCodCliente($_REQUEST["codcliente"]);
			$cliente->setDesCliente($_REQUEST["descliente"]);
			$cliente->setDesIndirizzoCliente($_REQUEST["indcliente"]);
			$cliente->setDesCittaCliente($_REQUEST["cittacliente"]);
			$cliente->setCapCliente($_REQUEST["capcliente"]);
			$cliente->setTipAddebito($_REQUEST["tipoaddebito"]);
			$cliente->setCodPiva($_REQUEST["codpiva"]);
			$cliente->setCodFisc($_REQUEST["codfisc"]);
			$cliente->setCatCliente($_REQUEST["catcliente"]);
			$cliente->setEsitoPivaCliente($_REQUEST["esitoPivaCliente"]);
			$cliente->setEsitoCfisCliente($_REQUEST["esitoCfisCliente"]);
		}

		if (isset($_REQUEST["codfisc"])) {
			$cliente->setCodFisc($_REQUEST["codfisc"]);
		}

		if (isset($_REQUEST["codpiva"])) {
			$cliente->setCodPiva($_REQUEST["codpiva"]);
			$cliente->setDesCliente($_REQUEST["descliente"]);
		}

		if (isset($_REQUEST["idcliente"])) {
			$cliente->setIdCliente($_REQUEST["idcliente"]);
		}

		if (isset($_REQUEST["idfornitore"])) {
			$fornitore->setIdFornitore($_REQUEST["idfornitore"]);
		}
		
		$_SESSION[self::FORNITORE] = serialize($fornitore);
		$_SESSION[self::CLIENTE] = serialize($cliente);
		
		if ($_REQUEST["modo"] == "start") { $this->anagraficaFunction->start(); }
		if ($_REQUEST["modo"] == "go") { $this->anagraficaFunction->go();}
	}
}

?>
