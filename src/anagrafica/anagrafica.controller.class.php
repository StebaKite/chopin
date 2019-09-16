<?php

require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'mercato.class.php';
require_once 'nexus6.abstract.class.php';

class AnagraficaController extends Nexus6Abstract {

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
 
        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
        }

        $fornitore = Fornitore::getInstance();
        $cliente = Cliente::getInstance();
        $mercato = Mercato::getInstance();

        if (null !== filter_input(INPUT_POST, "codforn_cre")) {
            $descrizione = ($this->getParmFromRequest("desforn_cre") != "") ? str_replace("'", "''", $this->getParmFromRequest("desforn_cre")) : "";
            $fornitore->setDesFornitore($descrizione);
            $indirizzo = ($this->getParmFromRequest("indforn_cre") != "") ? str_replace("'", "''", $this->getParmFromRequest("indforn_cre")) : "";
            $fornitore->setDesIndirizzoFornitore($indirizzo);
            $citta = ($this->getParmFromRequest("cittaforn_cre") != "") ? str_replace("'", "''", $this->getParmFromRequest("cittaforn_cre")) : "";
            $fornitore->setDesCittaFornitore($citta);
            $fornitore->setCapFornitore($this->getParmFromRequest("capforn_cre"));
            $fornitore->setTipAddebito($this->getParmFromRequest("tipoadd_cre"));
            $fornitore->setNumGgScadenzaFattura($this->getParmFromRequest("ggscadfat_cre"));
        }

        if (null !== filter_input(INPUT_POST, "codforn_mod")) {
            $descrizione = ($this->getParmFromRequest("desforn_mod") != "") ? str_replace("'", "''", $this->getParmFromRequest("desforn_mod")) : "";
            $fornitore->setDesFornitore($descrizione);
            $indirizzo = ($this->getParmFromRequest("indforn_mod") != "") ? str_replace("'", "''", $this->getParmFromRequest("indforn_mod")) : "";
            $fornitore->setDesIndirizzoFornitore($indirizzo);
            $citta = ($this->getParmFromRequest("cittaforn_mod") != "") ? str_replace("'", "''", $this->getParmFromRequest("cittaforn_mod")) : "";
            $fornitore->setDesCittaFornitore($citta);
            $fornitore->setCapFornitore($this->getParmFromRequest("capforn_mod"));
            $fornitore->setTipAddebito($this->getParmFromRequest("tipoadd_mod"));
            $fornitore->setNumGgScadenzaFattura($this->getParmFromRequest("ggscadfat_mod"));
        }

        if (null !== filter_input(INPUT_POST, "codcli_cre")) {
            $cliente->setCatCliente($this->getParmFromRequest("catcli_cre"));
            $cliente->setCodCliente($this->getParmFromRequest("codcli_cre"));
            $cliente->setDesCliente($this->getParmFromRequest("descli_cre"));
            $cliente->setDesIndirizzoCliente($this->getParmFromRequest("indcli_cre"));
            $cliente->setDesCittaCliente($this->getParmFromRequest("cittacli_cre"));
            $cliente->setCapCliente($this->getParmFromRequest("capcli_cre"));
            $cliente->setTipAddebito($this->getParmFromRequest("tipoadd_cre"));
            $cliente->setCodPiva($this->getParmFromRequest("pivacli_cre"));
            $cliente->setCodFisc($this->getParmFromRequest("cfiscli_cre"));
        }

        if (null !== filter_input(INPUT_POST, "codcli_mod")) {
            $cliente->setCatCliente($this->getParmFromRequest("catcli_mod"));
            $cliente->setDesCliente($this->getParmFromRequest("descli_mod"));
            $cliente->setDesIndirizzoCliente($this->getParmFromRequest("indcli_mod"));
            $cliente->setDesCittaCliente($this->getParmFromRequest("cittacli_mod"));
            $cliente->setCapCliente($this->getParmFromRequest("capcli_mod"));
            $cliente->setTipAddebito($this->getParmFromRequest("tipoadd_mod"));
            $cliente->setCodPiva($this->getParmFromRequest("pivacli_mod"));
            $cliente->setCodFisc($this->getParmFromRequest("cfiscli_mod"));
        }

        if (null !== filter_input(INPUT_POST, "codmer_cre")) {
            $mercato->setCodMercato($this->getParmFromRequest("codmer_cre"));
            $mercato->setDesMercato($this->getParmFromRequest("desmer_cre"));
            $mercato->setCittaMercato($this->getParmFromRequest("citmer_cre"));
            $mercato->setCodNegozio($this->getParmFromRequest("negmer_cre"));
        }

        if (null !== filter_input(INPUT_POST, "codmer_mod")) {
            $mercato->setCodMercato($this->getParmFromRequest("codmer_mod"));
            $mercato->setDesMercato($this->getParmFromRequest("desmer_mod"));
            $mercato->setCittaMercato($this->getParmFromRequest("citmer_mod"));
            $mercato->setCodNegozio($this->getParmFromRequest("negmer_mod"));
        }

        if (null !== filter_input(INPUT_POST, "codfisc")) {
            $cliente->setCodFisc($this->getParmFromRequest("codfisc"));
        }

        if (null !== filter_input(INPUT_POST, "codpiva")) {
            $cliente->setCodPiva($this->getParmFromRequest("codpiva"));
            $cliente->setDesCliente($this->getParmFromRequest("descliente"));
        }

        if (null !== filter_input(INPUT_POST, "idcliente")) {
            $cliente->setIdCliente($this->getParmFromRequest("idcliente"));
        }

        if (null !== filter_input(INPUT_POST, "idfornitore")) {
            $fornitore->setIdFornitore($this->getParmFromRequest("idfornitore"));
        }

        if (null !== filter_input(INPUT_POST, "idmercato")) {
            $mercato->setIdMercato($this->getParmFromRequest("idmercato"));
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::FORNITORE] = serialize($fornitore);
        $_SESSION[self::CLIENTE] = serialize($cliente);
        $_SESSION[self::MERCATO] = serialize($mercato);

        if ($this->getRequest() == "start") {
            $this->anagraficaFunction->start();
        }
        if ($this->getRequest() == "go") {
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