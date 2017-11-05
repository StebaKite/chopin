<?php

require_once 'registrazione.class.php';
require_once 'causale.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'mercato.class.php';

class PrimanotaController
{
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
			if (isset($_REQUEST["modo"])) $this->setRequest($_REQUEST["modo"]);
			else $this->setRequest("start");
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

		if (isset($_REQUEST["datareg_da"])) {
			$registrazione->setDatRegistrazioneDa($_REQUEST["datareg_da"]);
			$registrazione->setDatRegistrazioneA($_REQUEST["datareg_a"]);
			$registrazione->setCodNegozioSel($_REQUEST["codneg_sel"]);
			$registrazione->setCodCausaleSel($_REQUEST["causale"]);
			$causale->setCodCausale($_REQUEST["causale"]);
		}

		if (isset($_REQUEST["importo"])) {
			$dettaglioRegistrazione->setCodConto($_REQUEST["codconto"]);
			$dettaglioRegistrazione->setCodSottoconto($_REQUEST["codsottoconto"]);
			$dettaglioRegistrazione->setImpRegistrazione($_REQUEST["importo"]);
			$dettaglioRegistrazione->setIdDettaglioRegistrazione($_REQUEST["iddettaglio"]);
		}

		if (isset($_REQUEST["dareAvere"])) {
			$dettaglioRegistrazione->setCodConto($_REQUEST["codconto"]);
			$dettaglioRegistrazione->setCodSottoconto($_REQUEST["codsottoconto"]);
			$dettaglioRegistrazione->setIndDareavere(strtoupper($_REQUEST["dareAvere"]));
			$dettaglioRegistrazione->setIdDettaglioRegistrazione($_REQUEST["iddettaglio"]);
		}

		if (isset($_REQUEST["codconto"])) {
			$dettaglioRegistrazione->setCodConto($_REQUEST["codconto"]);
		}

		if (isset($_REQUEST["causale_cre"])) {
			$causale->setCodCausale($_REQUEST["causale_cre"]);
		}

		if (isset($_REQUEST["causale"])) {
			$causale->setCodCausale($_REQUEST["causale"]);
		}

		if (isset($_REQUEST["datareg"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg"]);
		}

		if (isset($_REQUEST["datareg_cre"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg_cre"]);
			$registrazione->setDesRegistrazione($_REQUEST["descreg_cre"]);
			$registrazione->setCodCausale($_REQUEST["causale_cre"]);
			$registrazione->setCodNegozio($_REQUEST["codneg_cre"]);
			$registrazione->setDesFornitore($_REQUEST["fornitore_cre"]);
			$registrazione->setDesCliente($_REQUEST["cliente_cre"]);
			$registrazione->setNumFattura($_REQUEST["numfatt_cre"]);
			$registrazione->setStaRegistrazione("00");
			$registrazione->setIdMercato("");
		}

		if (isset($_REQUEST["desfornitore"])) {
			$registrazione->setDesFornitore($_REQUEST["desfornitore"]);
			$registrazione->setNumFattura($_REQUEST["numfatt"]);
			$registrazione->setDatRegistrazione($_REQUEST["datareg"]);
		}

		if (isset($_REQUEST["descliente"])) {
			$registrazione->setDesCliente($_REQUEST["descliente"]);
			$registrazione->setNumFattura($_REQUEST["numfatt"]);
			$registrazione->setDatRegistrazione($_REQUEST["datareg"]);
		}

		if (isset($_REQUEST["datascad_for"])) {
			$fornitore->setDesFornitore($_REQUEST["fornitore"]);
			$scadenzaFornitore->setIdFornitore($_REQUEST["idfornitore"]);
			$scadenzaFornitore->setDatScadenza($_REQUEST["datascad_for"]);
			$scadenzaFornitore->setImpInScadenza($_REQUEST["impscad_for"]);
			$scadenzaFornitore->setNumFattura($_REQUEST["numfatt"]);
		}

		if (isset($_REQUEST["datascad_cli"])) {
			$cliente->setDesCliente($_REQUEST["cliente"]);
			$scadenzaCliente->setIdCliente($_REQUEST["idcliente"]);
			$scadenzaCliente->setDatRegistrazione($_REQUEST["datascad_cli"]);
			$scadenzaCliente->setImpRegistrazione($_REQUEST["impscad_cli"]);
			$scadenzaCliente->setNumFattura($_REQUEST["numfatt"]);
		}

		if (isset($_REQUEST["idreg"])) {
			$registrazione->setIdRegistrazione($_REQUEST["idreg"]);
		}

		if (isset($_REQUEST["datareg_mod"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg_mod"]);
			$registrazione->setDesRegistrazione($_REQUEST["descreg_mod"]);
			$registrazione->setCodCausale($_REQUEST["causale_mod"]);
			$registrazione->setCodNegozio($_REQUEST["codneg_mod"]);
			$registrazione->setDesFornitore($_REQUEST["fornitore_mod"]);
			$registrazione->setDesCliente($_REQUEST["cliente_mod"]);
			$registrazione->setNumFattura($_REQUEST["numfatt_mod"]);
			$registrazione->setNumFatturaOrig($_REQUEST["numfatt_mod_orig"]);
		}

		// Registrazione incasso ==========================================================

		if (isset($_REQUEST["descliente_inc_cre"])) {
			$cliente->setDesCliente($_REQUEST["descliente_inc_cre"]);
			$scadenzaCliente->setCodNegozioSel($_REQUEST["codnegozio_inc_cre"]);
		}

		if (isset($_REQUEST["datareg_inc_cre"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg_inc_cre"]);
			$registrazione->setDesRegistrazione($_REQUEST["descreg_inc_cre"]);
			$registrazione->setCodCausale($_REQUEST["causale_inc_cre"]);
			$registrazione->setCodNegozio($_REQUEST["codneg_inc_cre"]);
			$registrazione->setDesCliente($_REQUEST["cliente_inc_cre"]);
			$registrazione->setNumFattura($_REQUEST["numfatt_inc_cre"]);
			$registrazione->setStaRegistrazione("00");
			$registrazione->setIdMercato("");
		}

		// Registrazione pagamento =====================================================

		// ricerca scadenza aperte fornitore
		if (isset($_REQUEST["desfornitore_pag_cre"])) {
			$fornitore->setDesFornitore($_REQUEST["desfornitore_pag_cre"]);
			$scadenzaFornitore->setCodNegozioSel($_REQUEST["codnegozio_pag_cre"]);
			$scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_cre");
			$scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_cre");
			$dettaglioRegistrazione->setIdTablePagina("dettagli_pag_cre");			
		}

		// creazione pagamento
		if (isset($_REQUEST["datareg_pag_cre"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg_pag_cre"]);
			$registrazione->setDesRegistrazione($_REQUEST["descreg_pag_cre"]);
			$registrazione->setCodCausale($_REQUEST["causale_pag_cre"]);
			$registrazione->setCodNegozio($_REQUEST["codneg_pag_cre"]);
			$registrazione->setDesCliente($_REQUEST["cliente_pag_cre"]);
			$registrazione->setNumFattura($_REQUEST["numfatt_pag_cre"]);
			$registrazione->setStaRegistrazione("00");
			$registrazione->setIdMercato("");
			$scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_cre");
			$scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_cre");
		}
		
		// modifica pagamento
		if (isset($_REQUEST["idpag"])) {
		    $registrazione->setIdRegistrazione($_REQUEST["idpag"]);
		    $scadenzaFornitore->setIdTableScadenzeAperte("scadenze_aperte_pag_mod");
		    $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_mod");
		    $dettaglioRegistrazione->setIdTablePagina("dettagli_pag_mod");		    
		}
		
		if (isset($_REQUEST["datareg_pag_mod"])) {
		    $registrazione->setDatRegistrazione($_REQUEST["datareg_pag_mod"]);
		    $registrazione->setDesRegistrazione($_REQUEST["descreg_pag_mod"]);
		    $registrazione->setCodCausale($_REQUEST["causale_pag_mod"]);
		    $registrazione->setCodNegozio($_REQUEST["codneg_pag_mod"]);
		    $registrazione->setDesFornitore($_REQUEST["fornitore_pag_mod"]);
		    $registrazione->setNumFattura($_REQUEST["numfatt_pag_mod"]);
		}

		// aggiungi o rimuovi scadenze in creazione/modifica pagamento
		if (isset($_REQUEST["idscad"])) {
		    $scadenzaFornitore->setIdScadenza($_REQUEST["idscad"]);
		    $scadenzaFornitore->setIdTableScadenzeAperte($_REQUEST["idtableaperte"]);
		    $scadenzaFornitore->setIdTableScadenzeChiuse($_REQUEST["idtablechiuse"]);
		}
		
		// Registrazione corrispettivo mercato ==================================================

		if (isset($_REQUEST["codneg_cormer_cre"])) {
			$mercato->setCodNegozio($_REQUEST["codneg_cormer_cre"]);
		}

		if (isset($_REQUEST["codconto_cormer_cre"])) {
			$dettaglioRegistrazione->setCodConto($_REQUEST["codconto_cormer_cre"]);
			$dettaglioRegistrazione->setImpRegistrazione($_REQUEST["importo_cormer_cre"]);
			$dettaglioRegistrazione->setAliquota($_REQUEST["aliquota_cormer_cre"]);
			$dettaglioRegistrazione->setImpIva($_REQUEST["iva_cormer_cre"]);
			$dettaglioRegistrazione->setImponibile($_REQUEST["imponibile_cormer_cre"]);
		}

		if (isset($_REQUEST["datareg_cormer_cre"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg_cormer_cre"]);
			$registrazione->setDesRegistrazione($_REQUEST["descreg_cormer_cre"]);
			$registrazione->setCodCausale($_REQUEST["causale_cormer_cre"]);
			$registrazione->setCodNegozio($_REQUEST["codneg_cormer_cre"]);
			$registrazione->setDesCliente("");
			$registrazione->setNumFattura("");
			$registrazione->setStaRegistrazione("00");
			$registrazione->setIdMercato($_REQUEST["mercati_cormer_cre"]);
		}

		// Registrazione corrispettivo negozio ==================================================

		if (isset($_REQUEST["codconto_corneg_cre"])) {
			$dettaglioRegistrazione->setCodConto($_REQUEST["codconto_corneg_cre"]);
			$dettaglioRegistrazione->setImpRegistrazione($_REQUEST["importo_corneg_cre"]);
			$dettaglioRegistrazione->setAliquota($_REQUEST["aliquota_corneg_cre"]);
			$dettaglioRegistrazione->setImpIva($_REQUEST["iva_corneg_cre"]);
			$dettaglioRegistrazione->setImponibile($_REQUEST["imponibile_corneg_cre"]);
		}

		if (isset($_REQUEST["datareg_corneg_cre"])) {
			$registrazione->setDatRegistrazione($_REQUEST["datareg_corneg_cre"]);
			$registrazione->setDesRegistrazione($_REQUEST["descreg_corneg_cre"]);
			$registrazione->setCodCausale($_REQUEST["causale_corneg_cre"]);
			$registrazione->setCodNegozio($_REQUEST["codneg_corneg_cre"]);
			$registrazione->setDesCliente("");
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

		if ($this->getRequest() == "start") { $this->primanotaFunction->start(); }
		if ($this->getRequest() == "go") 	{ $this->primanotaFunction->go();}
	}

	public function getRequest(){
		return $this->request;
	}

	public function setRequest($request){
		$this->request = $request;
	}
}

?>