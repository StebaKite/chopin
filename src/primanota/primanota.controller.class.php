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

    // Oggetti

    const REGISTRAZIONE = "Obj_registrazione";
    const DETTAGLIO_REGISTRAZIONE = "Obj_dettaglioregistrazione";
    const CAUSALE = "Obj_causale";
    const SCADENZA_FORNITORE = "Obj_scadenzafornitore";
    const SCADENZA_CLIENTE = "Obj_scadenzacliente";
    const FORNITORE = "Obj_fornitore";
    const CLIENTE = "Obj_cliente";
    const MERCATO = "Obj_mercato";

    // Metodi

    public function __construct(PrimanotaBusinessInterface $primanotaFunction) {
        $this->primanotaFunction = $primanotaFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
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

        if (null !== filter_input(INPUT_POST, "datareg_da")) {
            $registrazione->setDatRegistrazioneDa($this->getParmFromRequest("datareg_da"));
            $registrazione->setDatRegistrazioneA($this->getParmFromRequest("datareg_a"));
            $registrazione->setCodNegozioSel($this->getParmFromRequest("codneg_sel"));
            $registrazione->setCodCausaleSel($this->getParmFromRequest("causale"));
            $causale->setCodCausale($this->getParmFromRequest("causale"));
        }

        if (null !== filter_input(INPUT_POST, "importo")) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest("codconto"));
            $dettaglioRegistrazione->setCodContoComposto($this->getParmFromRequest("codconto"));
            $dettaglioRegistrazione->setCodSottoconto($this->getParmFromRequest("codsottoconto"));
            $dettaglioRegistrazione->setIndDareavere(strtoupper($this->getParmFromRequest("dareAvere")));            
            $dettaglioRegistrazione->setImpRegistrazione(str_replace(",", ".", $this->getParmFromRequest("importo")));
            $dettaglioRegistrazione->setIdDettaglioRegistrazione($this->getParmFromRequest("iddettaglio"));
        }

        if (null !== filter_input(INPUT_POST, "importo_dettaglio")) {
            $scadenzaFornitore->setImpInScadenza(str_replace(",", ".", $this->getParmFromRequest("importo_dettaglio")));
            $scadenzaCliente->setImportoScadenza(str_replace(",", ".", $this->getParmFromRequest("importo_dettaglio")));
        }

        if (null !== filter_input(INPUT_POST, "dareAvere")) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest("codconto"));
            $dettaglioRegistrazione->setCodSottoconto($this->getParmFromRequest("codsottoconto"));
            $dettaglioRegistrazione->setIndDareavere(strtoupper($this->getParmFromRequest("dareAvere")));
            $dettaglioRegistrazione->setIdDettaglioRegistrazione($this->getParmFromRequest("iddettaglio"));
        }

        if (null !== filter_input(INPUT_POST, "codconto")) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest("codconto"));
            $dettaglioRegistrazione->setIndDareAvere(strtoupper($this->getParmFromRequest("dareAvere")));
        }

        if (null !== filter_input(INPUT_POST, "causale")) {
            $causale->setCodCausale($this->getParmFromRequest("causale"));
        }

        if (null !== filter_input(INPUT_POST, "datareg")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg"));
        }

        if (null !== filter_input(INPUT_POST, "datareg_cre")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_cre"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_cre"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_cre"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_cre"));
            $registrazione->setIdFornitore($this->getParmFromRequest("fornitore_cre"));
            $registrazione->setIdCliente($this->getParmFromRequest("cliente_cre"));
            $registrazione->setNumFattura($this->getParmFromRequest("numfatt_cre"));
            $registrazione->setStaRegistrazione("00");
            $registrazione->setIdMercato("");
            $scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_cre");
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenzesuppl_cre");
        }

        if (null !== filter_input(INPUT_POST, "idfornitore")) {
            $scadenzaFornitore->setIdFornitore($this->getParmFromRequest("idfornitore"));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest("numfatt"));
            $scadenzaFornitore->setDatScadenza($this->getParmFromRequest("datareg"));
            $registrazione->setIdFornitore($this->getParmFromRequest("idfornitore"));
            $registrazione->setCodCausale($this->getParmFromRequest("codCausale"));            
            $scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_cre");
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenzesuppl_cre");
        }

        if (null !== filter_input(INPUT_POST, "idcliente")) {
            $scadenzaCliente->setIdCliente($this->getParmFromRequest("idcliente"));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest("numfatt"));
            $scadenzaCliente->setDatRegistrazione($this->getParmFromRequest("datareg"));
            $registrazione->setIdCliente($this->getParmFromRequest("idcliente"));
        }

        if (null !== filter_input(INPUT_POST, "datascad_for")) {
            $scadenzaFornitore->setIdFornitore($this->getParmFromRequest("idfornitore"));
            $scadenzaFornitore->setDatScadenza($this->getParmFromRequest("datascad_for"));
            $scadenzaFornitore->setDatScadenzaNuova($this->getParmFromRequest("datascad_new"));
            $scadenzaFornitore->setImpInScadenza(str_replace(",", ".", $this->getParmFromRequest("impscad_for")));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest("numfatt"));
        }

        if (null !== filter_input(INPUT_POST, "datascad_cli")) {
            $scadenzaCliente->setIdCliente($this->getParmFromRequest("idcliente"));
            $scadenzaCliente->setDatRegistrazione($this->getParmFromRequest("datascad_cli"));
            $scadenzaCliente->setImpRegistrazione(str_replace(",", ".", $this->getParmFromRequest("impscad_cli")));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest("numfatt"));
        }

        if (null !== filter_input(INPUT_POST, "datascad_new_for")) {
            $scadenzaFornitore->setDatScadenza($this->getParmFromRequest("datascad_old_for"));
            $scadenzaFornitore->setDatScadenzaNuova($this->getParmFromRequest("datascad_new_for"));
            $scadenzaFornitore->setNumFattura($this->getParmFromRequest("numfatt"));
        }

        if (null !== filter_input(INPUT_POST, "datascad_new_cli")) {
            $scadenzaCliente->setDatRegistrazione($this->getParmFromRequest("datascad_old_cli"));
            $scadenzaCliente->setDatScadenzaNuova($this->getParmFromRequest("datascad_new_cli"));
            $scadenzaCliente->setNumFattura($this->getParmFromRequest("numfatt"));
        }
        
        if (null !== filter_input(INPUT_POST, "idreg")) {
            $registrazione->setIdRegistrazione($this->getParmFromRequest("idreg"));
        }

        if (null !== filter_input(INPUT_POST, "datareg_mod")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_mod"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_mod"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_mod"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_mod"));
            $registrazione->setIdFornitore($this->getParmFromRequest("fornitore_mod"));
            $registrazione->setIdCliente($this->getParmFromRequest("cliente_mod"));
            $registrazione->setNumFattura($this->getParmFromRequest("numfatt_mod"));
            $registrazione->setNumFatturaOrig($this->getParmFromRequest("numfatt_mod_orig"));
            $scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_mod");
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenzesuppl_mod");
        }

        if (null !== filter_input(INPUT_POST, "newcontodett_cre")) {
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest("newimpdett_cre"));
            $dettaglioRegistrazione->setIndDareavere($this->getParmFromRequest("newsegnodett_cre"));
            $temp = explode(" - ", $this->getParmFromRequest("newcontodett_cre"));      // Conto - descrizione
            $cc = explode(".", $temp[0]);   // conto.sottoconto
            $dettaglioRegistrazione->setCodConto($cc[0]);   // conto
            $dettaglioRegistrazione->setCodSottoconto($cc[1]);  // sottoconto
        }
        
        if (null !== filter_input(INPUT_POST, "scadenzeTable")) {
            $scadenzaCliente->setScadenzeTable($this->getParmFromRequest("scadenzeTable"));
        }
        
        // Registrazione incasso ==========================================================

        if (null !== filter_input(INPUT_POST, "cliente_inc_cre")) {
            $cliente->setIdCliente($this->getParmFromRequest("cliente_inc_cre"));
            $scadenzaCliente->setCodNegozioSel($this->getParmFromRequest("codnegozio_inc_cre"));
            $scadenzaCliente->setIdTableScadenzeAperte("scadenze_aperte_inc_cre");
            $scadenzaCliente->setIdTableScadenzeChiuse("scadenze_chiuse_inc_cre");
            $dettaglioRegistrazione->setIdTablePagina("dettagli_inc_cre");
        }

        if (null !== filter_input(INPUT_POST, "datareg_inc_cre")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_inc_cre"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_inc_cre"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_inc_cre"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_inc_cre"));
            $registrazione->setIdCliente($this->getParmFromRequest("cliente_inc_cre"));
            $registrazione->setNumFattura($this->getParmFromRequest("numfatt_inc_cre"));
            $registrazione->setStaRegistrazione("00");
            $registrazione->setIdMercato("");
        }

        // modifica incasso ===============================================================

        if (null !== filter_input(INPUT_POST, "cliente_inc_mod")) {
            $cliente->setIdCliente($this->getParmFromRequest("cliente_inc_mod"));
            $scadenzaCliente->setCodNegozioSel($this->getParmFromRequest("codnegozio_inc_mod"));
            $scadenzaCliente->setIdTableScadenzeAperte("scadenze_aperte_inc_mod");
            $scadenzaCliente->setIdTableScadenzeChiuse("scadenze_chiuse_inc_mod");
            $dettaglioRegistrazione->setIdTablePagina("dettagli_inc_mod");
        }

        if (null !== filter_input(INPUT_POST, "datareg_inc_mod")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_inc_mod"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_inc_mod"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_inc_mod"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_inc_mod"));
            $registrazione->setIdFornitore(" ");
            $registrazione->setIdCliente($this->getParmFromRequest("cliente_inc_mod"));
            $registrazione->setNumFattura($this->getParmFromRequest("numfatt_inc_mod"));
            $registrazione->setStaRegistrazione("00");
            $registrazione->setIdMercato("");
        }

        if (null !== filter_input(INPUT_POST, "idinc")) {
            $registrazione->setIdRegistrazione($this->getParmFromRequest("idinc"));
            $scadenzaCliente->setIdTableScadenzeAperte("scadenze_aperte_inc_mod");
            $scadenzaCliente->setIdTableScadenzeChiuse("scadenze_chiuse_inc_mod");
            $dettaglioRegistrazione->setIdTablePagina("dettagli_inc_mod");
        }

        // aggiungi o rimuovi scadenze in creazione/modifica incasso ===================

        if (null !== filter_input(INPUT_POST, "idscadcli")) {
            $scadenzaCliente->setIdScadenza($this->getParmFromRequest("idscadcli"));
            $scadenzaCliente->setIdTableScadenzeAperte($this->getParmFromRequest("idtableaperte"));
            $scadenzaCliente->setIdTableScadenzeChiuse($this->getParmFromRequest("idtablechiuse"));
        }

        // Registrazione pagamento =====================================================
        // ricerca scadenza aperte fornitore
        
        if (null !== filter_input(INPUT_POST, "fornitore_pag_cre")) {
            $fornitore->setIdFornitore($this->getParmFromRequest("fornitore_pag_cre"));
            $scadenzaFornitore->setCodNegozioSel($_REQUEST["codnegozio_pag_cre"]);
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_cre");
            $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_cre");
            $dettaglioRegistrazione->setIdTablePagina("dettagli_pag_cre");
        }

        // creazione pagamento
        
        if (null !== filter_input(INPUT_POST, "datareg_pag_cre")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_pag_cre"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_pag_cre"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_pag_cre"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_pag_cre"));
            $registrazione->setIdFornitore($this->getParmFromRequest("fornitore_pag_cre"));
            $registrazione->setNumFattura($this->getParmFromRequest("numfatt_pag_cre"));
            $registrazione->setStaRegistrazione("00");
            $registrazione->setIdMercato("");
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_cre");
            $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_cre");
        }

        // modifica pagamento
        
        if (null !== filter_input(INPUT_POST, "idpag")) {
            $registrazione->setIdRegistrazione($this->getParmFromRequest("idpag"));
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_mod");
            $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_mod");
            $dettaglioRegistrazione->setIdTablePagina("dettagli_pag_mod");
        }

        if (null !== filter_input(INPUT_POST, "datareg_pag_mod")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_pag_mod"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_pag_mod"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_pag_mod"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_pag_mod"));
            $registrazione->setIdFornitore($this->getParmFromRequest("fornitore_pag_mod"));
            $registrazione->setNumFattura($this->getParmFromRequest("numfatt_pag_mod"));
            $registrazione->setIdCliente(" ");
            $registrazione->setStaRegistrazione("00");
            $registrazione->setIdMercato("");
            $scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_mod");
            $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_mod");
        }

        // aggiungi o rimuovi scadenze in creazione/modifica pagamento
        
        if (null !== filter_input(INPUT_POST, "idscad")) {
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest("idscad"));
            $scadenzaFornitore->setIdTableScadenzeAperte($this->getParmFromRequest("idtableaperte"));
            $scadenzaFornitore->setIdTableScadenzeChiuse($this->getParmFromRequest("idtablechiuse"));
        }

        // aggiungi o rimuovi scadenze in creazione/modifica incasso ===================

        if (null !== filter_input(INPUT_POST, "idscadfor")) {
            $scadenzaFornitore->setIdScadenza($this->getParmFromRequest("idscadfor"));
            $scadenzaFornitore->setIdTableScadenzeAperte($this->getParmFromRequest("idtableaperte"));
            $scadenzaFornitore->setIdTableScadenzeChiuse($this->getParmFromRequest("idtablechiuse"));
        }

        // Registrazione corrispettivo mercato ==================================================

        if (null !== filter_input(INPUT_POST, "codneg_cormer_cre")) {
            $mercato->setCodNegozio($this->getParmFromRequest("codneg_cormer_cre"));
        }

        if (null !== filter_input(INPUT_POST, "codneg_cormer_mod")) {
            $mercato->setCodNegozio($this->getParmFromRequest("codneg_cormer_mod"));
        }

        if (null !== filter_input(INPUT_POST, "codconto_cormer_cre")) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest("codconto_cormer_cre"));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest("importo_cormer_cre"));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest("aliquota_cormer_cre"));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest("iva_cormer_cre"));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest("imponibile_cormer_cre"));
        }
        
        if (null !== filter_input(INPUT_POST, "codconto_cormer_mod")) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest("codconto_cormer_mod"));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest("importo_cormer_mod"));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest("aliquota_cormer_mod"));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest("iva_cormer_mod"));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest("imponibile_cormer_mod"));
        }

        if (null !== filter_input(INPUT_POST, "datareg_cormer_cre")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_cormer_cre"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_cormer_cre"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_cormer_cre"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_cormer_cre"));
            $registrazione->setIdMercato($this->getParmFromRequest("mercato_cormer_cre"));
            $registrazione->setIdFornitore(" ");
            $registrazione->setIdCliente(" ");
            $registrazione->setNumFattura("");
            $registrazione->setStaRegistrazione("00");
        }

        if (null !== filter_input(INPUT_POST, "datareg_cormer_mod")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_cormer_mod"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_cormer_mod"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_cormer_mod"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_cormer_mod"));
            $registrazione->setIdMercato($this->getParmFromRequest("mercato_cormer_mod"));
            $registrazione->setIdFornitore(" ");
            $registrazione->setIdCliente(" ");
            $registrazione->setNumFattura("");
            $registrazione->setStaRegistrazione("00");
        }

        // Registrazione corrispettivo negozio ==================================================

        if (null !== filter_input(INPUT_POST, "codneg_corneg_cre")) {
            $mercato->setCodNegozio($this->getParmFromRequest("codneg_corneg_cre"));
        }

        if (null !== filter_input(INPUT_POST, "codneg_cormer_mod")) {
            $mercato->setCodNegozio($this->getParmFromRequest("codneg_cormer_mod"));
        }

        if (null !== filter_input(INPUT_POST, "codconto_corneg_cre")) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest("codconto_corneg_cre"));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest("importo_corneg_cre"));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest("aliquota_corneg_cre"));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest("iva_corneg_cre"));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest("imponibile_corneg_cre"));
        }

        if (null !== filter_input(INPUT_POST, "codconto_corneg_mod")) {
            $dettaglioRegistrazione->setCodConto($this->getParmFromRequest("codconto_corneg_mod"));
            $dettaglioRegistrazione->setImpRegistrazione($this->getParmFromRequest("importo_corneg_mod"));
            $dettaglioRegistrazione->setAliquota($this->getParmFromRequest("aliquota_corneg_mod"));
            $dettaglioRegistrazione->setImpIva($this->getParmFromRequest("iva_corneg_mod"));
            $dettaglioRegistrazione->setImponibile($this->getParmFromRequest("imponibile_corneg_mod"));
        }

        if (null !== filter_input(INPUT_POST, "datareg_corneg_cre")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_corneg_cre"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_corneg_cre"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_corneg_cre"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_corneg_cre"));
            $registrazione->setIdFornitore(" ");
            $registrazione->setIdCliente(" ");
            $registrazione->setNumFattura("");
            $registrazione->setStaRegistrazione("00");
            $registrazione->setIdMercato("");
        }

        if (null !== filter_input(INPUT_POST, "datareg_corneg_mod")) {
            $registrazione->setDatRegistrazione($this->getParmFromRequest("datareg_corneg_mod"));
            $registrazione->setDesRegistrazione($this->getParmFromRequest("descreg_corneg_mod"));
            $registrazione->setCodCausale($this->getParmFromRequest("causale_corneg_mod"));
            $registrazione->setCodNegozio($this->getParmFromRequest("codneg_corneg_mod"));
            $registrazione->setIdFornitore(" ");
            $registrazione->setIdCliente(" ");
            $registrazione->setNumFattura("");
            $registrazione->setStaRegistrazione("00");
            $registrazione->setIdMercato("");
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

        if ($this->getRequest() == "start") {
            $this->primanotaFunction->start();
        }
        if ($this->getRequest() == "go") {
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