<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'mercato.class.php';
require_once 'causale.class.php';

class ModificaCorrispettivoMercato extends primanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::MODIFICA_CORRISPETTIVO_MERCATO])) {
            $_SESSION[self::MODIFICA_CORRISPETTIVO_MERCATO] = serialize(new ModificaCorrispettivoMercato());
        }
        return unserialize($_SESSION[self::MODIFICA_CORRISPETTIVO_MERCATO]);
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $mercato = Mercato::getInstance();
        $causale = Causale::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $registrazione->prepara();
        $mercato->prepara();

        $registrazione->leggi($db);
        $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);

        $mercato->setIdMercato($registrazione->getIdMercato());
        $mercato->leggi($db);

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
        $dettaglioRegistrazione->setIdTablePagina("dettagli_cormer_mod");
        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);

        $negozio = trim($registrazione->getCodNegozio());

        // Mercati configurati sul negozio

        $mercato->setCodNegozio($negozio);
        $mercato->cercaMercatiNegozio($db);

        $elenco_mercati = "<select class='form-control' id='mercato_cormer_mod' name='mercato_cormer_mod'><option value=''></option>";

        if ($mercato->getQtaMercati() > 0) {
            foreach ($mercato->getMercati() as $unMercato) {
                if ($unMercato[Mercato::ID_MERCATO] == $mercato->getIdMercato())
                    $elenco_mercati .= "<option value='" . $unMercato[Mercato::ID_MERCATO] . "' selected>" . $unMercato[Mercato::DES_MERCATO] . "</option>";
                else
                    $elenco_mercati .= "<option value='" . $unMercato[Mercato::ID_MERCATO] . "'>" . $unMercato[Mercato::DES_MERCATO] . "</option>";
            }
        }
        else {
            $elenco_mercati .= "<option value=''>Non ci sono mercati per il negozio</option>";
        }
        $elenco_mercati .= "</select>";

        $causale->setCodCausale($registrazione->getCodCausale());
        $causale->leggi($db);
        $causale->loadContiConfigurati($db); // conti configurati sulla causale

        $risultato_xml = $this->root . $array['template'] . self::XML_CORRISPETTIVO;

        $replace = array(
            '%datareg%' => trim($registrazione->getDatRegistrazione()),
            '%descreg%' => trim($registrazione->getDesRegistrazione()),
            '%causale%' => trim($causale->getCodCausale()),
            '%codneg%' => $negozio,
            '%mercato%' => trim($mercato->getIdMercato()),
            '%mercatiNegozio%' => $elenco_mercati,
            '%dettagli%' => trim($this->makeTabellaDettagliCorrispettivo($registrazione, $dettaglioRegistrazione)),
            '%contiCausale%' => $causale->getContiCausale()
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $utility = Utility::getInstance();
        $db = Database::getInstance();

        $array = $utility->getConfig();
        $registrazione->setCodCausale($array["corrispettiviMercato"]);        

        $registrazione->aggiorna($db);
        
        if ($dettaglioRegistrazione->getQtaDettagliRegistrazione() > 0) {
            foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
                $dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);
                $dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
                $dettaglioRegistrazione->setIdDettaglioRegistrazione($unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE]);
                $dettaglioRegistrazione->aggiorna($db);
            }
        }

        $_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
        $controller = unserialize($_SESSION["Obj_primanotacontroller"]);
        $controller->start();
    }

}