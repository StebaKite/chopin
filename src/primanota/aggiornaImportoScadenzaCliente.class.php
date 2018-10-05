<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';

class AggiornaImportoScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public static function getInstance()
	{
		if (!isset($_SESSION[self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE])) $_SESSION[self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE] = serialize(new AggiornaImportoScadenzaCliente());
		return unserialize($_SESSION[self::AGGIORNA_IMPORTO_SCADENZA_CLIENTE]);
	}

	public function start() {
		$this->go();
	}

	public function go()
	{
		$db = Database::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$scadenzaCliente->aggiornaImporto($db);
		$scadenzaCliente->setIdTableScadenzeAperte("scadenzesuppl_mod");
		$_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
		echo $this->makeTabellaScadenzeCliente($scadenzaCliente);
	}
}

?>