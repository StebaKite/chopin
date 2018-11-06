<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'main.template.php';
require_once 'fattura.class.php';
require_once 'dettaglioFattura.class.php';

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

        if (!isset($_SESSION[self::CREA_FATTURA_CLIENTE_XML]))
            $_SESSION[self::CREA_FATTURA_CLIENTE_XML] = serialize(new creaFatturaClienteXML());
        return unserialize($_SESSION[self::CREA_FATTURA_CLIENTE_XML]);
    }

    public function start() {

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();

        $domtree = new DOMDocument('1.0', 'UTF-8');
        
        $xmlRoot = $domtree->createElement("ns2:FatturaElettronica");
        $xmlRoot = $domtree->appendChild($xmlRoot);
        $xmlRoot->setAttribute('versione', 'FPR12');
        $xmlRoot->setAttribute('xmlns:ns2', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2');

        // Header Fattura ---------------------------

        $fatturaElettronicaHeader = $domtree->createElement("FatturaElettronicaHeader");       

        // DatiTrasmissione ---------------------------

        $datiTrasmissione = $fatturaElettronicaHeader->appendChild($domtree->createElement('DatiTrasmissione'));   
        $fatturaElettronicaHeader->appendChild($this->makeDatiTrasmissione($datiTrasmissione, $domtree));       

        // CedentePrestatore ---------------------------

        $cedentePrestatore = $fatturaElettronicaHeader->appendChild($domtree->createElement('CedentePrestatore'));
        $fatturaElettronicaHeader->appendChild($this->makeCedentePrestatore($cedentePrestatore, $domtree));       

        // CessionarioCommittente ---------------------------

        $cessionarioCommittente = $fatturaElettronicaHeader->appendChild($domtree->createElement('CessionarioCommittente'));
        $fatturaElettronicaHeader->appendChild($this->makeCessionarioCommittente($cessionarioCommittente, $domtree));       

        // ---------------------------








        
        $xmlRoot->appendChild($fatturaElettronicaHeader);       
        
        // Salvo il file generato
        $domtree->save('C:/Temp/fattura.xml');

        $mainTemplate = MainTemplate::getInstance();
        $mainTemplate->displayPagina();    
    }

    /**
     * 
     * @param type $datiTrasmissione
     * @param type $domtree
     * @return type
     */
    public function makeDatiTrasmissione($datiTrasmissione, $domtree) {

        $idTrasmittente = $datiTrasmissione->appendChild($domtree->createElement('IdTrasmittente'));
            $idTrasmittente->appendChild($domtree->createElement('IdPaese','IT'));
            $idTrasmittente->appendChild($domtree->createElement('IdCodice','HVQWPH73P42H501Y'));

        $datiTrasmissione->appendChild($domtree->createElement('ProgressivoInvio','24'));           // ok, oggetto Fattura            
        $datiTrasmissione->appendChild($domtree->createElement('FormatoTrasmissione','FPR12'));     // fisso in config            
        $datiTrasmissione->appendChild($domtree->createElement('CodiceDestinatario','0000000'));    // fisso in config            

        return $datiTrasmissione;
    }    
    
    /**
     * 
     * @param type $cessionarioCommittente
     * @param type $domtree
     * @return type
     */
    public function makeCessionarioCommittente($cessionarioCommittente, $domtree) {

        $datiAnagrafici = $cessionarioCommittente->appendChild($domtree->createElement('DatiAnagrafici'));

            $datiAnagrafici->appendChild($domtree->createElement('CodiceFiscale','BLDRFL86E05I452D'));    // ok, oggetto Fattura

            $anagrafica = $datiAnagrafici->appendChild($domtree->createElement('Anagrafica'));
                $anagrafica->appendChild($domtree->createElement('Nome','Mario'));      // ok, oggetto Fattura
                $anagrafica->appendChild($domtree->createElement('Cognome','Rossi'));   // ok, oggetto Fattura

        $sede = $cessionarioCommittente->appendChild($domtree->createElement('Sede'));
            $sede->appendChild($domtree->createElement('Indirizzo','Via del Corso'));   // ok, oggetto Fattura
            $sede->appendChild($domtree->createElement('NumeroCivico','45'));           // ok, oggetto Fattura
            $sede->appendChild($domtree->createElement('CAP','00100'));                 // ok, oggetto Fattura
            $sede->appendChild($domtree->createElement('Comune','Roma'));               // manca, oggetto Fattura
            $sede->appendChild($domtree->createElement('Provincia','RM'));              // manca, oggetto Fattura
            $sede->appendChild($domtree->createElement('Nazione','IT'));                // manca, oggetto Fattura 
        
        return $cessionarioCommittente;        
    }

    /**
     * 
     * @param type $cedentePrestatore
     * @param type $domtree
     * @return type
     */
    public function makeCedentePrestatore($cedentePrestatore, $domtree) {
        
        $datiAnagrafici = $cedentePrestatore->appendChild($domtree->createElement('DatiAnagrafici'));

            $idFiscaleIVA = $datiAnagrafici->appendChild($domtree->createElement('IdFiscaleIVA'));
                $idFiscaleIVA->appendChild($domtree->createElement('IdPaese','IT'));
                $idFiscaleIVA->appendChild($domtree->createElement('IdCodice','23333330589'));

            $datiAnagrafici->appendChild($domtree->createElement('CodiceFiscale','HVQWPH73P42H501Y'));            

            $anagrafica = $datiAnagrafici->appendChild($domtree->createElement('Anagrafica'));
                $anagrafica->appendChild($domtree->createElement('Nome','WINPHON'));
                $anagrafica->appendChild($domtree->createElement('Cognome','HIVEQ'));

            $datiAnagrafici->appendChild($domtree->createElement('RegimeFiscale','RF01'));


        $sede = $cedentePrestatore->appendChild($domtree->createElement('Sede'));
            $sede->appendChild($domtree->createElement('Indirizzo','Via del Melo'));                    
            $sede->appendChild($domtree->createElement('NumeroCivico','131'));                    
            $sede->appendChild($domtree->createElement('CAP','00100'));                    
            $sede->appendChild($domtree->createElement('Comune','Roma'));                    
            $sede->appendChild($domtree->createElement('Provincia','AG'));                    
            $sede->appendChild($domtree->createElement('Nazione','IT'));                    

        return $cedentePrestatore;        
    }
    
    
    
    
    
    public function go() {
        
    }

}
