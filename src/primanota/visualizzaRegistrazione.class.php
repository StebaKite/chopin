<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'fornitore.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'causale.class.php';

class VisualizzaRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}
	
	public static function getInstance()
	{
		if (!isset($_SESSION[self::VISUALIZZA_REGISTRAZIONE])) $_SESSION[self::VISUALIZZA_REGISTRAZIONE] = serialize(new VisualizzaRegistrazione());
		return unserialize($_SESSION[self::VISUALIZZA_REGISTRAZIONE]);
	}
	public function start()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$fornitore = Fornitore::getInstance();
		$cliente = Cliente::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$causale = Causale::getInstance();
		
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		$array = $utility->getConfig();
		
		$registrazione->prepara();
		$cliente->prepara();
		$fornitore->prepara();
		
		$registrazione->leggi($db);
		$_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
		
		if ($registrazione->getIdFornitore() != null) {
			$fornitore->setIdFornitore($registrazione->getIdFornitore());
			$fornitore->leggi($db);
			$scadenzaFornitore->setIdRegistrazione($registrazione->getIdRegistrazione());
			$scadenzaFornitore->trovaScadenzeRegistrazione($db);
			$scadenzaFornitore->setIdTableScadenzeAperte("scadenzesuppl_vis");
			$_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);
		}
		
		if ($registrazione->getIdCliente() != null) {
			$cliente->setIdCliente($registrazione->getIdCliente());
			$cliente->leggi($db);
			$scadenzaCliente->setIdRegistrazione($registrazione->getIdRegistrazione());
			$scadenzaCliente->trovaScadenzeRegistrazione($db);
			$scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_vis");
			$_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
		}
		
		$dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
		$dettaglioRegistrazione->leggiDettagliRegistrazione($db);
		$dettaglioRegistrazione->setCampoMsgControlloPagina("tddettagli_vis");
		$dettaglioRegistrazione->setIdTablePagina("dettagli_vis");
		$dettaglioRegistrazione->setMsgControlloPagina("messaggioControlloDettagli_vis");
		$dettaglioRegistrazione->setNomeCampo("descreg_vis");
		$dettaglioRegistrazione->setLabelNomeCampo("descreg_vis_label");
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
		
		$negozio = (trim($registrazione->getCodNegozio()) == "TRE") ? "Trezzo" : $negozio;
		$negozio = (trim($registrazione->getCodNegozio()) == "VIL") ? "Villa D'adda" : $negozio;
		$negozio = (trim($registrazione->getCodNegozio()) == "BRE") ? "Brembate" : $negozio;
		
		$causale->setCodCausale($registrazione->getCodCausale());
		$causale->leggi($db);
		
		$risultato_xml = $this->root . $array['template'] . self::XML_VISUALIZZA_REGISTRAZIONE;
		
		$replace = array(
				'%datareg%' => trim($registrazione->getDatRegistrazione()),
				'%descreg%' => trim($registrazione->getDesRegistrazione()),
				'%causale%' => trim($causale->getDesCausale()),
				'%codneg%' => $negozio,
				'%fornitore%' => trim($fornitore->getDesFornitore()),
				'%cliente%' => trim($cliente->getDesCliente()),
				'%numfatt%' => trim($registrazione->getNumFattura()),
				'%scadenzesupplfornitore%' => trim($this->makeTabellaReadOnlyScadenzeFornitore($scadenzaFornitore)),
				'%scadenzesupplcliente%' => trim($this->makeTabellaReadOnlyScadenzeCliente($scadenzaCliente)),
				'%dettagli%' => trim($this->makeTabellaReadOnlyDettagliRegistrazione($dettaglioRegistrazione)),
				'%conti%' => trim($causale->getContiCausale())
		);
		$template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
		echo $utility->tailTemplate($template);
	}
	
	public function go() {}	

}		
		
?>