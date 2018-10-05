<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'causale.class.php';

class VisualizzaCorrispettivoNegozio extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}
	
	public static function getInstance()
	{
		if (!isset($_SESSION[self::VISUALIZZA_CORRISPETTIVO_NEGOZIO])) $_SESSION[self::VISUALIZZA_CORRISPETTIVO_NEGOZIO] = serialize(new VisualizzaCorrispettivoNegozio());
		return unserialize($_SESSION[self::VISUALIZZA_CORRISPETTIVO_NEGOZIO]);
	}

	public function start()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$causale = Causale::getInstance();
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		$array = $utility->getConfig();
		
		$registrazione->prepara();
		
		$registrazione->leggi($db);
		$_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
		
		$dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
		$dettaglioRegistrazione->leggiDettagliRegistrazione($db);
		$dettaglioRegistrazione->setIdTablePagina("dettagli_cormer_vis");
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
		
		$negozio = (trim($registrazione->getCodNegozio()) == "TRE") ? "Trezzo" : $negozio;
		$negozio = (trim($registrazione->getCodNegozio()) == "VIL") ? "Villa D'adda" : $negozio;
		$negozio = (trim($registrazione->getCodNegozio()) == "BRE") ? "Brembate" : $negozio;
		
		$causale->setCodCausale($registrazione->getCodCausale());
		$causale->leggi($db);
		
		$risultato_xml = $this->root . $array['template'] . self::XML_CORRISPETTIVO;
		
		$replace = array(
				'%datareg%' => trim($registrazione->getDatRegistrazione()),
				'%descreg%' => trim($registrazione->getDesRegistrazione()),
				'%causale%' => trim($causale->getDesCausale()),
				'%codneg%' => $negozio,
				'%dettagli%' => trim($this->makeTabellaReadOnlyDettagliRegistrazione($dettaglioRegistrazione))
		);
		$template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
		echo $utility->tailTemplate($template);
	}
	
	public function go() {}
}		
		
?>