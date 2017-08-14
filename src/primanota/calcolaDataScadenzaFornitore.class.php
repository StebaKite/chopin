<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'fornitore.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaFornitore.class.php';

class CalcolaDataScadenzaFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	public static $ggMese = array(
			'01' => '31',
			'02' => '28',
			'03' => '31',
			'04' => '30',
			'05' => '31',
			'06' => '30',
			'07' => '31',
			'08' => '31',
			'09' => '30',
			'10' => '31',
			'11' => '30',
			'12' => '31',
	);

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CALCOLA_DATA_SCADENZA_FORNITORE])) $_SESSION[self::CALCOLA_DATA_SCADENZA_FORNITORE] = serialize(new CalcolaDataScadenzaFornitore());
		return unserialize($_SESSION[self::CALCOLA_DATA_SCADENZA_FORNITORE]);
	}

	public function start()
	{
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$registrazione = Registrazione::getInstance();
		$fornitore = Fornitore::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();

		$fornitore->setDesFornitore($registrazione->getDesFornitore());
		$fornitore->cercaConDescrizione($db);

		/**
		 * Se i giorni scadenza fattura del fornitore sono = 0 non viene calcolata da data scadenza
		 */
		if ($fornitore->getNumGgScadenzaFattura() > 0) {
			/**
			 * Le data di registrazione viene aumentata dei giorni configurati per il fornitore,
			 * alla data ottenuta viene sostituito il giorno con l'ultimo giorno del mese corrispondente
			 */
			$dataScadenza = $this->sommaGiorniData($registrazione->getDatRegistrazione(), "/", $fornitore->getNumGgScadenzaFattura());

			$data = explode("/",$dataScadenza);
			$mese = $data[1];
			$anno = $data[2];

			$scadenzaFornitore->setDatScadenza(SELF::$ggMese[$mese]."/".$mese."/".$anno);
			$scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
			$scadenzaFornitore->setImpInScadenza(0);
			$scadenzaFornitore->setNumFattura("0");
			$scadenzaFornitore->aggiungi();

			echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore);
		}
		else {
			echo "";
		}
	}
	public function go() {}
}

?>