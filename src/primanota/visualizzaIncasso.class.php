<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'causale.class.php';

class VisualizzaIncasso extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::VISUALIZZA_INCASSO) === NULL) {
            parent::setIndexSession(self::VISUALIZZA_INCASSO, serialize(new VisualizzaIncasso()));
        }
        return unserialize(parent::getIndexSession(self::VISUALIZZA_INCASSO));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $cliente = Cliente::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $causale = Causale::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $registrazione->prepara();
        $cliente->prepara();

        $registrazione->leggi($db);
        parent::setIndexSession(self::REGISTRAZIONE, serialize($registrazione));

        $cliente->setIdCliente($registrazione->getIdCliente());
        $cliente->leggi($db);
        $scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
        $scadenzaCliente->trovaScadenzeIncassate($db);
        $scadenzaCliente->setIdTableScadenzeChiuse("scadenze_incassate_inc_vis");
        parent::setIndexSession(self::SCADENZA_CLIENTE, serialize($scadenzaCliente));

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
        $dettaglioRegistrazione->setIdTablePagina("dettagli_inc_vis");
        $dettaglioRegistrazione->setNomeCampo("descreg_inc_vis");
        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));

        $negozio = (trim($registrazione->getCodNegozio()) == "ERB") ? "Erba" : $negozio;

        $causale->setCodCausale($registrazione->getCodCausale());
        $causale->leggi($db);

        $risultato_xml = $this->root . $array['template'] . self::XML_VISUALIZZA_INCASSO;

        $replace = array(
            '%datareg%' => trim($registrazione->getDatRegistrazione()),
            '%descreg%' => trim($registrazione->getDesRegistrazione()),
            '%causale%' => trim($causale->getDesCausale()),
            '%codneg%' => $negozio,
            '%cliente%' => trim($cliente->getDesCliente()),
            '%scadenzeincassate%' => trim($this->makeTabellaReadOnlyFattureIncassate($scadenzaCliente)),
            '%dettagli%' => trim($this->makeTabellaReadOnlyDettagliRegistrazione($dettaglioRegistrazione))
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        
    }

}