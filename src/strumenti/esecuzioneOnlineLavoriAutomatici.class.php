<?php

require_once 'strumenti.abstract.class.php';
require_once 'lavoroPianificato.class.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'main.template.php';

class EsecuzioneOnlineLavoriAutomatici extends StrumentiAbstract {

    public function start() {

        error_log("Start online lavori pianificati...");

        $utility = Utility::getInstance();
        $array = $utility->getConfig();
        
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agent, 'Windows') === false) {
            $project_root = $array['linuxProjectRoot'];
        } else {
            $project_root = $array['windowsProjectRoot'];
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

        error_log("...End lavori pianificati online");
        
        $mainTemplate = MainTemplate::getInstance();
        $mainTemplate->displayPagina();
    }
}

?>