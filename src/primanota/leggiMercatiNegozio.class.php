<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'mercato.class.php';

class LeggiMercatiNegozio extends PrimanotaAbstract implements PrimanotaBusinessInterface
{
	function __construct() {
		$this->root = $_SERVER['DOCUMENT_ROOT'];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::LEGGI_MERCATI_NEGOZIO])) $_SESSION[self::LEGGI_MERCATI_NEGOZIO] = serialize(new LeggiMercatiNegozio());
		return unserialize($_SESSION[self::LEGGI_MERCATI_NEGOZIO]);
	}

	public function start() {
		$this->go();
	}

	public function go()
	{
		$mercato = Mercato::getInstance();
		$db = Database::getInstance();
		$mercato->cercaMercatiNegozio($db);
		
		$elenco_mercati = "<select class='form-control' id='mercato_cormer_cre' name='mercato_cormer_cre'><option value=''></option>";

		if ($mercato->getQtaMercati() > 0) {
			foreach ($mercato->getMercati() as $unMercato) {
				$elenco_mercati .= "<option value='" . $unMercato[Mercato::ID_MERCATO] . "'>" . $unMercato[Mercato::DES_MERCATO] . "</option>";
			}
		}
		else {
			$elenco_mercati .= "<option value=''>Non ci sono mercati per il negozio</option>";
		}
		$elenco_mercati .= "</select>";
		echo $elenco_mercati;
	}
}

?>