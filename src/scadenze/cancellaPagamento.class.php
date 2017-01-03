<?php

require_once 'scadenze.abstract.class.php';

class CancellaPagamento extends ScadenzeAbstract {

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new CancellaPagamento();

			return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {

		require_once 'database.class.php';
		require_once 'utility.class.php';
		require_once 'ricercaScadenze.class.php';

		$utility = Utility::getInstance();
		$db = Database::getInstance();

		/**
		 * Prelevo la data del pagamento da cancellare per ricalcolare i saldi
		 */

		$result = $this->leggiRegistrazione($db, $utility, $_SESSION["idPagamento"]);

		$db->beginTransaction();

		if ($result) {

			$registrazione = pg_fetch_all($result);
			foreach ($registrazione as $row) {
				$datareg = $row["dat_registrazione"];
			}
		}

		$this->cancellaRegistrazione($db, $utility, $_SESSION["idPagamento"]);

		/**
		 * Ripristino lo stato della scadenza in "Da Pagare" e tolgo l'id del pagamento associato
		 */

		$this->cambiaStatoScadenzaFornitore($db, $utility, $_SESSION["idScadenza"], "00");		
		
		/**
		 * Rigenero i saldi
		 */

		$array = $utility->getConfig();
			
		$dataRegistrazione = strtotime(str_replace('/', '-', $datareg));

		if ($array['lavoriPianificatiAttivati'] == "Si") {
			$this->rigenerazioneSaldi($db, $utility, $dataRegistrazione);
		}
			
		$db->commitTransaction();

		$_SESSION["messaggioCancellazione"] = "Pagamento cancellato";
		$ricercaScadenze = RicercaScadenze::getInstance();
		$ricercaScadenze->go();
	}
}

?>