<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'registrazione.class.php';
require_once 'cliente.class.php';
require_once 'sottoconto.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'scadenzaFornitore.class.php';


class AnnullaNuovaRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public static function getInstance()
	{
		if (!isset($_SESSION[self::ANNULLA_NUOVA_REGISTRAZIONE])) $_SESSION[self::ANNULLA_NUOVA_REGISTRAZIONE] = serialize(new AnnullaNuovaRegistrazione());
		return unserialize($_SESSION[self::ANNULLA_NUOVA_REGISTRAZIONE]);
	}

	public function start() {
		$this->go();
	}

	public function go()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$registrazione->preparaFiltri();
		$dettaglioRegistrazione->prepara();
		$scadenzaCliente->prepara();
		$scadenzaFornitore->prepara();

		echo "Okay";
	}
}

?>