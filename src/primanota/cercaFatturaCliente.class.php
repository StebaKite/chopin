<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'cliente.class.php';

class CercaFatturaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CERCA_FATTURA_CLIENTE])) $_SESSION[self::CERCA_FATTURA_CLIENTE] = serialize(new CercaFatturaCliente());
		return unserialize($_SESSION[self::CERCA_FATTURA_CLIENTE]);
	}

	public function start()
	{
		$cliente = Cliente::getInstance();
		$registrazione = Registrazione::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$cliente->setDesCliente($registrazione->getDesCliente());
		$cliente->cercaConDescrizione($db);
		$registrazione->setIdCliente($cliente->getIdCliente());
		if ($registrazione->cercaFatturaCliente($db)) echo "Fattura gi&agrave; esistente";
		else echo "";
	}

	public function go() {
		$this->start();
	}
}

?>