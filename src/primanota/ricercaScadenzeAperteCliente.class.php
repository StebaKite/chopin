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

		$cliente->setDesCliente($registrazione->getDesCliente());
		$cliente->cercaConDescrizione($db);


		$options = '<select class="numfatt-cliente-multiple" multiple="multiple" style="width: 600px" id="select2">';



			$db->beginTransaction();
			$_SESSION["idcliente"] = $this->leggiDescrizioneCliente($db, $utility, str_replace("'", "''", $_SESSION["descli"]));
			$db->commitTransaction();

			$result_scadenze_cliente = $this->prelevaScadenzeAperteCliente($db, $utility, $_SESSION["idcliente"]);

			foreach(pg_fetch_all($result_scadenze_cliente) as $row) {
				$options .= '<option value="' . trim($row['num_fattura']) . '" >Ft.' . trim($row['num_fattura']) . ' - &euro; ' . trim($row['imp_registrazione']) . ' - (' . trim($row['nota']) . ')</option>';
			}

		$options .= '</select>';
		echo $options;
	}

	public function go() {
	}
}

?>