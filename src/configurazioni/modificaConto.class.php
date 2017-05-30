<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.business.interface.php';
require_once 'configurazioni.controller.class.php';
require_once 'ricercaConto.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class ModificaConto extends ConfigurazioniAbstract implements ConfigurazioniBusinessInterface {

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
		if (!isset($_SESSION[self::MODIFICA_CONTO])) $_SESSION[self::MODIFICA_CONTO] = serialize(new ModificaConto());
		return unserialize($_SESSION[self::MODIFICA_CONTO]);
	}

	public function start()
	{
		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$db = Database::getInstance();

		$conto->leggi($db);
		$_SESSION[self::CONTO] = serialize($conto);

		$sottoconto->setCodConto($conto->getCodConto());
		$sottoconto->leggi($db);
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);

		$datiPagina =
			$this->makeTabellaSottoconti($conto, $sottoconto) . "|" .
			$conto->getCodConto() . "|" .
			$conto->getDesConto() . "|" .
			$conto->getCatConto() . "|" .
			$conto->getTipConto() . "|" .
			$conto->getIndPresenzaInBilancio() . "|" .
			$conto->getIndVisibilitaSottoconti() . "|" .
			$conto->getNumRigaBilancio();

		echo $datiPagina;
	}

	public function go() {

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();

		if ($this->controlliLogici($conto, $sottoconto)) {

			// Aggiornamento del DB ------------------------------

			if ($this->aggiornaConto($utility, $conto, $sottoconto)) {
				$_SESSION[self::MSG_DA_MODIFICA_CONTO] = self::MODIFICA_CONTO_OK;
			}
		}
		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaConto::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}

	public function aggiornaConto($utility, $conto, $sottoconto) {

		$db = Database::getInstance();
		$db->beginTransaction();

		$conto->setDesConto(str_replace("'","''",$conto->getDesConto()));

		if ($conto->aggiorna($db)) {

			foreach ($sottoconto->getSottoconti() as $unSottoconto) {
				if ($unSottoconto[Sottoconto::DAT_CREAZIONE_SOTTOCONTO] == null) {
					$sottoconto->setCodConto($conto->getCodConto());
					$sottoconto->setCodSottoconto($unSottoconto[Sottoconto::COD_SOTTOCONTO]);
					$sottoconto->setDesSottoconto($unSottoconto[Sottoconto::DES_SOTTOCONTO]);
					$sottoconto->inserisci($db);
				}
			}

			$sottoconto->preparaNuoviSottoconti();
			$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
			$db->commitTransaction();
			return TRUE;
		}
		else {
			$db->rollbackTransaction();
			$_SESSION[self::MESSAGGIO] = self::ERRORE_SCRITTURA;
			return FALSE;
		}
	}

	public function controlliLogici($conto, $sottoconto) {

		$esito = TRUE;
		$msg = "<br>";

		/**
		 * Controllo presenza dati obbligatori
		 */

		if ($conto->getDesConto() == "") {
			$msg = $msg . self::ERRORE_DESCRIZIONE_CONTO;
			$esito = FALSE;
		}

		if ($msg != "<br>") {
			$_SESSION[self::MESSAGGIO] = $msg;
		}
		else {
			unset($_SESSION[self::MESSAGGIO]);
		}
		return $esito;
	}

	public function makeTabellaSottoconti($conto, $sottoconto)
	{
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$tbody = "";
		$thead =
			"<thead>" .
			"	<tr>" .
			"		<th width='100' align='center'>Sottoconto</th>" .
			"		<th width='400' align='left'>Descrizione</th>" .
			"		<th width='18'>Gruppo</th>" .
			"		<th>&nbsp;</th>" .
			"		<th>&nbsp;</th>" .
			"	</tr>" .
		    "</thead>";

		foreach ($sottoconto->getSottoconti() as $row)
		{
			$bottoneCancella = "<td width='28' align='right'>" . $row[Sottoconto::NUM_REG_SOTTOCONTO] . "</td>";

			if ($row[Sottoconto::NUM_REG_SOTTOCONTO] == 0) {
				$bottoneCancella = self::CANCELLA_SOTTOCONTO_HREF . $row[Sottoconto::COD_SOTTOCONTO] . "," . $sottoconto->getCodConto() . self::CANCELLA_SOTTOCONTO_ICON ;
			}

			if ($row[Sottoconto::IND_GRUPPO] == "") $indGruppo = "&ndash;&ndash;&ndash;";
			elseif ($row[Sottoconto::IND_GRUPPO] == self::NESSUNO) $indGruppo = "&ndash;&ndash;&ndash;";
			elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_FISSI) $indGruppo = "Costi Fissi";
			elseif ($row[Sottoconto::IND_GRUPPO] == self::COSTI_VARIABILI) $indGruppo = "Costi Variabili";
			elseif ($row[Sottoconto::IND_GRUPPO] == self::RICAVI) $indGruppo = "Ricavi";

			$tbody .=
			"<tr>" .
			"	<td>" . $row[Sottoconto::COD_SOTTOCONTO] . "</td>" .
			"	<td>" . $row[Sottoconto::DES_SOTTOCONTO] . "</td>" .
			"	<td>" . $indGruppo . "</td>" .
			self::MODIFICA_GRUPPO_SOTTOCONTO_HREF . '"' . $row[Sottoconto::IND_GRUPPO] . '","' . $row[Sottoconto::COD_SOTTOCONTO] . '","' . $row[Sottoconto::DES_SOTTOCONTO] . '","' . $row[Sottoconto::NUM_REG_SOTTOCONTO] . '"' . self::MODIFICA_GRUPPO_SOTTOCONTO_ICON .
			$bottoneCancella .
			"</tr>";
		}

		return "<table id='sottocontiTable_mod' class='result'>" . $thead . $tbody . "</table>";
	}


// 	public function preparaPagina($modificaContoTemplate) {

// 		$modificaContoTemplate->setAzione(self::AZIONE_MODIFICA_CONTO);
// 		$modificaContoTemplate->setConfermaTip("%ml.salvaTip%");
// 		$modificaContoTemplate->setTitoloPagina("%ml.modificaConto%");
// 	}
}

?>