<?php

require_once 'scadenzaFornitore.class.php';

class ScadenzeController
{
	public $scadenzeFunction = null;
	private $request;

	const MODO							= "modo";
	const START							= "start";
	const DATA_SCADENZA_DA 				= "datascad_da";
	const DATA_SCADENZA_A 				= "datascad_a";
	const COD_NEGOZIO_SELEZIONATO		= "codneg_sel";
	const STATO_SCADENZA_SELEZIONATO	= "statoscad_sel";

	// Oggetti

	const SCADENZA_FORNITORE = "Obj_scadenzafornitore";

	// Metodi

	public function __construct(ScadenzeBusinessInterface $scadenzeFunction) {
		$this->scadenzeFunction = $scadenzeFunction;
		$this->setRequest(null);
	}

	public function start() {

		if ($this->getRequest() == null) {
			if (isset($_REQUEST[self::MODO])) $this->setRequest($_REQUEST[self::MODO]);
			else $this->setRequest(self::START);
		}

		$scadenzaFornitore = ScadenzaFornitore::getInstance();

		// parametri della request

		if (isset($_REQUEST["datascad_da"])) {
			$scadenzaFornitore->setDatScadenzaDa($_REQUEST[self::DATA_SCADENZA_DA]);
			$scadenzaFornitore->setDatScadenzaA($_REQUEST[self::DATA_SCADENZA_A]);
			$scadenzaFornitore->setCodNegozioSel($_REQUEST[self::COD_NEGOZIO_SELEZIONATO]);
			$scadenzaFornitore->setStaScadenzaSel($_REQUEST[self::STATO_SCADENZA_SELEZIONATO]);
		}

		// Serializzo in sessione gli oggetti modificati

		$_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);

		if ($this->getRequest() == "start") { $this->scadenzeFunction->start(); }
		if ($this->getRequest() == "go") 	{ $this->scadenzeFunction->go();}
	}

    public function getRequest(){
        return $this->request;
    }

    public function setRequest($request){
        $this->request = $request;
    }
}
?>