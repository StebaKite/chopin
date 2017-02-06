<?php

/*****************************************************************************************************
 * 
 * Questa script esegue tutti i lavori automatici che trova da eseguire antecedenti la data odierna.
 * Deve essere inserita in Crontab ed eseguita al 45esimo minuto di ogni ora
 *
 * Dopo l'esecuzione di questa script può essere eseguita la script di backup.
 *
 * @author stefano
 *
 */

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