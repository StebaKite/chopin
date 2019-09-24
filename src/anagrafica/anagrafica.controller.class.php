<?php

require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'mercato.class.php';
require_once 'nexus6.abstract.class.php';

class AnagraficaController extends Nexus6Abstract {

    public static $anagraficaFunction = null;
    private $request;

    // Metodi

    public function __construct(AnagraficaBusinessInterface $anagraficaFunction) {
        $this->anagraficaFunction = $anagraficaFunction;
        $this->setRequest(null);
    }

    public function start() {
 
        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }

        $fornitore = Fornitore::getInstance();
        $cliente = Cliente::getInstance();
        $mercato = Mercato::getInstance();

        if (null !== $this->getParmFromRequest(self::CODICE_FORNITORE_CREAZIONE)) {
            $descrizione = ($this->getParmFromRequest(self::DES_FORNITORE_CREAZIONE) != "") ? str_replace("'", "''", $this->getParmFromRequest(self::DES_FORNITORE_CREAZIONE)) : "";
            $fornitore->setDesFornitore($descrizione);
            $indirizzo = ($this->getParmFromRequest(self::INDIRIZZO_FORNITORE_CREAZIONE) != "") ? str_replace("'", "''", $this->getParmFromRequest(self::INDIRIZZO_FORNITORE_CREAZIONE)) : "";
            $fornitore->setDesIndirizzoFornitore($indirizzo);
            $citta = ($this->getParmFromRequest(self::CITTA_FORNITORE_CREAZIONE) != "") ? str_replace("'", "''", $this->getParmFromRequest(self::CITTA_FORNITORE_CREAZIONE)) : "";
            $fornitore->setDesCittaFornitore($citta);
            $fornitore->setCapFornitore($this->getParmFromRequest(self::CAP_FORNITORE_CREAZIONE));
            $fornitore->setTipAddebito($this->getParmFromRequest(self::TIPO_ADDEBITO_CREAZIONE));
            $fornitore->setNumGgScadenzaFattura($this->getParmFromRequest(self::GIORNI_SCADENZA_FATTURA_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_FORNITORE_MODIFICA)) {
            $descrizione = ($this->getParmFromRequest(self::DES_FORNITORE_MODIFICA) != "") ? str_replace("'", "''", $this->getParmFromRequest(self::DES_FORNITORE_MODIFICA)) : "";
            $fornitore->setDesFornitore($descrizione);
            $indirizzo = ($this->getParmFromRequest(self::INDIRIZZO_FORNITORE_MODIFICA) != "") ? str_replace("'", "''", $this->getParmFromRequest(self::INDIRIZZO_FORNITORE_MODIFICA)) : "";
            $fornitore->setDesIndirizzoFornitore($indirizzo);
            $citta = ($this->getParmFromRequest(self::CITTA_FORNITORE_MODIFICA) != "") ? str_replace("'", "''", $this->getParmFromRequest(self::CITTA_FORNITORE_MODIFICA)) : "";
            $fornitore->setDesCittaFornitore($citta);
            $fornitore->setCapFornitore($this->getParmFromRequest(self::CAP_FORNITORE_MODIFICA));
            $fornitore->setTipAddebito($this->getParmFromRequest(self::TIPO_ADDEBITO_MODIFICA));
            $fornitore->setNumGgScadenzaFattura($this->getParmFromRequest(self::GIORNI_SCADENZA_FATTURA_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CLIENTE_CREAZIONE)) {
            $cliente->setCatCliente($this->getParmFromRequest(self::CATEGORIA_CLIENTE_CREAZIONE));
            $cliente->setCodCliente($this->getParmFromRequest(self::CODICE_CLIENTE_CREAZIONE));
            $cliente->setDesCliente($this->getParmFromRequest(self::DES_CLIENTE_CREAZIONE));
            $cliente->setDesIndirizzoCliente($this->getParmFromRequest(self::INDIRIZZO_CLIENTE_CREAZIONE));
            $cliente->setDesCittaCliente($this->getParmFromRequest(self::CITTA_CLIENTE_CREAZIONE));
            $cliente->setCapCliente($this->getParmFromRequest(self::CAP_CLIENTE_CREAZIONE));
            $cliente->setTipAddebito($this->getParmFromRequest(self::TIPO_ADDEBITO_CLIENTE_CREAZIONE));
            $cliente->setCodPiva($this->getParmFromRequest(self::PARTITA_IVA_CLIENTE_CREAZIONE));
            $cliente->setCodFisc($this->getParmFromRequest(self::CODICE_FISCALE_CLIENTE_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CLIENTE_MODIFICA)) {
            $cliente->setCatCliente($this->getParmFromRequest(self::CATEGORIA_CLIENTE_MODIFICA));
            $cliente->setDesCliente($this->getParmFromRequest(self::DES_CLIENTE_MODIFICA));
            $cliente->setDesIndirizzoCliente($this->getParmFromRequest(self::INDIRIZZO_CLIENTE_MODIFICA));
            $cliente->setDesCittaCliente($this->getParmFromRequest(self::CITTA_CLIENTE_MODIFICA));
            $cliente->setCapCliente($this->getParmFromRequest(self::CAP_CLIENTE_MODIFICA));
            $cliente->setTipAddebito($this->getParmFromRequest(self::TIPO_ADDEBITO_CLIENTE_MODIFICA));
            $cliente->setCodPiva($this->getParmFromRequest(self::PARTITA_IVA_CLIENTE_MODIFICA));
            $cliente->setCodFisc($this->getParmFromRequest(self::CODICE_FISCALE_CLIENTE_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_MERCATO_CREAZIONE)) {
            $mercato->setCodMercato($this->getParmFromRequest(self::CODICE_MERCATO_CREAZIONE));
            $mercato->setDesMercato($this->getParmFromRequest(self::DES_MERCATO_CREAZIONE));
            $mercato->setCittaMercato($this->getParmFromRequest(self::CITTA_MERCATO_CREAZIONE));
            $mercato->setCodNegozio($this->getParmFromRequest(self::NEGOZIO_MERCATO_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_MERCATO_MODIFICA)) {
            $mercato->setCodMercato($this->getParmFromRequest(self::CODICE_MERCATO_MODIFICA));
            $mercato->setDesMercato($this->getParmFromRequest(self::DES_MERCATO_MODIFICA));
            $mercato->setCittaMercato($this->getParmFromRequest(self::CITTA_MERCATO_MODIFICA));
            $mercato->setCodNegozio($this->getParmFromRequest(self::NEGOZIO_MERCATO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_FISCALE)) {
            $cliente->setCodFisc($this->getParmFromRequest(self::CODICE_FISCALE));
        }

        if (null !== $this->getParmFromRequest(self::PARTITA_IVA)) {
            $cliente->setCodPiva($this->getParmFromRequest(self::PARTITA_IVA));
            $cliente->setDesCliente($this->getParmFromRequest(self::DES_CLIENTE));
        }

        if (null !== $this->getParmFromRequest(self::ID_CLIENTE)) {
            $cliente->setIdCliente($this->getParmFromRequest(self::ID_CLIENTE));
        }

        if (null !== $this->getParmFromRequest(self::ID_FORNITORE)) {
            $fornitore->setIdFornitore($this->getParmFromRequest(self::ID_FORNITORE));
        }

        if (null !== $this->getParmFromRequest(self::ID_MERCATO)) {
            $mercato->setIdMercato($this->getParmFromRequest(self::ID_MERCATO));
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::FORNITORE] = serialize($fornitore);
        $_SESSION[self::CLIENTE] = serialize($cliente);
        $_SESSION[self::MERCATO] = serialize($mercato);

        if ($this->getRequest() == self::START) {
            $this->anagraficaFunction->start();
        }
        if ($this->getRequest() == self::GO) {
            $this->anagraficaFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}