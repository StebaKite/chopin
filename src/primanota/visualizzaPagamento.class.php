<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'fornitore.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'causale.class.php';

class VisualizzaPagamento extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::VISUALIZZA_PAGAMENTO) === NULL) {
            parent::setIndexSession(self::VISUALIZZA_PAGAMENTO, serialize(new VisualizzaPagamento()));
        }
        return unserialize(parent::getIndexSession(self::VISUALIZZA_PAGAMENTO));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $fornitore = Fornitore::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $causale = Causale::getInstance();

        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $registrazione->prepara();
        $fornitore->prepara();

        $registrazione->leggi($db);
        parent::setIndexSession(self::REGISTRAZIONE, serialize($registrazione));

        $fornitore->setIdFornitore($registrazione->getIdFornitore());
        $fornitore->leggi($db);
        $scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
        $scadenzaFornitore->trovaScadenzePagate($db);
        $scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_vis");
        parent::setIndexSession(self::SCADENZA_FORNITORE, serialize($scadenzaFornitore));

        $dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
        $dettaglioRegistrazione->leggiDettagliRegistrazione($db);
        $dettaglioRegistrazione->setIdTablePagina("dettagli_pag_vis");
        $dettaglioRegistrazione->setNomeCampo("descreg_pag_vis");
        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));

        $negozio = (trim($registrazione->getCodNegozio()) == "TRE") ? "Trezzo" : $negozio;
        $negozio = (trim($registrazione->getCodNegozio()) == "VIL") ? "Villa D'adda" : $negozio;
        $negozio = (trim($registrazione->getCodNegozio()) == "BRE") ? "Brembate" : $negozio;

        $causale->setCodCausale($registrazione->getCodCausale());
        $causale->leggi($db);

        $risultato_xml = $this->root . $array['template'] . self::XML_VISUALIZZA_PAGAMENTO;

        $replace = array(
            '%datareg%' => trim($registrazione->getDatRegistrazione()),
            '%descreg%' => trim($registrazione->getDesRegistrazione()),
            '%causale%' => trim($causale->getDesCausale()),
            '%codneg%' => $negozio,
            '%fornitore%' => trim($fornitore->getDesFornitore()),
            '%scadenzepagate%' => trim($this->makeTabellaReadOnlyFatturePagate($scadenzaFornitore)),
            '%dettagli%' => trim($this->makeTabellaReadOnlyDettagliRegistrazione($dettaglioRegistrazione))
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        
    }

}