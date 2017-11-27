<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaCliente.class.php';

class CalcolaDataScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
    public static $ggMese = array(
        '01' => '31',
        '02' => '28',
        '03' => '31',
        '04' => '30',
        '05' => '31',
        '06' => '30',
        '07' => '31',
        '08' => '31',
        '09' => '30',
        '10' => '31',
        '11' => '30',
        '12' => '31',
    );
    
    function __construct()
    {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }
    
    public function getInstance()
    {
        if (!isset($_SESSION[self::CALCOLA_DATA_SCADENZA_CLIENTE])) $_SESSION[self::CALCOLA_DATA_SCADENZA_CLIENTE] = serialize(new CalcolaDataScadenzaCliente());
        return unserialize($_SESSION[self::CALCOLA_DATA_SCADENZA_CLIENTE]);
    }
    
    public function start()
    {
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $registrazione = Registrazione::getInstance();
        $cliente = Cliente::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        
        $cliente->setDesCliente($registrazione->getDesCliente());
        $cliente->cercaConDescrizione($db);
        
        $scadenzaCliente->setQtaScadenzeDaIncassare(0);
        $scadenzaCliente->setScadenzeDaIncassare("");
        /**
         * I giorni di scadenza fattura per i clienti sono configurati in configurazione per tutti
         * Se questo valore è = 0 la scadenza non viene calcolata
         */
        $array = $utility->getConfig();
        
        if ($array['giorniScadenzaFattureCliente'] > 0) {
            /**
             * Le data di registrazione viene aumentata dei giorni configurati per il cliente,
             * alla data ottenuta viene sostituito il giorno con l'ultimo giorno del mese corrispondente
             */
            $dataScadenza = $this->sommaGiorniData($registrazione->getDatRegistrazione(), "/", $array['giorniScadenzaFattureCliente']);
            
            $data = explode("/",$dataScadenza);
            $mese = $data[1];
            $anno = $data[2];
            
            $scadenzaCliente->setDatRegistrazione(SELF::$ggMese[$mese]."/".$mese."/".$anno);
            $scadenzaCliente->setIdCliente($cliente->getIdCliente());
            $scadenzaCliente->setImpRegistrazione(0);
            $scadenzaCliente->setNumFattura("0");
            $scadenzaCliente->aggiungi();
            
            echo $this->makeTabellaScadenzeCliente($scadenzaCliente);
        }
        else {
            echo "";
        }
    }
    public function go() {}
}


?>