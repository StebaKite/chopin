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
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$dettaglioRegistrazione->setIdDettaglioRegistrazione(0);
		$dettaglioRegistrazione->setIdRegistrazione(0);
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();
		$sottoconto = Sottoconto::getInstance();

		/**
		 * Dettaglio sul conto selezionato
		 */
		$_cc = explode(".", $dettaglioRegistrazione->getCodConto());
		$sottoconto->setCodConto($_cc[0]);
		$sottoconto->setCodSottoconto($_cc[1]);
		$sottoconto->leggi($db);
		$sottoconto->searchSottoconto($_cc[1]);

		$dettaglioRegistrazione->setCodConto($dettaglioRegistrazione->getCodConto() . " - " . $sottoconto->getDesSottoconto());
		$dettaglioRegistrazione->setIndDareAvere("D");
		$dettaglioRegistrazione->aggiungi();

		/**
		 * Dettaglio conto erario
		 */
		$dettaglioRegistrazione->setCodConto($array['contoErarioMercati']);
		$dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImpIva());
		$dettaglioRegistrazione->setIndDareAvere("A");
		$dettaglioRegistrazione->aggiungi();

		/**
		 * Dettaglio Cassa/Banca
		 */
		$dettaglioRegistrazione->setCodConto($array['contoCorrispettivoMercati']);
		$dettaglioRegistrazione->setImpRegistrazione($dettaglioRegistrazione->getImponibile());
		$dettaglioRegistrazione->setIndDareAvere("A");
		$dettaglioRegistrazione->aggiungi();

		$_SESSION[self::DETTAGLIO_REGISTRAZIONE] = serialize($dettaglioRegistrazione);

		echo $this->makeTabellaDettagliRegistrazione($dettaglioRegistrazione);
	}
}

?>