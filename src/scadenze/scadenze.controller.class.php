<?php

require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'registrazione.class.php';
require_once 'nexus6.abstract.class.php';

class ScadenzeController extends Nexus6Abstract {

    public $scadenzeFunction = null;
    private $request;

    const DATA_SCADENZA_DA = "datascad_da";
    const DATA_SCADENZA_A = "datascad_a";
    const COD_NEGOZIO_SELEZIONATO = "codneg_sel";
    const STATO_SCADENZA_SELEZIONATO = "statoscad_sel";
    const ID_PAGAMENTO = "idPagamento";
    const ID_SCADENZA = "idScadenza";
    const ID_SCADENZA_CLIENTE = "idScadenzaCliente";
    
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
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
        }

        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $registrazione = Registrazione::getInstance();

        // parametri della request

        if (null !== filter_input(INPUT_POST, "datascad_da")) {
            $scadenzaFornitore->setDatScadenzaDa($this->getParmFromRequest("datascad_da"));
            $scadenzaFornitore->setDatScadenzaA($this->getParmFromRequest("datascad_a"));
            $scadenzaFornitore->setCodNegozioSel($this->getParmFromRequest("codneg_sel"));
            $scadenzaFornitore->setStaScadenzaSel($this->getParmFromRequest("statoscad_sel"));
            $scadenzaCliente->setDatScadenzaDa($this->getParmFromRequest("datascad_da"));
            $scadenzaCliente->setDatScadenzaA($this->getParmFromRequest("datascad_a"));
            $scadenzaCliente->setCodNegozioSel($this->getParmFromRequest("codneg_sel"));
            $scadenzaCliente->setStaScadenzaSel($this->getParmFromRequest("statoscad_sel"));
        }

        if (null !== filter_input(INPUT_POST, "idPagamento")) {
            $scadenzaFornitore->setIdPagamento($this->getParmFromRequest("idPagamento"));
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest("idScadenza"));
            $registrazione->setIdRegistrazione($this->getParmFromRequest("idPagamento"));
        }

        if (null !== filter_input(INPUT_POST, "idScadenza")) {
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest("idScadenza"));
        }

        if (null !== filter_input(INPUT_POST, "idScadenzaCliente")) {
            $scadenzaCliente->setIdScadenza($this->getParmFromRequest("idScadenzaCliente"));
        }

        if (null !== filter_input(INPUT_POST, "datascad_mod")) {
            if ($this->getParmFromRequest("datascad_mod") != $scadenzaFornitore->getDatScadenzaNuova()) {
                $scadenzaFornitore->setDatScadenzaNuova($this->getParmFromRequest("datascad_mod"));
            }
            $scadenzaFornitore->setNotaScadenza($this->getParmFromRequest("notascad_mod"));
            $scadenzaFornitore->setCodNegozio($this->getParmFromRequest("negozio_mod"));
            $scadenzaFornitore->setImpInScadenza($this->getParmFromRequest("impscad_mod"));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest("fatscad_mod"));
            $scadenzaFornitore->setNumFatturaOrig($this->getParmFromRequest("fatscad_orig_mod"));
            $scadenzaFornitore->setIdFornitoreOrig($this->getParmFromRequest("fornitore_orig_mod"));
            $scadenzaFornitore->setCodNegozio($this->getParmFromRequest("negozio_mod"));
        }

        if (null !== filter_input(INPUT_POST, "datascad_cli_mod")) {
            if ($this->getParmFromRequest("datascad_cli_mod") != $scadenzaCliente->getDatRegistrazione()) {
                $scadenzaCliente->setDatScadenzaNuova($this->getParmFromRequest("datascad_cli_mod"));
            }
            $scadenzaCliente->setNota($this->getParmFromRequest("notascad_cli_mod"));
            $scadenzaCliente->setCodNegozio($this->getParmFromRequest("negozio_cli_mod"));
            $scadenzaCliente->setImpRegistrazione($this->getParmFromRequest("impscad_cli_mod"));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest("fatscad_cli_mod"));
            $scadenzaCliente->setNumFatturaOrig($this->getParmFromRequest("fatscad_orig_cli_mod"));
            $scadenzaCliente->setIdClienteOrig($this->getParmFromRequest("fornitore_orig_cli_mod"));
            $scadenzaCliente->setCodNegozio($this->getParmFromRequest("negozio_cli_mod"));
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
        $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);

        if ($this->getRequest() == "start") {
            $this->scadenzeFunction->start();
        }
        if ($this->getRequest() == "go") {
            $this->scadenzeFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}