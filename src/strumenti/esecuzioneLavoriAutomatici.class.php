<?php

require_once 'chopin.abstract.class.php';

class EsecuzioneLavoriAutomatici extends ChopinAbstract {

	private static $project_root = "/var/www/html";
	
	public function start() {
		
		echo "Start lavori pianificati...\n";
		
		require_once 'utility.class.php';
		require_once 'database.class.php';
		
		$utility = Utility::getInstance();		
		$array = $utility->getConfigInBatchMode(self::$project_root);
		
		if ($array['lavoriPianificatiAttivati'] == "Si") {
				
			$db = Database::getInstance();
		
			$db->beginTransaction();
		
			$lavoriPianificati = $this->leggiLavoriPianificatiBatchMode($db, $utility, self::$project_root);
		
			if ($lavoriPianificati) {
		
				$this->eseguiLavoriPianificati($db, $lavoriPianificati, self::$project_root);
				$db->commitTransaction();
			}
		}
		else {
			echo "ATTENZIONE: Lavori pianificati non attivi!!\n";
		}

		echo "...End lavori pianificati\n";
	
	}
	
}
?>