<?php

require_once 'registrazione.class.php';
require_once 'causale.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';

class PrimanotaController
{
	public $primanotaFunction = null;
	private $request;

	// Oggetti

	const REGISTRAZIONE = "Obj_registrazione";
	const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";
	const CAUSALE = "Obj_causale";
	const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
	const SCADENZA_CLIENTE = "Obj_scadenzacliente";
	const FORNITORE = "Obj_fornitore";
	const CLIENTE = "Obj_cliente";

	// Metodi

	public function __construct(PrimanotaBusinessInterface $primanotaFunction) {
		$this->primanotaFunction = $primanotaFunction;
		$this->setRequest(null);
	}

	public function start() {

		if ($this->getRequest() == null) {
			if (isset($_REQUEST["modo"])) $this->setRequest($_REQUEST["modo"]);
			else $this->setRequest("start");
		}

		$registrazione = Registrazione::getInstance();
		$causale = Causale::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();

		if (isset($_REQUEST["datareg_da"])) {
			$registrazione->setDatRegistrazioneDa($_REQUEST["datareg_da"]);
			$registrazione->setDatRegistrazioneA($_REQUEST["datareg_a"]);
			$registrazione->setCodNegozioSel($_REQUEST["codneg_sel"]);
			$registrazione->setCodCausaleSel($_REQUEST["causale"]);
			$causale->setCodCausale($_REQUEST["causale"]);
		}

		if (isset($_REQUEST["importo"])) {
			$dettaglioRegistrazione->setCodConto($_REQUEST["codconto"]);
			$dettaglioRegistrazione->setImpRegistrazione($_REQUEST["importo"]);
			$dettaglioRegistrazione->setIndDareavere($_REQUEST["dareAvere"]);
		}

		if (isset($_REQUEST["codconto"])) {
			$dettaglioRegistrazione->setCodConto($_REQUEST["codconto"]);
		}

		if (isset($_REQUEST["causale_cre"])) {
			$causale->setCodCausale($_REQUEST["causale_cre"]);
		}

		if (isset($_REQUEST["causale"])) {
			$causale->setCodCausale($_REQUEST["causale"]);
		}

		if (isset($_REQUEST["datareg_cre"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg_cre"]);
			$registrazione->setDesRegistrazione($_REQUEST["descreg_cre"]);
			$registrazione->setCodCausale($_REQUEST["causale_cre"]);
			$registrazione->setCodNegozio($_REQUEST["codneg_cre"]);
			$registrazione->setDesFornitore($_REQUEST["fornitore_cre"]);
			$registrazione->setDesCliente($_REQUEST["cliente_cre"]);

			// Scadenza singola
// 			if ($_REQUEST["datascad_cre"] != "")
// 				$registrazione->setDatScadenza($_REQUEST["datascad_cre"]);
// 			else {
// 				// Multiscadenza
// 				if ($scadenzaFornitore->getQtaScadenze() > 0) {
// 					if ($registrazione->getDesFornitore() != "") {
// 						foreach ($scadenzaFornitore->getScadenze() as $unaScadenza) {
// 							$registrazione->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);
// 							break;
// 						}
// 					}
// 					elseif ($registrazione->getDesCliente() != "") {
// 						foreach ($scadenzaCliente->getScadenze() as $unaScadenza) {
// 							$registrazione->setDatScadenza($unaScadenza[ScadenzaCliente::DAT_SCADENZA]);
// 							break;
// 						}
// 					}
// 				}
// 				// non e' stata immessa una data scadenza
// 				else $registrazione->setDatScadenza("");
// 			}

			$registrazione->setNumFattura($_REQUEST["numfatt_cre"]);
			$registrazione->setStaRegistrazione("00");
			$registrazione->setIdMercato("");
		}

		if (isset($_REQUEST["desfornitore"])) {
			$registrazione->setDesFornitore($_REQUEST["desfornitore"]);
			$registrazione->setNumFattura($_REQUEST["numfatt"]);
			$registrazione->setDatRegistrazione($_REQUEST["datareg"]);
		}

		if (isset($_REQUEST["descliente"])) {
			$registrazione->setDesCliente($_REQUEST["descliente"]);
			$registrazione->setNumFattura($_REQUEST["numfatt"]);
			$registrazione->setDatRegistrazione($_REQUEST["datareg"]);
		}

		if (isset($_REQUEST["datascad_for"])) {
			$scadenzaFornitore->setDatScadenza($_REQUEST["datascad_for"]);
			$scadenzaFornitore->setImpInScadenza($_REQUEST["impscad_for"]);
		}

		if (isset($_REQUEST["datascad_cli"])) {
			$scadenzaCliente->setDatScadenza($_REQUEST["datascad_for"]);
			$scadenzaCliente->setImpInScadenza($_REQUEST["impscad_for"]);
		}

		// Serializzo in sessione gli oggetti modificati

		$_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
		$_SESSION[self::CAUSALE] = serialize($causale);
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
		$_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
		$_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);

		if ($this->getRequest() == "start") { $this->primanotaFunction->start(); }
		if ($this->getRequest() == "go") 	{ $this->primanotaFunction->go();}
	}

	public function getRequest(){
		return $this->request;
	}

	public function setRequest($request){
		$this->request = $request;
	}
}

?>