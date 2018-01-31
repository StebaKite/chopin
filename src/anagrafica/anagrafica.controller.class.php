<?php

require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'mercato.class.php';

class AnagraficaController {

	public static $anagraficaFunction = null;
	private $request;

	// Oggetti

	const FORNITORE = "Obj_fornitore";
	const CLIENTE = "Obj_cliente";
	const MERCATO = "Obj_mercato";

	// Metodi

	public function __construct(AnagraficaBusinessInterface $anagraficaFunction) {
		$this->anagraficaFunction = $anagraficaFunction;
		$this->setRequest(null);
	}

	public function start() {

		if ($this->getRequest() == null) $this->setRequest($_REQUEST["modo"]);

		$fornitore = Fornitore::getInstance();
		$cliente = Cliente::getInstance();
		$mercato = Mercato::getInstance();

		if (isset($_REQUEST["codforn_cre"]))
		{
			$descrizione = ($_REQUEST["desforn_cre"] != "") ? str_replace("'","''",$_REQUEST["desforn_cre"]) : "" ;
			$fornitore->setDesFornitore($descrizione);
			$indirizzo = ($_REQUEST["indforn_cre"] != "") ? str_replace("'","''",$_REQUEST["indforn_cre"]) : "" ;
			$fornitore->setDesIndirizzoFornitore($indirizzo);
			$citta = ($_REQUEST["cittaforn_cre"] != "") ? str_replace("'","''",$_REQUEST["cittaforn_cre"]) : "" ;
			$fornitore->setDesCittaFornitore($citta);
			$fornitore->setCapFornitore($_REQUEST["capforn_cre"]);
			$fornitore->setTipAddebito($_REQUEST["tipoadd_cre"]);
			$fornitore->setNumGgScadenzaFattura($_REQUEST["ggscadfat_cre"]);
		}

		if (isset($_REQUEST["codforn_mod"]))
		{
			$descrizione = ($_REQUEST["desforn_mod"] != "") ? str_replace("'","''",$_REQUEST["desforn_mod"]) : "" ;
			$fornitore->setDesFornitore($descrizione);
			$indirizzo = ($_REQUEST["indforn_mod"] != "") ? str_replace("'","''",$_REQUEST["indforn_mod"]) : "" ;
			$fornitore->setDesIndirizzoFornitore($indirizzo);
			$citta = ($_REQUEST["cittaforn_mod"] != "") ? str_replace("'","''",$_REQUEST["cittaforn_mod"]) : "" ;
			$fornitore->setDesCittaFornitore($citta);
			$fornitore->setCapFornitore($_REQUEST["capforn_mod"]);
			$fornitore->setTipAddebito($_REQUEST["tipoadd_mod"]);
			$fornitore->setNumGgScadenzaFattura($_REQUEST["ggscadfat_mod"]);
		}

		if (isset($_REQUEST["codcli_cre"])) {
			$cliente->setCatCliente($_REQUEST["catcli_cre"]);
			$cliente->setCodCliente($_REQUEST["codcli_cre"]);
			$cliente->setDesCliente($_REQUEST["descli_cre"]);
			$cliente->setDesIndirizzoCliente($_REQUEST["indcli_cre"]);
			$cliente->setDesCittaCliente($_REQUEST["cittacli_cre"]);
			$cliente->setCapCliente($_REQUEST["capcli_cre"]);
			$cliente->setTipAddebito($_REQUEST["tipoadd_cre"]);
			$cliente->setCodPiva($_REQUEST["pivacli_cre"]);
			$cliente->setCodFisc($_REQUEST["cfiscli_cre"]);
		}

		if (isset($_REQUEST["codcli_mod"])) {
			$cliente->setCatCliente($_REQUEST["catcli_mod"]);
			$cliente->setDesCliente($_REQUEST["descli_mod"]);
			$cliente->setDesIndirizzoCliente($_REQUEST["indcli_mod"]);
			$cliente->setDesCittaCliente($_REQUEST["cittacli_mod"]);
			$cliente->setCapCliente($_REQUEST["capcli_mod"]);
			$cliente->setTipAddebito($_REQUEST["tipoadd_mod"]);
			$cliente->setCodPiva($_REQUEST["pivacli_mod"]);
			$cliente->setCodFisc($_REQUEST["cfiscli_mod"]);
		}

		if (isset($_REQUEST["codmer_cre"])) {
			$mercato->setCodMercato($_REQUEST["codmer_cre"]);
			$mercato->setDesMercato($_REQUEST["desmer_cre"]);
			$mercato->setCittaMercato($_REQUEST["citmer_cre"]);
			$mercato->setCodNegozio($_REQUEST["negmer_cre"]);
		}
		
		if (isset($_REQUEST["codmer_mod"])) {
			$mercato->setCodMercato($_REQUEST["codmer_mod"]);
			$mercato->setDesMercato($_REQUEST["desmer_mod"]);
			$mercato->setCittaMercato($_REQUEST["citmer_mod"]);
			$mercato->setCodNegozio($_REQUEST["negmer_mod"]);
		}
		
		if (isset($_REQUEST["codfisc"])) {
			$cliente->setCodFisc($_REQUEST["codfisc"]);
		}

		if (isset($_REQUEST["codpiva"])) {
			$cliente->setCodPiva($_REQUEST["codpiva"]);
			$cliente->setDesCliente($_REQUEST["descliente"]);
		}

// 		if (isset($_REQUEST["idcliente"])) {
// 			$cliente->setIdCliente($_REQUEST["idcliente"]);
// 		}

// 		if (isset($_REQUEST["idfornitore"])) {
// 			$fornitore->setIdFornitore($_REQUEST["idfornitore"]);
// 		}

 		if (isset($_REQUEST["idmercato"])) {
 			$mercato->setIdMercato($_REQUEST["idmercato"]);
 		}

		// Serializzo in sessione gli oggetti modificati

		$_SESSION[self::FORNITORE] = serialize($fornitore);
		$_SESSION[self::CLIENTE] = serialize($cliente);
		$_SESSION[self::MERCATO] = serialize($mercato);

		if ($this->getRequest() == "start") { $this->anagraficaFunction->start(); }
		if ($this->getRequest() == "go") 	{ $this->anagraficaFunction->go();}
	}

    public function getRequest(){
        return $this->request;
    }

    public function setRequest($request){
        $this->request = $request;
    }

}

?>
