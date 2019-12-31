<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'fornitore.class.php';

class RicercaScadenzeAperteFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RICERCA_SCADENZE_FORNITORE_APERTE) === NULL) {
            parent::setIndexSession(self::RICERCA_SCADENZE_FORNITORE_APERTE, serialize(new RicercaScadenzeAperteFornitore()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_SCADENZE_FORNITORE_APERTE));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $fornitore = Fornitore::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
        $scadenzaFornitore->trovaScadenzeDaPagare($db);
        
        /**
         * Ripulisco i dettagli della registrazione inseriti precedentemente
         */
        
        $dettaglioRegistrazione->setDettagliRegistrazione(self::EMPTYSTRING);
        $dettaglioRegistrazione->setQtaDettagliRegistrazione(self::ZERO_VALUE);
        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));

        /**
         * Nell'attributo numFattureDaPagare ci appoggio la table html generata
         * Le scadenze si trovano nell'oggetto scadenzaFornitore
         */
        $registrazione->setNumFattureDaPagare($this->makeTabellaFattureDaPagare($scadenzaFornitore));
        $registrazione->setNumFatturePagate("");

        $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZE_FORNITORE_APERTE;

        $replace = array(
            '%scadenzedapagare%' => $registrazione->getNumFattureDaPagare(),
            '%scadenzepagate%' => $registrazione->getNumFatturePagate()
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        
    }

}