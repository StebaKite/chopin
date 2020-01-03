<?php

require_once 'bilancio.class.php';
require_once 'riepilogo.class.php';
require_once 'nexus6.abstract.class.php';

class RiepiloghiController extends Nexus6Abstract {

    public $riepiloghiFunction = null;
    private $request;

    /**
     * Metodi
     */
    public function __construct(RiepiloghiBusinessInterface $riepiloghiFunction) {
        $this->riepiloghiFunction = $riepiloghiFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }

        $bilancio = Bilancio::getInstance();
        $riepilogo = Riepilogo::getInstance();

        if (null !== $this->getParmFromRequest(self::ANNO_ESERCIZIO_RICERCA)) {
            $bilancio->setDataregDa("01/01/" . $this->getParmFromRequest(self::ANNO_ESERCIZIO_RICERCA));
            $bilancio->setDataregA("31/12/" . $this->getParmFromRequest(self::ANNO_ESERCIZIO_RICERCA));
            $bilancio->setAnnoEsercizioSel($this->getParmFromRequest(self::ANNO_ESERCIZIO_RICERCA));
            $bilancio->setSoloContoEconomico(self::INDICATORE_TUTTI_I_CONTI);
            $bilancio->setCodnegSel($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA));
            $bilancio->setSaldiInclusi($this->getParmFromRequest(self::INDICATORE_SALDI_INCLUSI));
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA)) {
            $bilancio->setDataregDa($this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA));
            $bilancio->setDataregA($this->getParmFromRequest(self::DATA_REGISTRAZIONE_A_RICERCA));
            $bilancio->setCodnegSel($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA));
            $bilancio->setCatconto($this->getParmFromRequest(self::CATEGORIA_CONTO_RICERCA));
            $bilancio->setSaldiInclusi($this->getParmFromRequest(self::INDICATORE_SALDI_INCLUSI));
            $bilancio->setSoloContoEconomico($this->getParmFromRequest(self::INDICATORE_CONTO_ECONOMICO));
            $riepilogo->setDataregDa($this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA));
            $riepilogo->setDataregA($this->getParmFromRequest(self::DATA_REGISTRAZIONE_A_RICERCA));
            $riepilogo->setSaldiInclusi($this->getParmFromRequest(self::INDICATORE_SALDI_INCLUSI));
            $riepilogo->setSoloContoEconomico($this->getParmFromRequest(self::INDICATORE_CONTO_ECONOMICO));
            $riepilogo->setCodnegSel($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA));
        }

        // Serializzo in sessione gli oggetti modificati

        parent::setIndexSession(self::BILANCIO, serialize($bilancio));
        parent::setIndexSession(self::RIEPILOGO, serialize($riepilogo));

        if ($this->getRequest() == self::START) {
            $this->riepiloghiFunction->start();
        }
        if ($this->getRequest() == self::GO) {
            $this->riepiloghiFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}