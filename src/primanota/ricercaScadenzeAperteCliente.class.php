<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'cliente.class.php';

class RicercaScadenzeAperteCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface
{

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_SCADENZE_CLIENTE_APERTE])) $_SESSION[self::RICERCA_SCADENZE_CLIENTE_APERTE] = serialize(new RicercaScadenzeAperteCliente());
		return unserialize($_SESSION[self::RICERCA_SCADENZE_CLIENTE_APERTE]);
	}

	public function start()
	{

		$registrazione = Registrazione::getInstance();
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$cliente = Cliente::getInstance();
		$db = Database::getInstance();

		$cliente->cercaConDescrizione($db);
		$scadenzaCliente->setIdCliente($cliente->getIdCliente());
		$scadenzaCliente->trovaScadenzeDaIncassare($db);

		$options = '<select class="numfatt-cliente-multiple" multiple="multiple" style="width: 600px" id="select2">';

		if ($scadenzaCliente->getQtaScadenzeDaIncassare() > 0)
		{
			foreach($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza) {
				$options .= '<option value="' . trim($unaScadenza[ScadenzaCliente::NUM_FATTURA]) . '" >Ft.' . trim($unaScadenza[ScadenzaCliente::NUM_FATTURA]) . ' - &euro; ' . trim($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE]) . ' - (' . trim($unaScadenza[ScadenzaCliente::NOTA]) . ')</option>';
			}
		}
		$options .= '</select>';
		echo $options;
	}

	public function go() {
	}
}

?>