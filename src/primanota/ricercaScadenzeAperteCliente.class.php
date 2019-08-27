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
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::RICERCA_SCADENZE_CLIENTE_APERTE])) {
            $_SESSION[self::RICERCA_SCADENZE_CLIENTE_APERTE] = serialize(new RicercaScadenzeAperteCliente());
        }
        return unserialize($_SESSION[self::RICERCA_SCADENZE_CLIENTE_APERTE]);
    }

    public function start() {

        $registrazione = Registrazione::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $cliente = Cliente::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $scadenzaCliente->setIdCliente($cliente->getIdCliente());
        $scadenzaCliente->trovaScadenzeDaIncassare($db);

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