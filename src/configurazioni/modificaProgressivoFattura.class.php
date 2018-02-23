<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'ricercaProgressivoFattura.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'progressivoFattura.class.php';

class ModificaProgressivoFattura extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA])) $_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA] = serialize(new ModificaProgressivoFattura());
		return unserialize($_SESSION[self::MODIFICA_PROGRESSIVO_FATTURA]);
	}

	public function start()
	{

		$progressivoFattura = ProgressivoFattura::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$progressivoFattura->leggi($db);
		$_SESSION[self::PROGRESIVO_FATTURA] = serialize($progressivoFattura);
		
		$risultato_xml = $this->root . $array['template'] . self::XML_PROGRESSIVO;
		
		$replace = array(
				'%categoria%' => trim($progressivoFattura->getCatCliente()),
				'%negozio%' => trim($progressivoFattura->getNegProgr()),
				'%numfatturaultimo%' => trim($progressivoFattura->getNumFatturaUltimo()),
				'%notatestata%' => trim($progressivoFattura->getNotaTestaFattura()),
				'%notapiede%' => trim($progressivoFattura->getNotaPiedeFattura()),
		);
		$template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
		echo $utility->tailTemplate($template);
	}

	public function go()
	{
		$progressivoFattura = ProgressivoFattura::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$progressivoFattura->update($db);
		$progressivoFattura->load($db);
		
		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaProgressivoFattura::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}
}

?>