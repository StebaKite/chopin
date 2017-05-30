<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'registrazione.class.php';
require_once 'lavoroPianificato.class.php';
require_once 'ricercaScadenzeFornitore.class.php';

class CancellaPagamento extends ScadenzeAbstract implements ScadenzeBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();

		$this->testata = $this->root . $this->array[self::TESTATA];
		$this->piede = $this->root . $this->array[self::PIEDE];
		$this->messaggioErrore = $this->root . $this->array[self::ERRORE];
		$this->messaggioInfo = $this->root . $this->array[self::INFO];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CANCELLA_PAGAMENTO])) $_SESSION[self::CANCELLA_PAGAMENTO] = serialize(new CancellaPagamento());
		return unserialize($_SESSION[self::CANCELLA_PAGAMENTO]);
	}

	public function go()
	{
		$lavoroPianificato = LavoroPianificato::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$registrazione = Registrazione::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();

		$db->beginTransaction();

		/**
		 * Prelevo la data del pagamento da cancellare per ricalcolare i saldi
		 */

		$registrazione->leggi($db);		// prelevo la data del pagamento per ricalcolare i saldi
		$_SESSION[self::REGISTRAZIONE] = serialize($registrazione);
		$registrazione->cancella($db);	// cancello il pagamento

		/**
		 * Ripristino lo stato della scadenza in "Da Pagare"
		 * L'id del pagamento associato viene settato a null dalla delete rule definita sulla fk
		 */
		$scadenzaFornitore->setStaScadenza(self::SCADENZA_APERTA);
		$scadenzaFornitore->cambiaStato($db);

		/**
		 * Setto le date di riporto saldo come da eseguire sulla base della data di registrazione del
		 * pagamento
		 */

		$array = $utility->getConfig();

		$lavoroPianificato->setDatRegistrazione(str_replace('/', '-', $registrazione->getDatRegistrazione()));

		if ($array['lavoriPianificatiAttivati'] == "Si") {
			$lavoroPianificato->settaDaEseguire($db);
			$_SESSION[self::LAVORO_PIANIFICATO] = serialize($lavoroPianificato);
		}

		$db->commitTransaction();

		$_SESSION[self::MSG_DA_CANCELLAZIONE] = self::CANCELLA_PAGAMENTO_OK;

		$_SESSION["Obj_scadenzecontroller"] = serialize(new ScadenzeController(RicercaScadenzeFornitore::getInstance()));
		$controller = unserialize($_SESSION["Obj_scadenzecontroller"]);
		$controller->start();
	}

	public function start() {
		$this->go();
	}
}

?>