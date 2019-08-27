<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'causale.class.php';

class ModificaCorrispettivoNegozio extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::MODIFICA_CORRISPETTIVO_NEGOZIO]))
            $_SESSION[self::MODIFICA_CORRISPETTIVO_NEGOZIO] = serialize(new ModificaCorrispettivoNegozio());
        return unserialize($_SESSION[self::MODIFICA_CORRISPETTIVO_NEGOZIO]);
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $causale = Causale::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $registrazione->prepara();

        $registrazione->leggi($db);
        $_SESSION[self::REGISTRAZIONE] = serialize($registrazione);

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
        $dettaglioRegistrazione->setIdTablePagina("dettagli_corneg_mod");
        $_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);

        $negozio = trim($registrazione->getCodNegozio());

        $causale->setCodCausale($registrazione->getCodCausale());
        $causale->leggi($db);
        $causale->loadContiConfigurati($db); // conti configurati sulla causale

        $risultato_xml = $this->root . $array['template'] . self::XML_CORRISPETTIVO;

        $replace = array(
            '%datareg%' => trim($registrazione->getDatRegistrazione()),
            '%descreg%' => trim($registrazione->getDesRegistrazione()),
            '%causale%' => trim($causale->getCodCausale()),
            '%codneg%' => $negozio,
            '%dettagli%' => trim($this->makeTabellaDettagliCorrispettivo($registrazione, $dettaglioRegistrazione)),
            '%contiCausale%' => $causale->getContiCausale()
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $db = Database::getInstance();

        $registrazione->aggiorna($db);

        foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
            $dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);
            $dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
            $dettaglioRegistrazione->setIdDettaglioRegistrazione($unDettaglio[DettaglioRegistrazione::ID_DETTAGLIO_REGISTRAZIONE]);
            $dettaglioRegistrazione->aggiorna($db);
        }
        
        $_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
        $controller = unserialize($_SESSION["Obj_primanotacontroller"]);
        $controller->start();
    }

}

?>