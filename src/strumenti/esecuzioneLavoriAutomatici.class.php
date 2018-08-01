<?php

require_once 'strumenti.abstract.class.php';
require_once 'lavoroPianificato.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class EsecuzioneLavoriAutomatici extends StrumentiAbstract {

    private static $project_root = "/var/www/html";

    public function start() {

        error_log("Start lavori pianificati...");

        $utility = Utility::getInstance();
        $array = $utility->getConfigInBatchMode(self::$project_root);

        if ($array['lavoriPianificatiAttivati'] == "Si") {

            $lavoroPianificato = LavoroPianificato::getInstance();
            $db = Database::getInstance();


            if ($lavoroPianificato->load($db, self::$project_root)) {
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