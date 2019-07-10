<?php

require_once 'strumenti.abstract.class.php';
require_once 'lavoroPianificato.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class EsecuzioneLavoriAutomatici extends StrumentiAbstract {

    private static $linuxProjectRoot = "/var/www/html";
    private static $windowsProjectRoot = "D:\Programmi\Apache24\Apache24\htdocs";
    
    public function start() {

        error_log("Start lavori pianificati...");

        $utility = Utility::getInstance();

        $so = shell_exec("uname -a");
        error_log("Operating system detected : ". $so);
        
        if (strpos($so, 'Windows') === false) {
            $project_root = self::$linuxProjectRoot;
        } else {
            $project_root = self::$windowsProjectRoot;
        }
        
        $array = $utility->getConfigInBatchMode($project_root);

        if ($array['lavoriPianificatiAttivati'] == "Si") {

            $lavoroPianificato = LavoroPianificato::getInstance();
            $db = Database::getInstance();


            if ($lavoroPianificato->load($db, $project_root)) {
                $db->beginTransaction();
                $lavoroPianificato->esegui($db);
                $db->commitTransaction();
            }
        } else {
            error_log("ATTENZIONE: Lavori pianificati non attivi!!");
        }

        error_log("...End lavori pianificati");
    }

}

?>