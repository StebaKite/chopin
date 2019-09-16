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

    // Oggetti

    const CONTO = "Obj_conto";
    const SOTTOCONTO = "Obj_sottoconto";
    const CAUSALE = "Obj_causale";
    const CONFIGURAZIONE_CAUSALE = "Obj_configurazionecausale";
    const PROGRESSIVO_FATTURA = "Obj_progressivofattura";

    // Metodi

    public function __construct(ConfigurazioniBusinessInterface $configurazioniFunction) {
        $this->configurazioniFunction = $configurazioniFunction;
        $this->setRequest(null);
    }

    public function start() {

        if ($this->getRequest() == null) {
            $this->setRequest($this->getParmFromRequest("modo"));
        } else {
            $this->setRequest("start");         // default set
        }

        $conto = Conto::getInstance();
        $sottoconto = Sottoconto::getInstance();
        $causale = Causale::getInstance();
        $configurazioneCausale = ConfigurazioneCausale::getInstance();
        $progressivoFattura = ProgressivoFattura::getInstance();

        // Conti --------------------------------------------------

        $conto->setCodConto($this->getParmFromRequest("codconto"));
        
        if (null !== filter_input(INPUT_POST, "catconto_sel")) {
            $conto->setCatContoSel($this->getParmFromRequest("catconto_sel"));
            $conto->setTipContoSel($this->getParmFromRequest("tipconto_sel"));
        }
        
        if (null !== filter_input(INPUT_POST, "codsottoconto")) {
            $sottoconto->setCodConto($this->getParmFromRequest("codconto"));
            $sottoconto->setCodSottoconto($this->getParmFromRequest("codsottoconto"));
            $sottoconto->setDesSottoconto($this->getParmFromRequest("dessottoconto"));
        }        

        if (null !== filter_input(INPUT_POST, "codconto_cre")) {
            $conto->setCodConto($this->getParmFromRequest("codconto_cre"));
            $conto->setDesConto($this->getParmFromRequest("desconto_cre"));
            $conto->setCatConto($this->getParmFromRequest("catconto_cre"));
            $conto->setTipConto($this->getParmFromRequest("dareavere_cre"));
            $conto->setIndPresenzaInBilancio($this->getParmFromRequest("indpresenza_cre"));
            $conto->setIndVisibilitaSottoconti($this->getParmFromRequest("indvissottoconti_cre"));
            $conto->setNumRigaBilancio($this->getParmFromRequest("numrigabilancio_cre"));
        }
        
        if (null !== filter_input(INPUT_POST, "codconto_mod")) {
            $conto->setCodConto($this->getParmFromRequest("codconto_mod"));
            $conto->setDesConto($this->getParmFromRequest("desconto_mod"));
            $conto->setCatConto($this->getParmFromRequest("catconto_mod"));
            $conto->setTipConto($this->getParmFromRequest("dareavere_mod"));
            $conto->setIndPresenzaInBilancio($this->getParmFromRequest("indpresenza_mod"));
            $conto->setIndVisibilitaSottoconti($this->getParmFromRequest("indvissottoconti_mod"));
            $conto->setNumRigaBilancio($this->getParmFromRequest("numrigabilancio_mod"));
        }

        if (null !== filter_input(INPUT_POST, "codconto_modgru")) {
            $sottoconto->setCodConto($this->getParmFromRequest("codconto_modgru"));
            $sottoconto->setCodSottoconto($this->getParmFromRequest("codsottoconto_modgru"));
            $sottoconto->setIndGruppo($this->getParmFromRequest("indgruppo_modgru"));
        }

        if (null !== filter_input(INPUT_POST, "codsottoconto_new")) {
            $sottoconto->setCodSottoconto($this->getParmFromRequest("codsottoconto_new"));
            $sottoconto->setDesSottoconto($this->getParmFromRequest("dessottoconto_new"));
            $sottoconto->setIndGruppo($this->getParmFromRequest("indgruppo_new"));
        }

        if (null !== filter_input(INPUT_POST, "codsottoconto_del")) {
            $sottoconto->setCodConto($this->getParmFromRequest("codconto_del"));
            $sottoconto->setCodSottoconto($this->getParmFromRequest("codsottoconto_del"));
        }

        if (null !== filter_input(INPUT_POST, "csot_mov")) {
            $sottoconto->setDataRegistrazioneDa($this->getParmFromRequest("dtda_mov"));
            $sottoconto->setDataRegistrazioneA($this->getParmFromRequest("dta_mov"));
            $sottoconto->setCodConto($this->getParmFromRequest("ccon_mov"));
            $sottoconto->setCodSottoconto($this->getParmFromRequest("csot_mov"));
            $sottoconto->setCodNegozio($this->getParmFromRequest("cneg_mov"));
            $sottoconto->setSaldiInclusi($this->getParmFromRequest("sal_mov"));
        }

        if (null !== filter_input(INPUT_POST, "datareg_da")) {
            $conto->setCatConto($this->getParmFromRequest("catconto"));
            $conto->setDesConto($this->getParmFromRequest("desconto"));
            $sottoconto->setDesSottoconto($this->getParmFromRequest("dessottoconto"));
        }

        // Causali ---------------------------------------

        if (null !== filter_input(INPUT_POST, "codcausale_cre")) {
            $causale->setCodCausale($this->getParmFromRequest("codcausale_cre"));
            $causale->setDesCausale($this->getParmFromRequest("descausale_cre"));
            $causale->setCatCausale($this->getParmFromRequest("catcausale_cre"));
            $configurazioneCausale->setCodCausale($this->getParmFromRequest("codcausale_cre"));
            $configurazioneCausale->setDesCausale($this->getParmFromRequest("descausale_cre"));
        }

        if (null !== filter_input(INPUT_POST, "codcausale_conf")) {
            $causale->setCodCausale($this->getParmFromRequest("codcausale_conf"));
            $configurazioneCausale->setCodCausale($this->getParmFromRequest("codcausale_conf"));
        }

        if (null !== filter_input(INPUT_POST, "codconto_conf")) {
            $configurazioneCausale->setCodConto($this->getParmFromRequest("codconto_conf"));
        }

        if (null !== filter_input(INPUT_POST, "codcausale_mod")) {
            $causale->setCodCausale($this->getParmFromRequest("codcausale_mod"));
            $causale->setDesCausale($this->getParmFromRequest("descausale_mod"));
            $causale->setCatCausale($this->getParmFromRequest("catcausale_mod"));
        }

        if (null !== filter_input(INPUT_POST, "codcausale_del")) {
            $causale->setCodCausale($this->getParmFromRequest("codcausale_del"));
        }

        if (null !== filter_input(INPUT_POST, "catcliente_mod")) {
            $progressivoFattura->setCatCliente($this->getParmFromRequest("catcliente_mod"));
            $progressivoFattura->setNegProgr($this->getParmFromRequest("codnegozio_mod"));
            $progressivoFattura->setNumFatturaUltimo($this->getParmFromRequest("numfatt_mod"));
            $progressivoFattura->setNotaTestaFattura($this->getParmFromRequest("notatesta_mod"));
            $progressivoFattura->setNotaPiedeFattura($this->getParmFromRequest("notapiede_mod"));
        }

        // Serializzo in sessione gli oggetti modificati

        $_SESSION[self::CONTO] = serialize($conto);
        $_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
        $_SESSION[self::CAUSALE] = serialize($causale);
        $_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
        $_SESSION[self::PROGRESSIVO_FATTURA] = serialize($progressivoFattura);

        if ($this->getRequest() == "start") {
            $this->configurazioniFunction->start();
        }
        if ($this->getRequest() == "go") {
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