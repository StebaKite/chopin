<?php

require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'registrazione.class.php';
require_once 'nexus6.abstract.class.php';

class ScadenzeController extends Nexus6Abstract {

    public $scadenzeFunction = null;
    private $request;

    // Metodi

    public function __construct(ScadenzeBusinessInterface $scadenzeFunction) {
        $this->scadenzeFunction = $scadenzeFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }

        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $registrazione = Registrazione::getInstance();

        // parametri della request

        if (null !== $this->getParmFromRequest(self::DATA_SCADENZA_DA_RICERCA)) {
            $scadenzaFornitore->setDatScadenzaDa($this->getParmFromRequest(self::DATA_SCADENZA_DA_RICERCA));
            $scadenzaFornitore->setDatScadenzaA($this->getParmFromRequest(self::DATA_SCADENZA_A_RICERCA));
            $scadenzaFornitore->setCodNegozioSel($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA));
            $scadenzaFornitore->setStaScadenzaSel($this->getParmFromRequest(self::STATO_SCADENZA_RICERCA));
            $scadenzaCliente->setDatScadenzaDa($this->getParmFromRequest(self::DATA_SCADENZA_DA_RICERCA));
            $scadenzaCliente->setDatScadenzaA($this->getParmFromRequest(self::DATA_SCADENZA_A_RICERCA));
            $scadenzaCliente->setCodNegozioSel($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA));
            $scadenzaCliente->setStaScadenzaSel($this->getParmFromRequest(self::STATO_SCADENZA_RICERCA));
        }

        if (null !== $this->getParmFromRequest(self::ID_PAGM)) {
            $scadenzaFornitore->setIdPagamento($this->getParmFromRequest(self::ID_PAGM));
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest(self::ID_SCAD));
            $registrazione->setIdRegistrazione($this->getParmFromRequest(self::ID_PAGM));
        }

        if (null !== $this->getParmFromRequest(self::ID_SCAD)) {
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest(self::ID_SCAD));
        }

        if (null !== $this->getParmFromRequest(self::ID_SCAD_CLIENTE)) {
            $scadenzaCliente->setIdScadenza($this->getParmFromRequest(self::ID_SCAD_CLIENTE));
        }

        if (null !== $this->getParmFromRequest(self::DATA_SCADENZA_MODIFICA)) {
            if ($this->getParmFromRequest(self::DATA_SCADENZA_MODIFICA) != $scadenzaFornitore->getDatScadenzaNuova()) {
                $scadenzaFornitore->setDatScadenzaNuova($this->getParmFromRequest(self::DATA_SCADENZA_MODIFICA));
            }
            $scadenzaFornitore->setNotaScadenza($this->getParmFromRequest(self::NOTA_SCADENZA_MODIFICA));
            $scadenzaFornitore->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_SCADENZA_MODIFICA));
            $scadenzaFornitore->setImpInScadenza($this->getParmFromRequest(self::IMPORTO_SCADENZA_MODIFICA));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_SCADENZA_MODIFICA));
            $scadenzaFornitore->setNumFatturaOrig($this->getParmFromRequest(self::NUMERO_FATTURA_SCADENZA_ORIGINALE_MODIFICA));
            $scadenzaFornitore->setIdFornitoreOrig($this->getParmFromRequest(self::FORNITORE_SCADENZA_ORIGINALE_MODIFICA));
            $scadenzaFornitore->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_SCADENZA_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::DATA_SCADENZA_CLIENTE_MODIFICA)) {
            if ($this->getParmFromRequest(self::DATA_SCADENZA_CLIENTE_MODIFICA) != $scadenzaCliente->getDatRegistrazione()) {
                $scadenzaCliente->setDatScadenzaNuova($this->getParmFromRequest(self::DATA_SCADENZA_CLIENTE_MODIFICA));
            }
            $scadenzaCliente->setNota($this->getParmFromRequest(self::NOTA_SCADENZA_CLIENTE_MODIFICA));
            $scadenzaCliente->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_SCADENZA_CLIENTE_MODIFICA));
            $scadenzaCliente->setImpRegistrazione($this->getParmFromRequest(self::IMPORTO_SCADENZA_CLIENTE_MODIFICA));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_SCADENZA_CLIENTE_MODIFICA));
            $scadenzaCliente->setNumFatturaOrig($this->getParmFromRequest(self::NUMERO_FATTURA_SCADENZA_CLIENTE_ORIGINALE_MODIFICA));
            $scadenzaCliente->setIdClienteOrig($this->getParmFromRequest(self::FORNITORE_SCADENZA_CLIENTE_ORIGINALE_MODIFICA));
            $scadenzaCliente->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_SCADENZA_CLIENTE_MODIFICA));
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
        $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);

        if ($this->getRequest() == self::START) {
            $this->scadenzeFunction->start();
        }
        if ($this->getRequest() == self::GO) {
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