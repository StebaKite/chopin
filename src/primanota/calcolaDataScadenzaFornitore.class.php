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

		if ($registrazione->getIdFornitore() == "") {

			/**
			 * Devo eliminare da DB le scadenze del fornitore indicato nella registrazione
			 * Queste scadenze si trovano nell'oggetto ScadenzeFornitore, elimino solo quelle da pagare.
			 */ 
			$scadenzaFornitore->rimuoviScadenzeRegistrazione($db);
			echo "<div class='alert alert-warning' role='alert'>Scadenze esistenti eliminate, nessuna scadenza presente.</div>";
		}
		else {
			$fornitore->setIdFornitore($registrazione->getIdFornitore());
			$fornitore->leggi($db);

			/**
			 * Verifico se ci sono gia' scadenze significa che è stato cambiato il fornitore.
			 * Verifico se l'id del nuovo fornitore è diverso da quello delle scadenze, 
			 *   - se si, aggiorno l'id del fornitore e il tipo addebito di tutte le scadenze della registrazione
			 *   - se no, calcolo la nuova scadenza del fornitore
			 */ 
			
			if ($scadenzaFornitore->getQtaScadenzeDaPagare() == 0) 
			{
				$scadenzaFornitore->setQtaScadenzeDaPagare(0);
				$scadenzaFornitore->setScadenzeDaPagare("");
				$dataScadenza = $this->calcolaDataScadenza($registrazione->getDatRegistrazione(),$fornitore->getNumGgScadenzaFattura());
				/**
				 * Se i giorni scadenza fattura del fornitore sono = 0 non viene calcolata da data scadenza
				 */
				if ($dataScadenza != "") {
					
					$scadenzaFornitore->setDatScadenza($dataScadenza);					
					$scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
					$scadenzaFornitore->setImpInScadenza(0);
					$scadenzaFornitore->setNumFattura("0");
					$scadenzaFornitore->aggiungi();
					
					echo $this->makeTabellaScadenzeFornitore($scadenzaFornitore);
				}
				else {
					echo "<div class='alert alert-info' role='alert'>Il fornitore ha il numero di giorni scadenza fatture impostato a zero.</div>";
				}
			}
			else {
			
				foreach ($scadenzaFornitore->getScadenzeDaPagare() as $unaScadenza)
				{
					$scadenzaFornitore->setIdFornitoreOrig($unaScadenza[ScadenzaFornitore::ID_FORNITORE]);
					$scadenzaFornitore->setDatScadenza($unaScadenza[ScadenzaFornitore::DAT_SCADENZA]);
					$scadenzaFornitore->setNumFatturaOrig($registrazione->getNumFatturaOrig());
					
					$scadenzaFornitore->setImpInScadenza($unaScadenza[ScadenzaFornitore::IMP_IN_SCADENZA]);
					$scadenzaFornitore->setNotaScadenza($unaScadenza[ScadenzaFornitore::NOTA_SCADENZA]);
					$scadenzaFornitore->setTipAddebito($fornitore->getTipAddebito());
					$scadenzaFornitore->setCodNegozio($unaScadenza[ScadenzaFornitore::COD_NEGOZIO]);
					$scadenzaFornitore->setIdFornitore($fornitore->getIdFornitore());
					$scadenzaFornitore->setNumFattura($unaScadenza[ScadenzaFornitore::NUM_FATTURA]);
					$scadenzaFornitore->setStaScadenza($unaScadenza[ScadenzaFornitore::STA_SCADENZA]);
					
					/**
					 * L'aggiornamento delle scadenze non riguarda le date di scadenza impostate con il fornitore 
					 * precedente. Viene aggiornato solo l'id del fornitore e il tipo di addebito.
					 */
					$scadenzaFornitore->aggiorna($db);
				}
			}
		}
	}
	public function go() {}
}

?>