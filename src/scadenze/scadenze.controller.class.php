<?php

require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'registrazione.class.php';

class ScadenzeController {

    public $scadenzeFunction = null;
    private $request;

    const MODO = "modo";
    const START = "start";
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
            if (isset($_REQUEST[self::MODO]))
                $this->setRequest($_REQUEST[self::MODO]);
            else
                $this->setRequest(self::START);
        }

        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $registrazione = Registrazione::getInstance();

        // parametri della request

        if (isset($_REQUEST[self::DATA_SCADENZA_DA])) {
            $scadenzaFornitore->setDatScadenzaDa($_REQUEST[self::DATA_SCADENZA_DA]);
            $scadenzaFornitore->setDatScadenzaA($_REQUEST[self::DATA_SCADENZA_A]);
            $scadenzaFornitore->setCodNegozioSel($_REQUEST[self::COD_NEGOZIO_SELEZIONATO]);
            $scadenzaFornitore->setStaScadenzaSel($_REQUEST[self::STATO_SCADENZA_SELEZIONATO]);

            $scadenzaCliente->setDatScadenzaDa($_REQUEST[self::DATA_SCADENZA_DA]);
            $scadenzaCliente->setDatScadenzaA($_REQUEST[self::DATA_SCADENZA_A]);
            $scadenzaCliente->setCodNegozioSel($_REQUEST[self::COD_NEGOZIO_SELEZIONATO]);
            $scadenzaCliente->setStaScadenzaSel($_REQUEST[self::STATO_SCADENZA_SELEZIONATO]);
        }

        if (isset($_REQUEST[self::ID_PAGAMENTO])) {
            $scadenzaFornitore->setIdPagamento($_REQUEST[self::ID_PAGAMENTO]);
            $scadenzaFornitore->setIdScadenza($_REQUEST[self::ID_SCADENZA]);
            $registrazione->setIdRegistrazione($_REQUEST[self::ID_PAGAMENTO]);
        }

        if (isset($_REQUEST[self::ID_SCADENZA])) {
            $scadenzaFornitore->setIdScadenza($_REQUEST[self::ID_SCADENZA]);
        }

        if (isset($_REQUEST[self::ID_SCADENZA_CLIENTE])) {
            $scadenzaCliente->setIdScadenza($_REQUEST[self::ID_SCADENZA_CLIENTE]);
        }

        if (isset($_REQUEST["datascad_mod"])) {
            if ($_REQUEST["datascad_mod"] != $scadenzaFornitore->getDatScadenzaNuova())
                $scadenzaFornitore->setDatScadenzaNuova($_REQUEST["datascad_mod"]);
                        
            $scadenzaFornitore->setNotaScadenza($_REQUEST["notascad_mod"]);
            $scadenzaFornitore->setCodNegozio($_REQUEST["negozio_mod"]);
            $scadenzaFornitore->setImpInScadenza($_REQUEST["impscad_mod"]);
            $scadenzaFornitore->setNumFattura($_REQUEST["fatscad_mod"]);
            $scadenzaFornitore->setNumFatturaOrig($_REQUEST["fatscad_orig_mod"]);
            $scadenzaFornitore->setIdFornitoreOrig($_REQUEST["fornitore_orig_mod"]);
            $scadenzaFornitore->setCodNegozio($_REQUEST["negozio_mod"]);
        }

        if (isset($_REQUEST["datascad_cli_mod"])) {
            if ($_REQUEST["datascad_cli_mod"] != $scadenzaCliente->getDatRegistrazione())
                $scadenzaCliente->setDatScadenzaNuova($_REQUEST["datascad_cli_mod"]);            
            
            $scadenzaCliente->setNota($_REQUEST["notascad_cli_mod"]);
            $scadenzaCliente->setCodNegozio($_REQUEST["negozio_cli_mod"]);
            $scadenzaCliente->setImpRegistrazione($_REQUEST["impscad_cli_mod"]);
            $scadenzaCliente->setNumFattura($_REQUEST["fatscad_cli_mod"]);
            $scadenzaCliente->setNumFatturaOrig($_REQUEST["fatscad_orig_cli_mod"]);
            $scadenzaCliente->setIdClienteOrig($_REQUEST["fornitore_orig_cli_mod"]);
            $scadenzaCliente->setCodNegozio($_REQUEST["negozio_cli_mod"]);
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

?>