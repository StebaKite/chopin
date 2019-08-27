<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaCliente.class.php';

class AggiungiFatturaIncassata extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {

        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::AGGIUNGI_FATTURA_INCASSATA])) {
            $_SESSION[self::AGGIUNGI_FATTURA_INCASSATA] = serialize(new AggiungiFatturaIncassata());
        }
        return unserialize($_SESSION[self::AGGIUNGI_FATTURA_INCASSATA]);
    }

    public function start() {
        $this->go();
    }

    public function go() {
        $db = Database::getInstance();
        $registrazione = Registrazione::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $cliente = Cliente::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $cliente->setIdCliente($registrazione->getIdCliente());

        if ($scadenzaCliente->getIdTableScadenzeAperte() == "scadenze_aperte_inc_cre") {
            $scadenzaCliente->leggi($db);
            $scadenzaCliente->aggiungiScadenzaIncassata();

            $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZE_CLIENTE_APERTE;

            $replace = array(
                '%scadenzedaincassare%' => $this->refreshTabellaFattureDaIncassare($scadenzaCliente),
                '%scadenzeincassate%' => $this->makeTabellaFattureIncassate($scadenzaCliente)
            );
            $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
            echo $utility->tailTemplate($template);
        } elseif ($scadenzaCliente->getIdTableScadenzeAperte() == "scadenze_aperte_inc_mod") {
            $scadenzaCliente->setStaScadenza("10");   // pagata e chiusa
            $scadenzaCliente->setIdIncasso($registrazione->getIdRegistrazione());
            $scadenzaCliente->cambiaStato($db);
            $scadenzaCliente->trovaScadenzeDaIncassare($db);
            $scadenzaCliente->trovaScadenzeIncassate($db);

            $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZE_CLIENTE_APERTE;

            $replace = array(
                '%scadenzedaincassare%' => $this->makeTabellaFattureDaIncassare($scadenzaCliente),
                '%scadenzeincassate%' => $this->makeTabellaFattureIncassate($scadenzaCliente)
            );
            $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
            echo $utility->tailTemplate($template);
        }
    }

}
