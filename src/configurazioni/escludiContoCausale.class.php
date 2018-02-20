<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'configuraCausale.class.php';

class EscludiContoCausale extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::ESCLUDI_CONTO_CAUSALE])) $_SESSION[self::ESCLUDI_CONTO_CAUSALE] = serialize(new EscludiContoCausale());
		return unserialize($_SESSION[self::ESCLUDI_CONTO_CAUSALE]);
	}

	public function start()
	{
		$configurazioneCausale = ConfigurazioneCausale::getInstance();
		$utility = Utility::getInstance();
		$db = Database::getInstance();
		$array = $utility->getConfig();
		
		$configurazioneCausale->cancellaConto($db);
		$_SESSION[self::CONFIGURAZIONE_CAUSALE] = serialize($configurazioneCausale);
		
		$risultato_xml = $this->root . $array['template'] . self::XML_CAUSALE;
		
		$replace = array(
				'%conticonfigurati%' => trim($this->makeTableContiConfigurati($configurazioneCausale)),
				'%contidisponibili%' => trim($this->makeTableContiConfigurabili($configurazioneCausale))
		);
		$template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
		echo $utility->tailTemplate($template);
	}

	public function go() {}
}

?>