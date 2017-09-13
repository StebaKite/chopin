<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'sottoconto.class.php';

class AggiungiNuovoDettaglioCorrispettivoNegozio extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {

		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO])) $_SESSION[self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO] = serialize(new AggiungiNuovoDettaglioCorrispettivoNegozio());
		return unserialize($_SESSION[self::AGGIUNGI_DETTAGLIO_CORRISPETTIVO_NEGOZIO]);
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