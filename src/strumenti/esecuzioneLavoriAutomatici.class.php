<?php

require_once 'strumenti.abstract.class.php';
require_once 'lavoroPianificato.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';

class EsecuzioneLavoriAutomatici extends StrumentiAbstract {

    private static $project_root = "/var/www/html";

    public function start() {

        echo "Start lavori pianificati...\n";

        $utility = Utility::getInstance();
        $array = $utility->getConfigInBatchMode(self::$project_root);

        if ($array['lavoriPianificatiAttivati'] == "Si") {

            $lavoroPianificato = LavoroPianificato::getInstance();
            $db = Database::getInstance();


            if ($lavoroPianificato->load($db)) {
                $db->beginTransaction();
                $lavoroPianificato->esegui($db);
                $db->commitTransaction();
            }
        } else {
            echo "ATTENZIONE: Lavori pianificati non attivi!!\n";
        }

        echo "...End lavori pianificati\n";
    }

}

?>