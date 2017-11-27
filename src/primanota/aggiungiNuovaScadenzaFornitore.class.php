<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fornitore.class.php';
require_once 'scadenzaFornitore.class.php';

class AggiungiNuovaScadenzaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::AGGIUNGI_SCADENZA_FORNITORE])) $_SESSION[self::AGGIUNGI_SCADENZA_FORNITORE] = serialize(new AggiungiNuovaScadenzaFornitore());
		return unserialize($_SESSION[self::AGGIUNGI_SCADENZA_FORNITORE]);
	}

	public function start() {
		$this->go();
	}

	public function go()
	{
		$db = Database::getInstance();
		$fornitore = Fornitore::getInstance();
		$fornitore->cercaConDescrizione($db);

		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
		
		$scadenzaFornitore->aggiungi();
		echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore);
	}
}

?>