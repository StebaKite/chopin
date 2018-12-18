<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';

/**
 * Description of creaFatturaClienteXML
 *
 * @author BarbieriStefano
 */
class creaFatturaClienteXML extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {

        if (!isset($_SESSION[self::CREA_FATTURA_CLIENTE]))
            $_SESSION[self::CREA_FATTURA_CLIENTE] = serialize(new CreaFatturaCliente());
        return unserialize($_SESSION[self::CREA_FATTURA_CLIENTE]);
    }

    public function start() {

        $domtree = new DOMDocument('1.0', 'UTF-8');
        
        $xmlRoot = $domtree->createElement("xml");
        $xmlRoot = $domtree->appendChild($xmlRoot);

        $currentTrack = $domtree->createElement("FatturaElettronicaHeader");
        $currentTrack = $xmlRoot->appendChild($currentTrack);       
        $currentTrack->appendChild($domtree->createElement('path','song1.mp3'));    
        $currentTrack->appendChild($domtree->createElement('title','title of song1.mp3'));
        
        
        $domtree->save('C:/Temp/fattura.xml');
        
    }

    public function go() {
        
    }

}
