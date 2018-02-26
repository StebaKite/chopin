<?php

require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'registrazione.class.php';

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
	const ID_PAGAMENTO					= "idPagamento";
	const ID_SCADENZA					= "idScadenza";

	// Oggetti

	const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
	const SCADENZA_CLIENTE = "Obj_scadenzacliente";
	const REGISTRAZIONE = "Obj_registrazione";

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
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$registrazione = Registrazione::getInstance();

		// parametri della request

		if (isset($_REQUEST[self::DATA_SCADENZA_DA]))
		{
			$scadenzaFornitore->setDatScadenzaDa($_REQUEST[self::DATA_SCADENZA_DA]);
			$scadenzaFornitore->setDatScadenzaA($_REQUEST[self::DATA_SCADENZA_A]);
			$scadenzaFornitore->setCodNegozioSel($_REQUEST[self::COD_NEGOZIO_SELEZIONATO]);
			$scadenzaFornitore->setStaScadenzaSel($_REQUEST[self::STATO_SCADENZA_SELEZIONATO]);

			$scadenzaCliente->setDatScadenzaDa($_REQUEST[self::DATA_SCADENZA_DA]);
			$scadenzaCliente->setDatScadenzaA($_REQUEST[self::DATA_SCADENZA_A]);
			$scadenzaCliente->setCodNegozioSel($_REQUEST[self::COD_NEGOZIO_SELEZIONATO]);
			$scadenzaCliente->setStaScadenzaSel($_REQUEST[self::STATO_SCADENZA_SELEZIONATO]);
		}

		if (isset($_REQUEST[self::ID_PAGAMENTO]))
		{
			$scadenzaFornitore->setIdPagamento($_REQUEST[self::ID_PAGAMENTO]);
			$scadenzaFornitore->setIdScadenza($_REQUEST[self::ID_SCADENZA]);
			$registrazione->setIdRegistrazione($_REQUEST[self::ID_PAGAMENTO]);
		}
		
		if (isset($_REQUEST[self::ID_SCADENZA]))
		{
			$scadenzaFornitore->setIdScadenza($_REQUEST[self::ID_SCADENZA]);
		}
		

		// Serializzo in sessione gli oggetti modificati

		$_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
		$_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
		$_SESSION[self::REGISTRAZIONE] = serialize($registrazione);

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