<?php

require_once 'registrazione.class.php';
require_once 'conto.class.php';
require_once 'corrispettivo.class.php';
require_once 'nexus6.abstract.class.php';

class StrumentiController extends Nexus6Abstract {
    
    public $strumentiFunction = null;
    private $request;

    // Metodi
    
    public function __construct(StrumentiBusinessInterface $strumentiFunction) {
        $this->strumentiFunction = $strumentiFunction;
        $this->setRequest(null);
    }
    
    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }
             
        $registrazione = Registrazione::getInstance();
        $conto = Conto::getInstance();
        $corrispettivo = Corrispettivo::getInstance();

        // Cambia conto registrazioni ==============================================================

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA)) {
            $registrazione->setDatRegistrazioneDa($this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA));
            $registrazione->setDatRegistrazioneA($this->getParmFromRequest(self::DATA_REGISTRAZIONE_A_RICERCA));
            $registrazione->setCodNegozioSel($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA));
            $registrazione->setCodContoSel($this->getParmFromRequest(self::CODICE_CONTO_RICERCA));
            $conto->setCodContoSel($this->getParmFromRequest(self::CODICE_CONTO_RICERCA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_NUOVO_MODIFICA)) {
            $conto->setCodContoSelNuovo($this->getParmFromRequest(self::CODICE_CONTO_NUOVO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_CASSA)) {
            $corrispettivo->setMese($this->getParmFromRequest(self::MESE));
            $corrispettivo->setAnno($this->getParmFromRequest(self::ANNO));
            $corrispettivo->setCodNeg($this->getParmFromRequest(self::CODICE_NEGOZIO));
            $corrispettivo->setFile($this->getParmFromRequest(self::FILE));
            $corrispettivo->setDatada($this->getParmFromRequest(self::DATA_IMPORTAZIONE_DA));
            $corrispettivo->setDataa($this->getParmFromRequest(self::DATA_IMPORTAZIONE_A));
            $corrispettivo->setContoCassa($this->getParmFromRequest(self::CODICE_CONTO_CASSA));
        }
        
        // Serializzo in sessione gli oggetti modificati ========================================

        parent::setIndexSession(self::REGISTRAZIONE, serialize($registrazione));
        parent::setIndexSession(self::CONTO, serialize($conto));
        parent::setIndexSession(self::CORRISPETTIVO, serialize($corrispettivo));
        
        if ($this->getRequest() == self::START) {
            $this->strumentiFunction->start();
        }
        if ($this->getRequest() == self::GO) {
            $this->strumentiFunction->go();
        }
    }    
 
    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }   
}
