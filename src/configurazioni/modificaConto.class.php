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
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$conto->leggi($db);
		$_SESSION[self::CONTO] = serialize($conto);

		$sottoconto->setCodConto($conto->getCodConto());
		$sottoconto->leggi($db);
		$_SESSION[self::SOTTOCONTO] = serialize($sottoconto);
		
		$risultato_xml = $this->root . $array['template'] . self::XML_CONTO;
		
		$replace = array(
				'%codice%' => trim($conto->getCodConto()),
				'%descrizione%' => trim($conto->getDesConto()),
				'%categoria%' => trim($conto->getCatConto()),
				'%tipo%' => trim($conto->getTipConto()),
				'%presenzaInBilancio%' => trim($conto->getIndPresenzaInBilancio()),
				'%presenzaSottoconti%' => trim($conto->getIndVisibilitaSottoconti()),
				'%numeroRigaBilancio%' => trim($conto->getNumRigaBilancio()),
				'%sottoconti%' => $this->makeTabellaSottoconti($conto, $sottoconto)
		);
		$template = $utility->tailFile($utility->getTemplate($risultato_xml), $replace);
		echo $utility->tailTemplate($template);
	}

	public function go() {

		$conto = Conto::getInstance();
		$sottoconto = Sottoconto::getInstance();
		$utility = Utility::getInstance();

		$this->aggiornaConto($utility, $conto, $sottoconto);

		$_SESSION["Obj_configurazionicontroller"] = serialize(new ConfigurazioniController(RicercaConto::getInstance()));
		$controller = unserialize($_SESSION["Obj_configurazionicontroller"]);
		$controller->start();
	}

	public function aggiornaConto($utility, $conto, $sottoconto) {

		$db = Database::getInstance();
		$db->beginTransaction();

		if ($conto->aggiorna($db)) {

			foreach ($sottoconto->getSottoconti() as $unSottoconto) {
				if ($unSottoconto[Sottoconto::DAT_CREAZIONE_SOTTOCONTO] == null) {
					$sottoconto->setCodConto($conto->getCodConto());
					$sottoconto->setCodSottoconto($unSottoconto[Sottoconto::COD_SOTTOCONTO]);
					$sottoconto->setDesSottoconto($unSottoconto[Sottoconto::DES_SOTTOCONTO]);
					$sottoconto->setIndGruppo($unSottoconto[Sottoconto::IND_GRUPPO]);
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
}

?>