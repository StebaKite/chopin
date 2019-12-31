<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'cliente.class.php';

class RicercaScadenzeAperteCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RICERCA_SCADENZE_CLIENTE_APERTE) === NULL) {
            parent::setIndexSession(self::RICERCA_SCADENZE_CLIENTE_APERTE, serialize(new RicercaScadenzeAperteCliente()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_SCADENZE_CLIENTE_APERTE));
    }

    public function start() {

        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $scadenzaCliente->setIdCliente($cliente->getIdCliente());
        $scadenzaCliente->trovaScadenzeDaIncassare($db);

        /**
         * Ripulisco i dettagli della registrazione inseriti precedentemente
         */
        
        $dettaglioRegistrazione->setDettagliRegistrazione(self::EMPTYSTRING);
        $dettaglioRegistrazione->setQtaDettagliRegistrazione(self::ZERO_VALUE);
        parent::setIndexSession(self::DETTAGLIO_REGISTRAZIONE, serialize($dettaglioRegistrazione));
        
        /**
         * Nell'attributo numFattureDaIncassare ci appoggio la table html generata
         * Le scadenze si trovano nell'oggetto scadenzaCliente
         */
        $registrazione->setNumFattureDaIncassare($this->makeTabellaFattureDaIncassare($scadenzaCliente));
        $registrazione->setNumFattureIncassate("");

        $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZE_CLIENTE_APERTE;

        $replace = array(
            '%scadenzedaincassare%' => $registrazione->getNumFattureDaIncassare(),
            '%scadenzeincassate%' => $registrazione->getNumFattureIncassate()
        );
        $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        
    }

}