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

class VisualizzaPagamento extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}
	
	public static function getInstance()
	{
		if (!isset($_SESSION[self::VISUALIZZA_PAGAMENTO])) $_SESSION[self::VISUALIZZA_PAGAMENTO] = serialize(new VisualizzaPagamento());
		return unserialize($_SESSION[self::VISUALIZZA_PAGAMENTO]);
	}
	
	public function start()
	{
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
		$_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
		
		$fornitore->setIdFornitore($registrazione->getIdFornitore());
		$fornitore->leggi($db);
		$scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
		$scadenzaFornitore->trovaScadenzePagate($db);
		$scadenzaFornitore->setIdTableScadenzeChiuse("scadenze_chiuse_pag_vis");
		$_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
		
		$dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
		$dettaglioRegistrazione->leggiDettagliRegistrazione($db);
		$dettaglioRegistrazione->setIdTablePagina("dettagli_pag_vis");
		$dettaglioRegistrazione->setNomeCampo("descreg_pag_vis");
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
		
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
	
	public function go() {}
	
}

?>