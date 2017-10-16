<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'fornitore.class.php';

class RicercaScadenzeAperteFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface
{

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_SCADENZE_FORNITORE_APERTE])) $_SESSION[self::RICERCA_SCADENZE_FORNITORE_APERTE] = serialize(new RicercaScadenzeAperteFornitore());
		return unserialize($_SESSION[self::RICERCA_SCADENZE_FORNITORE_APERTE]);
	}

	public function start()
	{

		$registrazione = Registrazione::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$fornitore = Fornitore::getInstance();
		$db = Database::getInstance();

		$fornitore->cercaConDescrizione($db);
		$scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
		$scadenzaFornitore->trovaScadenzeDaPagare($db);
		$registrazione->setNumFattureDaPagare($this->makeTabellaFattureDaPagare($scadenzaFornitore));
		$registrazione->setNumFatturePagate("");
	
		$datiPagina = trim($registrazione->getNumFattureDaPagare()) . "|" . trim($registrazione->getNumFatturePagate());
		                                
        echo $datiPagina;
	}

	public function go() {
	}
}

?>