<?php

require_once 'conto.class.php';
require_once 'sottoconto.class.php';
require_once 'causale.class.php';
require_once 'progressivoFattura.class.php';
require_once 'configurazioneCausale.class.php';
require_once 'nexus6.abstract.class.php';

class ConfigurazioniController extends Nexus6Abstract {

    public $configurazioniFunction = null;
    private $request;

    // Metodi

    public function __construct(ConfigurazioniBusinessInterface $configurazioniFunction) {
        $this->configurazioniFunction = $configurazioniFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }

        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $causale = Causale::getInstance();
        $configurazioneCausale = ConfigurazioneCausale::getInstance();
        $progressivoFattura = ProgressivoFattura::getInstance();

        // Conti --------------------------------------------------

        $conto->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO));
        
        if (null !== $this->getParmFromRequest(self::CATEGORIA_CONTO_RICERCA)) {
            $conto->setCatContoSel($this->getParmFromRequest(self::CATEGORIA_CONTO_RICERCA));
            $conto->setTipContoSel($this->getParmFromRequest(self::TIPO_CONTO_RICERCA));
        }
        
        if (null !== $this->getParmFromRequest(self::CODICE_SOTTOCONTO_DETTAGLIO)) {
            $sottoconto->setCodConto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_DETTAGLIO));
            $sottoconto->setCodSottoconto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_DETTAGLIO));
            $sottoconto->setDesSottoconto($this->getParmFromRequest(self::DES_SOTTOCONTO_DETTAGLIO));
        }        

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_CREAZIONE)) {
            $conto->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_CREAZIONE));
            $conto->setDesConto($this->getParmFromRequest(self::DES_CONTO_CREAZIONE));
            $conto->setCatConto($this->getParmFromRequest(self::CATEGORIA_CONTO_CREAZIONE));
            $conto->setTipConto($this->getParmFromRequest(self::INDICATORE_DARE_AVERE_CREAZIONE));
            $conto->setIndPresenzaInBilancio($this->getParmFromRequest(self::INDICATORE_PRESENZA_IN_BILANCIO_CREAZIONE));
            $conto->setIndVisibilitaSottoconti($this->getParmFromRequest(self::INDICATORE_VISUALIZZAZIONE_SOTTOCONTI_CREAZIONE));
            $conto->setNumRigaBilancio($this->getParmFromRequest(self::NUMERO_RIGA_BILANCIO_CREAZIONE));
        }
        
        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_MODIFICA)) {
            $conto->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_MODIFICA));
            $conto->setDesConto($this->getParmFromRequest(self::DES_CONTO_MODIFICA));
            $conto->setCatConto($this->getParmFromRequest(self::CATEGORIA_CONTO_MODIFICA));
            $conto->setTipConto($this->getParmFromRequest(self::INDICATORE_DARE_AVERE_MODIFICA));
            $conto->setIndPresenzaInBilancio($this->getParmFromRequest(self::INDICATORE_PRESENZA_IN_BILANCIO_MODIFICA));
            $conto->setIndVisibilitaSottoconti($this->getParmFromRequest(self::INDICATORE_VISUALIZZAZIONE_SOTTOCONTI_MODIFICA));
            $conto->setNumRigaBilancio($this->getParmFromRequest(self::NUMERO_RIGA_BILANCIO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_GRUPPO_MODIFICA)) {
            $sottoconto->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_GRUPPO_MODIFICA));
            $sottoconto->setCodSottoconto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_GRUPPO_MODIFICA));
            $sottoconto->setIndGruppo($this->getParmFromRequest(self::INDICATORE_GRUPPO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_SOTTOCONTO_GRUPPO_CREAZIONE)) {
            $sottoconto->setCodSottoconto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_GRUPPO_CREAZIONE));
            $sottoconto->setDesSottoconto($this->getParmFromRequest(self::DES_SOTTOCONTO_GRUPPO_CREAZIONE));
            $sottoconto->setIndGruppo($this->getParmFromRequest(self::INDICATORE_GRUPPO_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_SOTTOCONTO_GRUPPO_CANCELLAZIONE)) {
            $sottoconto->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_GRUPPO_CANCELLAZIONE));
            $sottoconto->setCodSottoconto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_GRUPPO_CANCELLAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_SOTTOCONTO_RICERCA_MOVIMENTI)) {
            $sottoconto->setDataRegistrazioneDa($this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA_MOVIMENTI));
            $sottoconto->setDataRegistrazioneA($this->getParmFromRequest(self::DATA_REGISTRAZIONE_A_RICERCA_MOVIMENTI));
            $sottoconto->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_RICERCA_MOVIMENTI));
            $sottoconto->setCodSottoconto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_RICERCA_MOVIMENTI));
            $sottoconto->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA_MOVIMENTI));
            $sottoconto->setSaldiInclusi($this->getParmFromRequest(self::SALDI_INCLUSI_RICERCA_MOVIMENTI));
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA)) {
            $conto->setCatConto($this->getParmFromRequest(self::CATEGORIA_CONTO));
            $conto->setDesConto($this->getParmFromRequest(self::DES_CONTO));
            $sottoconto->setDesSottoconto($this->getParmFromRequest(self::DES_SOTTOCONTO));
        }

        // Causali ---------------------------------------

        if (null !== $this->getParmFromRequest(self::CODICE_CAUSALE_CREAZIONE)) {
            $causale->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_CREAZIONE));
            $causale->setDesCausale($this->getParmFromRequest(self::DES_CAUSALE_CREAZIONE));
            $causale->setCatCausale($this->getParmFromRequest(self::CATEGORIA_CAUSALE_CREAZIONE));
            $configurazioneCausale->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_CREAZIONE));
            $configurazioneCausale->setDesCausale($this->getParmFromRequest(self::DES_CAUSALE_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CAUSALE_CONFIGURAZIONE)) {
            $causale->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_CONFIGURAZIONE));
            $configurazioneCausale->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_CONFIGURAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_CONFIGURAZIONE)) {
            $configurazioneCausale->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_CONFIGURAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CAUSALE_MODIFICA)) {
            $causale->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_MODIFICA));
            $causale->setDesCausale($this->getParmFromRequest(self::DES_CAUSALE_MODIFICA));
            $causale->setCatCausale($this->getParmFromRequest(self::CATEGORIA_CAISALE_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CAUSALE_CANCELLAZIONE)) {
            $causale->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_CANCELLAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CAT_CLIENTE_MODIFICA)) {
            $progressivoFattura->setCatCliente($this->getParmFromRequest(self::CAT_CLIENTE_MODIFICA));
            $progressivoFattura->setNegProgr($this->getParmFromRequest(self::CODICE_NEGOZIO_MODIFICA));
            $progressivoFattura->setNumFatturaUltimo($this->getParmFromRequest(self::NUMERO_FATTURA_REGISTRAZIONE_MODIFICA));
            $progressivoFattura->setNotaTestaFattura($this->getParmFromRequest(self::NOTA_TESTATA_MODIFICA));
            $progressivoFattura->setNotaPiedeFattura($this->getParmFromRequest(self::NOTA_PIEDE_MODIFICA));
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::CONTO] = serialize($conto);
        $_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
        $_SESSION[self::CAUSALE] = serialize($causale);
        $_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
        $_SESSION[self::PROGRESSIVO_FATTURA] = serialize($progressivoFattura);

        if ($this->getRequest() == self::START) {
            $this->configurazioniFunction->start();
        }
        if ($this->getRequest() == self::GO) {
            $this->configurazioniFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}