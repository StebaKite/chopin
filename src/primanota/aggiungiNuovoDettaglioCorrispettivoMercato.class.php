<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'sottoconto.class.php';

class AggiungiNuovoDettaglioCorrispettivoMercato extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO])) $_SESSION[self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO] = serialize(new AggiungiNuovoDettaglioCorrispettivoMercato());
		return unserialize($_SESSION[self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_MERCATO]);
	}

	public function start() {
		$this->go();
	}

	public function go()
	{
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$dettaglioRegistrazione = $this->aggiungiDettagliCorrispettivo($db, $utility, $array);
		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);
		echo $this->makeTabellaDettagliRegistrazione($dettaglioRegistrazione);
	}
}

?>