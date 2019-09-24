<?php

require_once 'registrazione.class.php';
require_once 'causale.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'mercato.class.php';
require_once 'nexus6.abstract.class.php';

class PrimanotaController extends Nexus6Abstract {

    public $primanotaFunction = null;
    private $request;

    // Metodi

    public function __construct(PrimanotaBusinessInterface $primanotaFunction) {
        $this->primanotaFunction = $primanotaFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest(self::MODO));
        } else {
            $this->setRequest(self::START);         // default set
        }

        $registrazione = Registrazione::getInstance();
        $causale = Causale::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $fornitore = Fornitore::getInstance();
        $cliente = Cliente::getInstance();
        $mercato = Mercato::getInstance();

        // Registrazione fatture ==============================================================

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA)) {
            $registrazione->setDatRegistrazioneDa($this->getParmFromRequest(self::DATA_REGISTRAZIONE_DA_RICERCA));
            $registrazione->setDatRegistrazioneA($this->getParmFromRequest(self::DATA_REGISTRAZIONE_A_RICERCA));
            $registrazione->setCodNegozioSel($this->getParmFromRequest(self::CODICE_NEGOZIO_RICERCA));
            $registrazione->setCodCausaleSel($this->getParmFromRequest(self::CAUSALE_RICERCA));
            $causale->setCodCausale($this->getParmFromRequest(self::CAUSALE_RICERCA));
        }

        if (null !== $this->getParmFromRequest(self::IMPORTO_DETTAGLIO)) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO));
            $dettaglioRegistrazione->setCodContoComposto($this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO));
            $dettaglioRegistrazione->setCodSottoconto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_DETTAGLIO));
            $dettaglioRegistrazione->setIndDareavere(strtoupper($this->getParmFromRequest(self::DARE_AVERE_DETTAGLIO)));            
            $dettaglioRegistrazione->setImpRegistrazione(str_replace(",", ".", $this->getParmFromRequest(self::IMPORTO_DETTAGLIO)));
            $dettaglioRegistrazione->setIdDettaglioRegistrazione($this->getParmFromRequest(self::ID_DETTAGLIO));
        }

        if (null !== $this->getParmFromRequest(self::IMPORTO_DETTAGLIO_IN_SCADENZA)) {
            $scadenzaFornitore->setImpInScadenza(str_replace(",", ".", $this->getParmFromRequest(self::IMPORTO_DETTAGLIO_IN_SCADENZA)));
            $scadenzaCliente->setImportoScadenza(str_replace(",", ".", $this->getParmFromRequest(self::IMPORTO_DETTAGLIO_IN_SCADENZA)));
        }

        if (null !== $this->getParmFromRequest(self::DARE_AVERE_DETTAGLIO)) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO));
            $dettaglioRegistrazione->setCodSottoconto($this->getParmFromRequest(self::CODICE_SOTTOCONTO_DETTAGLIO));
            $dettaglioRegistrazione->setIndDareavere(strtoupper($this->getParmFromRequest(self::DARE_AVERE_DETTAGLIO)));
            $dettaglioRegistrazione->setIdDettaglioRegistrazione($this->getParmFromRequest(self::ID_DETTAGLIO));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO)) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO));
            $dettaglioRegistrazione->setIndDareAvere(strtoupper($this->getParmFromRequest(self::DARE_AVERE_DETTAGLIO)));
        }

        if (null !== $this->getParmFromRequest(self::CAUSALE_RICERCA)) {
            $causale->setCodCausale($this->getParmFromRequest(self::CAUSALE_RICERCA));
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_CREAZIONE)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_CREAZIONE));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_REGISTRAZIONE_CREAZIONE));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CAUSALE_REGISTRAZIONE_CREAZIONE));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_REGISTRAZIONE_CREAZIONE));
            $registrazione->setIdFornitore($this->getParmFromRequest(self::FORNITORE_REGISTRAZIONE_CREAZIONE));
            $registrazione->setIdCliente($this->getParmFromRequest(self::CLIENTE_REGISTRAZIONE_CREAZIONE));
            $registrazione->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_REGISTRAZIONE_CREZIONE));
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
            $registrazione->setIdMercato(self::EMPTYSTRING);
            $scadenzaCliente->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_CREAZIONE);
            $scadenzaFornitore->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_CREAZIONE);
        }

        if (null !== $this->getParmFromRequest(self::ID_FORNITORE)) {
            $scadenzaFornitore->setIdFornitore($this->getParmFromRequest(self::ID_FORNITORE));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_FORNITORE));
            $scadenzaFornitore->setDatScadenza($this->getParmFromRequest(self::DATA_REGISTRAZIONE));
            $registrazione->setIdFornitore($this->getParmFromRequest(self::ID_FORNITORE));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE));
//            $scadenzaCliente->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_CREAZIONE);
//            $scadenzaFornitore->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_CREAZIONE);
        }

        if (null !== $this->getParmFromRequest(self::ID_CLIENTE)) {
            $scadenzaCliente->setIdCliente($this->getParmFromRequest(self::ID_CLIENTE));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_FORNITORE));
            $scadenzaCliente->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE));
            $registrazione->setIdCliente($this->getParmFromRequest(self::ID_CLIENTE));
        }

        if (null !== $this->getParmFromRequest(self::DATA_SCADENZA_FORNITORE)) {
            $scadenzaFornitore->setIdFornitore($this->getParmFromRequest(self::ID_FORNITORE));
            $scadenzaFornitore->setDatScadenza($this->getParmFromRequest(self::DATA_SCADENZA_FORNITORE));
            $scadenzaFornitore->setDatScadenzaNuova($this->getParmFromRequest(self::DATA_SCADENZA_NUOVA));
            $scadenzaFornitore->setImpInScadenza(str_replace(",", ".", $this->getParmFromRequest(self::IMPORTO_SCADENZA_FORNITORE)));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_FORNITORE));
        }

        if (null !== $this->getParmFromRequest(self::DATA_SCADENZA_CLIENTE)) {
            $scadenzaCliente->setIdCliente($this->getParmFromRequest(self::ID_CLIENTE));
            $scadenzaCliente->setDatRegistrazione($this->getParmFromRequest(self::DATA_SCADENZA_CLIENTE));
            $scadenzaCliente->setImpRegistrazione(str_replace(",", ".", $this->getParmFromRequest(self::IMPORTO_SCADENZA_CLIENTE)));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_FORNITORE));
        }

        if (null !== $this->getParmFromRequest(self::DATA_SCADENZA_NUOVA_FORNITORE)) {
            $scadenzaFornitore->setDatScadenza($this->getParmFromRequest(self::DATA_SCADENZA_VECCHIA_FORNITORE));
            $scadenzaFornitore->setDatScadenzaNuova($this->getParmFromRequest(self::DATA_SCADENZA_NUOVA_FORNITORE));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_FORNITORE));
        }

        if (null !== $this->getParmFromRequest(self::DATA_SCADENZA_NUOVA_CLIENTE)) {
            $scadenzaCliente->setDatRegistrazione($this->getParmFromRequest(self::DATA_SCADENZA_VECCHIA_CLIENTE));
            $scadenzaCliente->setDatScadenzaNuova($this->getParmFromRequest(self::DATA_SCADENZA_NUOVA_CLIENTE));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_FORNITORE));
        }
        
        if (null !== $this->getParmFromRequest(self::ID_REGISTRAZIONE)) {
            $registrazione->setIdRegistrazione($this->getParmFromRequest(self::ID_REGISTRAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_MODIFICA)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_MODIFICA));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_REGISTRAZIONE_MODIFICA));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CAUSALE_REGISTRAZIONE_MODIFICA));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_REGISTRAZIONE_MODIFICA));
            $registrazione->setIdFornitore($this->getParmFromRequest(self::FORNITORE_REGISTRAZIONE_MODIFICA));
            $registrazione->setIdCliente($this->getParmFromRequest(self::CLIENTE_REGISTRAZIONE_MODIFICA));
            $registrazione->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_REGISTRAZIONE_MODIFICA));
            $registrazione->setNumFatturaOrig($this->getParmFromRequest(self::NUMERO_FATTURA_REGISTRAZIONE_ORIGINALE_MODIFICA));
//            $scadenzaCliente->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_MODIFICA);
//            $scadenzaFornitore->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_MODIFICA);
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO_CREAZIONE)) {
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest(self::IMPORTO_DETTAGLIO_CREAZIONE));
            $dettaglioRegistrazione->setIndDareavere($this->getParmFromRequest(self::SEGNO_DETTAGLIO_CREAZIONE));
            $temp = explode(" - ", $this->getParmFromRequest(self::CODICE_CONTO_DETTAGLIO_CREAZIONE));      // Conto - descrizione
            $cc = explode(".", $temp[0]);   // conto.sottoconto
            $dettaglioRegistrazione->setCodConto($cc[0]);   // conto
            $dettaglioRegistrazione->setCodSottoconto($cc[1]);  // sottoconto
        }
        
        if (null !== $this->getParmFromRequest(self::TABELLA_SCADENZE)) {
            $scadenzaCliente->setScadenzeTable($this->getParmFromRequest(self::TABELLA_SCADENZE));
        }
        
        // Registrazione incasso ==========================================================

        if (null !== $this->getParmFromRequest(self::CODICE_CLIENTE_INCASSO_CREAZIONE)) {
            $cliente->setIdCliente($this->getParmFromRequest(self::CODICE_CLIENTE_INCASSO_CREAZIONE));
            $scadenzaCliente->setCodNegozioSel($this->getParmFromRequest(self::CODICE_NEGOZIO_INCASSO_CREAZIONE));
            $scadenzaCliente->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_INCASSO_CREAZIONE);
            $scadenzaCliente->setIdTableScadenzeChiuse(self::TABELLA_SCADENZE_CHIUSE_INCASSO_CREAZIONE);
            $dettaglioRegistrazione->setIdTablePagina(self::TABELLA_DETTAGLI_INCASSO_CREAZIONE);
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_INCASSO_CREAZIONE)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_INCASSO_CREAZIONE));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_REGISTRAZIONE_INCASSO_CREAZIONE));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_INCASSO_CREAZIONE));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_INCASSO_CREAZIONE));
            $registrazione->setIdCliente($this->getParmFromRequest(self::CODICE_CLIENTE_INCASSO_CREAZIONE));
            $registrazione->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_INCASSO_CREAZIONE));
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
            $registrazione->setIdMercato(self::EMPTYSTRING);
        }

        // modifica incasso ===============================================================

        if (null !== $this->getParmFromRequest(self::CODICE_CLIENTE_INCASSO_MODIFICA)) {
            $cliente->setIdCliente($this->getParmFromRequest(self::CODICE_CLIENTE_INCASSO_MODIFICA));
            $scadenzaCliente->setCodNegozioSel($this->getParmFromRequest(self::CODICE_NEGOZIO_INCASSO_MODIFICA));
            $scadenzaCliente->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_INCASSO_MODIFICA);
            $scadenzaCliente->setIdTableScadenzeChiuse(self::TABELLA_SCADENZE_CHIUSE_INCASSO_MODIFICA);
            $dettaglioRegistrazione->setIdTablePagina(self::TABELLA_DETTAGLI_INCASSO_MODIFICA);
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_INCASSO_MODIFICA)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_INCASSO_MODIFICA));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_REGISTRAZIONE_INCASSO_MODIFICA));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_INCASSO_MODIFICA));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_INCASSO_MODIFICA));
            $registrazione->setIdFornitore(self::EMPTYSTRING);
            $registrazione->setIdCliente($this->getParmFromRequest(self::CODICE_CLIENTE_INCASSO_MODIFICA));
            $registrazione->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_INCASSO_MODIFICA));
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
            $registrazione->setIdMercato(self::EMPTYSTRING);
        }

        if (null !== $this->getParmFromRequest(self::ID_INCASSO)) {
            $registrazione->setIdRegistrazione($this->getParmFromRequest(self::ID_INCASSO));
            $scadenzaCliente->setIdTableScadenzeAperte(self::SCADENZE_APERTE_INCASSO_MODIFICA);
            $scadenzaCliente->setIdTableScadenzeChiuse(self::SCADENZE_CHIUSE_INCASSO_MODIFICA);
            $dettaglioRegistrazione->setIdTablePagina(self::DETTAGLIO_INCASSO_MODIFICA);
        }

        // aggiungi o rimuovi scadenze in creazione/modifica incasso ===================

        if (null !== $this->getParmFromRequest(self::ID_SCADENZA_CLIENTE)) {
            $scadenzaCliente->setIdScadenza($this->getParmFromRequest(self::ID_SCADENZA_CLIENTE));
            $scadenzaCliente->setIdTableScadenzeAperte($this->getParmFromRequest(self::TABELLA_SCADENZE_APERTE));
            $scadenzaCliente->setIdTableScadenzeChiuse($this->getParmFromRequest(self::TABELLA_SCADENZE_CHIUSE));
        }

        // Registrazione pagamento =====================================================
        // ricerca scadenza aperte fornitore
        
        if (null !== $this->getParmFromRequest(self::FORNITORE_PAGAMENTO_CREAZIONE)) {
            $fornitore->setIdFornitore($this->getParmFromRequest(self::FORNITORE_PAGAMENTO_CREAZIONE));
            $scadenzaFornitore->setCodNegozioSel($this->getParmFromRequest(self::CODICE_NEGOZIO_PAGAMENTO_CREAZIONE));
            $scadenzaFornitore->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_PAGAMENTO_CREAZIONE);
            $scadenzaFornitore->setIdTableScadenzeChiuse(self::TABELLA_SCADENZE_CHIUSE_PAGAMENTO_CREAZIONE);
            $dettaglioRegistrazione->setIdTablePagina(self::DETTAGLI_PAGAMENTO_CREAZIONE);
        }

        // creazione pagamento
        
        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_PAGAMENTO_CREAZIONE)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_PAGAMENTO_CREAZIONE));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_REGISTRAZIONE_PAGAMENTO_CREAZIONE));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_PAGAMENTO_CREAZIONE));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_PAGAMENTO_CREAZIONE));
            $registrazione->setIdFornitore($this->getParmFromRequest(self::FORNITORE_PAGAMENTO_CREAZIONE));
            $registrazione->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_PAGAMENTO_CREAZIONE));
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
            $registrazione->setIdMercato(self::EMPTYSTRING);
            $scadenzaFornitore->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_PAGAMENTO_CREAZIONE);
            $scadenzaFornitore->setIdTableScadenzeChiuse(self::TABELLA_SCADENZE_CHIUSE_PAGAMENTO_CREAZIONE);
        }

        // modifica pagamento
        
        if (null !== $this->getParmFromRequest(self::ID_PAGAMENTO)) {
            $registrazione->setIdRegistrazione($this->getParmFromRequest(self::ID_PAGAMENTO));
            $scadenzaFornitore->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_PAGAMENTO_MODIFICA);
            $scadenzaFornitore->setIdTableScadenzeChiuse(self::TABELLA_SCADENZE_CHIUSE_PAGAMENTO_MODIFICA);
            $dettaglioRegistrazione->setIdTablePagina(self::DETTAGLI_PAGAMENTO_MODIFICA);
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_PAGAMENTO_MODIFICA)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_PAGAMENTO_MODIFICA));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_REGISTRAZIONE_PAGAMENTO_MODIFICA));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CODICE_CAUSALE_PAGAMENTO_MODIFICA));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_PAGAMENTO_MODIFICA));
            $registrazione->setIdFornitore($this->getParmFromRequest(self::FORNITORE_PAGAMENTO_MODIFICA));
            $registrazione->setNumFattura($this->getParmFromRequest(self::NUMERO_FATTURA_PAGAMENTO_MODIFICA));
            $registrazione->setIdCliente(self::EMPTYSTRING);
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
            $registrazione->setIdMercato(self::EMPTYSTRING);
            $scadenzaFornitore->setIdTableScadenzeAperte(self::TABELLA_SCADENZE_APERTE_PAGAMENTO_MODIFICA);
            $scadenzaFornitore->setIdTableScadenzeChiuse(self::TABELLA_SCADENZE_CHIUSE_PAGAMENTO_MODIFICA);
        }

        // aggiungi o rimuovi scadenze in creazione/modifica pagamento
        
        if (null !== $this->getParmFromRequest(self::ID_SCADENZA)) {
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest(self::ID_SCADENZA));
            $scadenzaFornitore->setIdTableScadenzeAperte($this->getParmFromRequest(self::TABELLA_SCADENZE_APERTE));
            $scadenzaFornitore->setIdTableScadenzeChiuse($this->getParmFromRequest(self::TABELLA_SCADENZE_CHIUSE));
        }

        // aggiungi o rimuovi scadenze in creazione/modifica incasso ===================

        if (null !== $this->getParmFromRequest(self::ID_SCADENZA_FORNITORE)) {
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest(self::ID_SCADENZA_FORNITORE));
            $scadenzaFornitore->setIdTableScadenzeAperte($this->getParmFromRequest(self::TABELLA_SCADENZE_APERTE));
            $scadenzaFornitore->setIdTableScadenzeChiuse($this->getParmFromRequest(self::TABELLA_SCADENZE_CHIUSE));
        }

        // Registrazione corrispettivo mercato ==================================================

        if (null !== $this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_CREAZIONE)) {
            $mercato->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_MODIFICA)) {
            $mercato->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_MERCATO_CREAZIONE)) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_MERCATO_CREAZIONE));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest(self::IMPORTO_CORRISPETTIVO_MERCATO_CREAZIONE));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest(self::ALIQUOTA_CORRISPETTIVO_MERCATO_CREAZIONE));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest(self::IVA_CORRISPETTIVO_MERCATO_CREAZIONE));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest(self::IMPONIBILE_CORRISPETTIVO_MERCATO_CREAZIONE));
        }
        
        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_MERCATO_MODIFICA)) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_MERCATO_MODIFICA));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest(self::IMPORTO_CORRISPETTIVO_MERCATO_MODIFICA));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest(self::ALIQUOTA_CORRISPETTIVO_MERCATO_MODIFICA));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest(self::IVA_CORRISPETTIVO_MERCATO_MODIFICA));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest(self::IMPONIBILE_CORRISPETTIVO_MERCATO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_MERCATO_CREAZIONE)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_MERCATO_CREAZIONE));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_CORRISPETTIVO_MERCATO_CREAZIONE));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CAUSALE_CORRISPETTIVO_MERCATO_CREAZIONE));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_CREAZIONE));
            $registrazione->setIdMercato($this->getParmFromRequest(self::MERCATO_CORRISPETTIVO_MERCATO_CREAZIONE));
            $registrazione->setIdFornitore(self::EMPTYSTRING);
            $registrazione->setIdCliente(self::EMPTYSTRING);
            $registrazione->setNumFattura(self::EMPTYSTRING);
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_MERCATO_MODIFICA)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_MERCATO_MODIFICA));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_CORRISPETTIVO_MERCATO_MODIFICA));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CAUSALE_CORRISPETTIVO_MERCATO_MODIFICA));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_MERCATO_MODIFICA));
            $registrazione->setIdMercato($this->getParmFromRequest(self::MERCATO_CORRISPETTIVO_MERCATO_MODIFICA));
            $registrazione->setIdFornitore(self::EMPTYSTRING);
            $registrazione->setIdCliente(self::EMPTYSTRING);
            $registrazione->setNumFattura(self::EMPTYSTRING);
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
        }

        // Registrazione corrispettivo negozio ==================================================

        if (null !== $this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_CREAZIONE)) {
            $mercato->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_MODIFICA)) {
            $mercato->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_NEGOZIO_CREAZIONE)) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest(self::IMPORTO_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest(self::ALIQUOTA_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest(self::IVA_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest(self::IMPONIBILE_CORRISPETTIVO_NEGOZIO_CREAZIONE));
        }

        if (null !== $this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_NEGOZIO_MODIFICA)) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest(self::CODICE_CONTO_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest(self::IMPORTO_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest(self::ALIQUOTA_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest(self::IVA_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest(self::IMPONIBILE_CORRISPETTIVO_NEGOZIO_MODIFICA));
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_NEGOZIO_CREAZIONE)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CAUSALE_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_CREAZIONE));
            $registrazione->setIdFornitore(self::EMPTYSTRING);
            $registrazione->setIdCliente(self::EMPTYSTRING);
            $registrazione->setNumFattura(self::EMPTYSTRING);
            $registrazione->setStaRegistrazione(self::REGISTRAZIONE_APERTA);
            $registrazione->setIdMercato(self::EMPTYSTRING);
        }

        if (null !== $this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_NEGOZIO_MODIFICA)) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest(self::DATA_REGISTRAZIONE_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $registrazione->setDesRegistrazione($this->getParmFromRequest(self::DES_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $registrazione->setCodCausale($this->getParmFromRequest(self::CAUSALE_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $registrazione->setCodNegozio($this->getParmFromRequest(self::CODICE_NEGOZIO_CORRISPETTIVO_NEGOZIO_MODIFICA));
            $registrazione->setIdFornitore(self::EMPTYSTRING);
            $registrazione->setIdCliente(self::EMPTYSTRING);
            $registrazione->setNumFattura(self::EMPTYSTRING);
            $registrazione->setStaRegistrazione(self::EMPTYSTRING);
            $registrazione->setIdMercato(self::EMPTYSTRING);
        }

        // Serializzo in sessione gli oggetti modificati ========================================

        $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
        $_SESSION[self::CAUSALE] = serialize($causale);
        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
        $_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
        $_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
        $_SESSION[self::FORNITORE] = serialize($fornitore);
        $_SESSION[self::CLIENTE] = serialize($cliente);
        $_SESSION[self::MERCATO] = serialize($mercato);

        if ($this->getRequest() == self::START) {
            $this->primanotaFunction->start();
        }
        if ($this->getRequest() == self::GO) {
            $this->primanotaFunction->go();
        }
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

}