<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaCliente.class.php';

class RimuoviFatturaIncassata extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RIMUOVI_FATTURA_INCASSATA) === NULL) {
            parent::setIndexSession(self::RIMUOVI_FATTURA_INCASSATA, serialize(new RimuoviFatturaIncassata()));
        }
        return unserialize(parent::getIndexSession(self::RIMUOVI_FATTURA_INCASSATA));
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

        $cliente->cercaConDescrizione($db);
        $scadenzaCliente->setIdCliente($cliente->getIdCliente());

        if ($scadenzaCliente->getIdTableScadenzeAperte() == "scadenze_aperte_inc_cre") {
            $scadenzaCliente->leggi($db);
            $scadenzaCliente->rimuoviScadenzaIncassata();

            $risultato_xml = $this->root . $array['template'] . self::XML_SCADENZE_CLIENTE_APERTE;

            $replace = array(
                '%scadenzedaincassare%' => $this->refreshTabellaFattureDaIncassare($scadenzaCliente),
                '%scadenzeincassate%' => $this->makeTabellaFattureIncassate($scadenzaCliente)
            );
            $template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
            echo $utility->tailTemplate($template);
        } elseif ($scadenzaCliente->getIdTableScadenzeAperte() == "scadenze_aperte_inc_mod") {
            $scadenzaCliente->setIdCliente($registrazione->getIdCliente());
            $scadenzaCliente->setStaScadenza("00");   // aperta e da incassare
            $scadenzaCliente->setIdIncasso("");
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