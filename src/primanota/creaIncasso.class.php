<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'primanota.controller.class.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'causale.class.php';
require_once 'ricercaRegistrazione.class.php';
require_once 'scadenzaCliente.class.php';
require_once 'cliente.class.php';
require_once 'lavoroPianificato.class.php';


class CreaIncasso extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::CREA_INCASSO])) $_SESSION[self::CREA_INCASSO] = serialize(new CreaIncasso());
		return unserialize($_SESSION[self::CREA_INCASSO]);
	}

	public function start()
	{
	    $scadenzaCliente = ScadenzaCliente::getInstance();
	    $scadenzaCliente->setIdTableScadenzeAperte("scadenze_aperte_inc_cre");
	    $scadenzaCliente->setIdTableScadenzeChiuse("scadenze_chiuse_inc_cre");
	    $scadenzaCliente->setScadenzeIncassate("");
	    $scadenzaCliente->setQtaScadenzeIncassate(0);
	    $scadenzaCliente->setScadenzeDaIncassare("");
	    $scadenzaCliente->setQtaScadenzeDaIncassare(0);
	    
	    $_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
	    echo "Ok";
	}

	public function go()
	{
		$registrazione = Registrazione::getInstance();
		$dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
		$utility = Utility::getInstance();

		if ($this->creaIncasso($utility, $registrazione, $dettaglioRegistrazione))
			$_SESSION[self::MSG_DA_CREAZIONE] = self::CREA_INCASSO_OK;
		else $_SESSION[self::MSG_DA_CREAZIONE] = self::ERRORE_CREAZIONE_REGISTRAZIONE;

		$_SESSION["Obj_primanotacontroller"] = serialize(new PrimanotaController(RicercaRegistrazione::getInstance()));
		$controller = unserialize($_SESSION["Obj_primanotacontroller"]);
		$controller->start();
	}

	public function creaIncasso($utility, $registrazione, $dettaglioRegistrazione)
	{
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$cliente = Cliente::getInstance();
		$db = Database::getInstance();
		$db->beginTransaction();

		if ($registrazione->inserisci($db))
		{
			foreach ($dettaglioRegistrazione->getDettagliRegistrazione() as $unDettaglio) {
				$this->creaDettaglioIncasso($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio);
			}

			/**
			 * Riconciliazione delle fatture indicate con chiusura delle rispettive scadenze
			 */
			$riconciliazioneFattureOkay = true;

			foreach ($scadenzaCliente->getScadenzeIncassate() as $unaScadenza)
			{
			    $scadenzaCliente->setIdCliente($cliente->getIdCliente());
			    $scadenzaCliente->setIdIncasso($registrazione->getIdRegistrazione());
			    $scadenzaCliente->setStaScadenza("10");		// incassata e chiusa
			    
			    $scadenzaCliente->setNumFattura($unaScadenza[ScadenzaCliente::NUM_FATTURA]);
			    $scadenzaCliente->setDatRegistrazione($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);
			    
			    if (!$scadenzaCliente->cambiaStato($db))
			    {
			        $riconciliazioneFattureOkay = false;
			        break;
			    }
			}

			/***
			 * Ricalcolo i saldi dei conti
			 */
			if ($riconciliazioneFattureOkay) {
				$this->ricalcolaSaldi($db, $registrazione->getDatRegistrazione());
				$db->commitTransaction();
				return true;
			}
			else {
				$db->rollbackTransaction();
				return false;
			}
		}
		else {
			$db->rollbackTransaction();
			return false;
		}
	}

	public function creaDettaglioIncasso($db, $utility, $registrazione, $dettaglioRegistrazione, $unDettaglio)
	{
		$_cc = explode(" - ", $unDettaglio[DettaglioRegistrazione::COD_CONTO]);	// il codconto del dettaglio contiene anche la descrizione
		$conto = explode(".", $_cc[0]);		// conto e sottoconto separati da un punto

		$dettaglioRegistrazione->setIdRegistrazione($registrazione->getIdRegistrazione());
		$dettaglioRegistrazione->setCodConto($conto[0]);
		$dettaglioRegistrazione->setCodSottoconto($conto[1]);
		$dettaglioRegistrazione->setImpRegistrazione($unDettaglio[DettaglioRegistrazione::IMP_REGISTRAZIONE]);
		$dettaglioRegistrazione->setIndDareavere($unDettaglio[DettaglioRegistrazione::IND_DAREAVERE]);

		if (!$dettaglioRegistrazione->inserisci($db)) {
			$db->rollbackTransaction();
			return false;
		}
		return true;
	}
}

?>
