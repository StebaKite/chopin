<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaFornitore.class.php';
require_once 'fornitore.class.php';

class RicercaScadenzeAperteFornitore extends PrimanotaAbstract implements PrimanotaBusinessInterface
{

	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_SCADENZE_FORNITORE_APERTE])) $_SESSION[self::RICERCA_SCADENZE_FORNITORE_APERTE] = serialize(new RicercaScadenzeAperteFornitore());
		return unserialize($_SESSION[self::RICERCA_SCADENZE_FORNITORE_APERTE]);
	}

	public function start()
	{

		$registrazione = Registrazione::getInstance();
		$scadenzaFornitore = ScadenzaFornitore::getInstance();
		$fornitore = Fornitore::getInstance();
		$db = Database::getInstance();

		$fornitore->cercaConDescrizione($db);
		$scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
		$scadenzaFornitore->trovaScadenzeDaPagare($db);

		$options = '<select class="numfatt-fornitore-multiple" multiple="multiple" style="width: 600px" id="numfatt_pag_cre">';

		if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0)
		{
			foreach($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza) {
				$options .= '<option value="' . trim($unaScadenza[ScadenzaFornitore::NUM_FATTURA]) . ',' . trim($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]) . '" >Ft.' . trim($unaScadenza[ScadenzaFornitore::NUM_FATTURA]) . ' - ' . trim($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]) .  ' - &euro; ' . trim($unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA]) . ' - (' . trim($unaScadenza[ScadenzaFornitore::NOTA_SCADENZA]) . ')</option>';
			}
		}
		$options .= '</select>';
		echo $options;
	}

	public function go() {
	}
}

?>