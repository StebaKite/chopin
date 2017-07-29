<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';
require_once 'scadenzaCliente.class.php';

class AggiungiNuovaScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::AGGIUNGI_SCADENZA_CLIENTE])) $_SESSION[self::AGGIUNGI_SCADENZA_CLIENTE] = serialize(new AggiungiNuovaScadenzaCliente());
		return unserialize($_SESSION[self::AGGIUNGI_SCADENZA_CLIENTE]);
	}
	public function start() {
		$this->go();
	}

	public function go()
	{
		$db = Database::getInstance();
		$cliente = Cliente::getInstance();
		$cliente->cercaConDescrizione($db);

		$scadenzaCliente = ScadenzaCliente::getInstance();
		$scadenzaCliente->setIdCliente($cliente->getIdCliente());
		$scadenzaCliente->aggiungi();
		echo $this->makeTabellaScadenzeCliente($scadenzaCliente);
	}
}

