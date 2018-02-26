<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'scadenze.controller.class.php';
require_once 'ricercaScadenzeFornitore.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'registrazione.class.php';

class VisualizzaScadenzaFornitore extends ScadenzeAbstract implements ScadenzeBusinessInterface {
	
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}
	
	public function getInstance()
	{
		if (!isset($_SESSION[self::VISUALIZZA_SCADENZA_FORNITORE])) $_SESSION[self::VISUALIZZA_SCADENZA_FORNITORE] = serialize(new VisualizzaScadenzaFornitore());
		return unserialize($_SESSION[self::VISUALIZZA_SCADENZA_FORNITORE]);
	}
	
	public function start()
	{
		$scadenza = ScadenzaFornitore::getInstance();
		$registrazione = Registrazione::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		
		$scadenza->leggi($db);
		
		$registrazione->setIdRegistrazione($scadenza->getIdRegistrazione());
		$registrazione->leggi($db);
		$registrazioneOriginante = $this->makeTabellaReadOnlyRegistrazioneOriginale($registrazione);

		if (trim($scadenza->getStaScadenza()) == self::SCADENZA_CHIUSA) {
			$registrazione->setIdRegistrazione($scadenza->getIdPagamento());
			$registrazione->leggi($db);
			$pagamento = $this->makeTabellaReadOnlyPagamento($registrazione);
		}
		else $pagamento = 
		"<tbody>" .
		"	<tr><td class='bg-warning'>Questa scadenza non ha ancora un pagamento associato</td></tr>" .
		"</tbody>";

		
		$risultato_xml = $this->root . $array['template'] . self::XML_SCADENZA_FORNITORE;
		
		$replace = array(
				'%data%' => trim($scadenza->getDatScadenza()),
				'%importo%' => trim($scadenza->getImpInScadenza()),
				'%addebito%' => trim($scadenza->getTipAddebito()),
				'%stato%' => trim($scadenza->getStaScadenza()),
				'%fattura%' => trim($scadenza->getNumFattura()),
				'%nota%' => trim($scadenza->getNotaScadenza()),
				'%registrazioneoriginante%' => $registrazioneOriginante,
				'%pagamento%' => $pagamento
		);
		$template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
		echo $utility->tailTemplate($template);
	}
	
	public function go() {}
}

?>