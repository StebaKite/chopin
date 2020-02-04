<?php

require_once 'nexus6.abstract.class.php';
require_once 'strumenti.presentation.interface.php';

abstract class StrumentiAbstract implements StrumentiPresentationInterface {

    public $menu;
    
    public function intestazione($dati): string {
        
        $tab = "<div class='row'>" .
                "    <div class='col-sm-4'>" .
                "        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
                "    </div>" .
                "    <div class='col-sm-8'>" . $this->getIndexSession(self::MSG) . "</div>" .
                "</div>" .
                "<br/>" .
                "<table class='table table-bordered table-hover'>" .
                "   <thead>" .
                "       <tr>";
        
        foreach ($dati as $label) {
            $tab .= "<th>" . $label . "</th>";
        }        
        $tab .= "</tr></thead><tbody id='myTable'>";
        return $tab;
    }
    
    public function intestazioneCorrispettiviNegozio($dati): string {
        
        $tab = "<table class='table table-bordered table-hover'><thead><tr>";
        foreach ($dati as $label) {
            $tab .= "<th>" . $label . "</th>";
        }        
        $tab .= "</tr></thead><tbody id='myTable'>";
        return $tab;
    }
    
    public function intestazionePresenzeAssistito($dati): string {
        
        $tab = "<table class='table table-bordered table-hover'><thead><tr>";
        foreach ($dati as $label) {
            $tab .= "<th>" . $label . "</th>";
        }        
        $tab .= "</tr></thead><tbody id='myTable'>";
        return $tab;
    }
    

    // Composizione del menu in testata pagine --------------------------------------------

    public function makeMenu($utility): string {

        $array = $utility->getConfig();

        $ambiente = $this->getIndexSession(self::AMBIENTE) !== NULL ? $this->getIndexSession(self::AMBIENTE) : $this->getEnvironment($array);

        // H o m e --------------------------------------

        $home = "";

//        <li class="dropdown">
//            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
//            <ul class="dropdown-menu">
//                <li><a href="#">Action</a></li>
//                <li><a href="#">Another action</a></li>
//                <li><a href="#">Something else here</a></li>
//                <li role="separator" class="divider"></li>
//                <li><a href="#">Separated link</a></li>
//                <li role="separator" class="divider"></li>
//                <li><a href="#">One more separated link</a></li>
//            </ul>
//        </li>


        if ($array["home"] == "Y") {
            $home .= "<li class='dropdown'>";
            $home .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['home_menu_title'];
            $home .= "<span class='caret'></span></a>";
            $home .= "<ul class='dropdown-menu'>";

            if ($array["home_item_1"] == "Y")
                $home .= "<li><a href='../strumenti/cambiaContoStep1Facade.class.php?modo=start'>" . $array["home_item_1_name"] . "</a></li>";
            if ($array["home_item_2"] == "Y")
                $home .= "<li><a href='../strumenti/lavoriAutomaticiFacade.class.php?modo=start'>" . $array["home_item_2_name"] . "</a></li>";

            $home .= "</ul></li>";
        }
        $this->menu .= $home;

        // O p er a z i o n i ------------------------------------------------------------

        $operazioni = "";

        if ($array["operazioni"] == "Y") {
            $operazioni .= "<li class='dropdown'>";
            $operazioni .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['operazioni_menu_title'];
            $operazioni .= "<span class='caret'></span></a>";
            $operazioni .= "<ul class='dropdown-menu'>";

            if ($array["operazioni_item_1"] == "Y")
                $operazioni .= "<li><a href='../primanota/ricercaRegistrazioneFacade.class.php?modo=start'>" . $array["operazioni_item_1_name"] . "</a></li>";
            
            $operazioni .= "</ul></li>";
        }
        $this->menu .= $operazioni;

        // A n a g r a f i c h e ------------------------------------------------------------

        $anagrafiche = "";

        if ($array["anagrafiche"] == "Y") {
            $anagrafiche .= "<li class='dropdown'>";
            $anagrafiche .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['anagrafiche_menu_title'];
            $anagrafiche .= "<span class='caret'></span></a>";
            $anagrafiche .= "<ul class='dropdown-menu'>";

            if ($array["anagrafiche_item_3"] == "Y")
                $anagrafiche .= "<li><a href='../anagrafica/ricercaFornitoreFacade.class.php?modo=start'>" . $array["anagrafiche_item_3_name"] . "</a></li>";
            if ($array["anagrafiche_item_4"] == "Y")
                $anagrafiche .= "<li><a href='../anagrafica/ricercaClienteFacade.class.php?modo=start'>" . $array["anagrafiche_item_4_name"] . "</a></li>";
            if ($array["anagrafiche_item_5"] == "Y")
                $anagrafiche .= "<li><a href='../anagrafica/ricercaMercatoFacade.class.php?modo=start'>" . $array["anagrafiche_item_5_name"] . "</a></li>";

            $anagrafiche .= "</ul></li>";
        }
        $this->menu .= $anagrafiche;

        // C o n f i g u r a z i o n i ------------------------------------------------------------

        $configurazioni = "";

        if ($array["configurazioni"] == "Y") {
            $configurazioni .= "<li class='dropdown'>";
            $configurazioni .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['configurazioni_menu_title'];
            $configurazioni .= "<span class='caret'></span></a>";
            $configurazioni .= "<ul class='dropdown-menu'>";

            if ($array["configurazioni_item_2"] == "Y")
                $configurazioni .= "<li><a href='../configurazioni/ricercaContoFacade.class.php?modo=start'>" . $array["configurazioni_item_2_name"] . "</a></li>";
            if ($array["configurazioni_item_4"] == "Y")
                $configurazioni .= "<li><a href='../configurazioni/ricercaCausaleFacade.class.php?modo=start'>" . $array["configurazioni_item_4_name"] . "</a></li>";
            if ($array["configurazioni_item_5"] == "Y")
                $configurazioni .= "<li><a href='../configurazioni/ricercaProgressivoFatturaFacade.class.php?modo=start'>" . $array["configurazioni_item_5_name"] . "</a></li>";

            $configurazioni .= "</ul></li>";
        }
        $this->menu .= $configurazioni;

        // S c a d e n z e ------------------------------------------------------------

        $scadenze = "";

        if ($array["scadenze"] == "Y") {
            $scadenze .= "<li class='dropdown'>";
            $scadenze .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['scadenze_menu_title'];
            $scadenze .= "<span class='caret'></span></a>";
            $scadenze .= "<ul class='dropdown-menu'>";

            if ($array["scadenze_item_1"] == "Y")
                $scadenze .= "<li><a href='../scadenze/ricercaScadenzeFornitoreFacade.class.php?modo=start'>" . $array["scadenze_item_1_name"] . "</a></li>";
            if ($array["scadenze_item_2"] == "Y")
                $scadenze .= "<li><a href='../scadenze/ricercaScadenzeClienteFacade.class.php?modo=start'>" . $array["scadenze_item_2_name"] . "</a></li>";

            $scadenze .= "</ul></li>";
        }
        $this->menu .= $scadenze;

        // R i e p i o l o g h i ------------------------------------------------------------

        $riepiloghi = "";

        if ($array["riepiloghi"] == "Y") {
            $riepiloghi .= "<li class='dropdown'>";
            $riepiloghi .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['riepiloghi_menu_title'];
            $riepiloghi .= "<span class='caret'></span></a>";
            $riepiloghi .= "<ul class='dropdown-menu'>";

            if ($array["riepiloghi_item_1"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/generaBilancioEsercizioFacade.class.php?modo=start'>" . $array["riepiloghi_item_1_name"] . "</a></li>";
            if ($array["riepiloghi_item_2"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/generaBilancioPeriodicoFacade.class.php?modo=start'>" . $array["riepiloghi_item_2_name"] . "</a></li>";

            $riepiloghi .= "<li role='separator' class='divider'></li>";

            if ($array["riepiloghi_item_3"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/riepilogoNegoziFacade.class.php?modo=start'>" . $array["riepiloghi_item_3_name"] . "</a></li>";
            if ($array["riepiloghi_item_4"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/andamentoNegoziFacade.class.php?modo=start'>" . $array["riepiloghi_item_4_name"] . "</a></li>";
            if ($array["riepiloghi_item_7"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/andamentoNegoziConfrontatoFacade.class.php?modo=start'>" . $array["riepiloghi_item_7_name"] . "</a></li>";
            if ($array["riepiloghi_item_8"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/andamentoMercatiFacade.class.php?modo=start'>" . $array["riepiloghi_item_8_name"] . "</a></li>";
            if ($array["riepiloghi_item_5"] == "Y")
                $riepiloghi .= "<li><a href='../saldi/ricercaSaldiFacade.class.php?modo=start'>" . $array["riepiloghi_item_5_name"] . "</a></li>";
            if ($array["riepiloghi_item_6"] == "Y")
                $riepiloghi .= "<li><a href='../saldi/creaSaldoFacade.class.php?modo=start'>" . $array["riepiloghi_item_6_name"] . "</a></li>";

            $riepiloghi .= "<li role='separator' class='divider'></li>";

            if ($array["riepiloghi_item_9"] == "Y")
                $riepiloghi .= "<li><a href='../riepiloghi/generaQuadroPresenzeAssistitiFacade.class.php?modo=start'>" . $array["riepiloghi_item_9_name"] . "</a></li>";

            $riepiloghi .= "</ul></li>";
        }
        $this->menu .= $riepiloghi;

        // F a t t u r e ------------------------------------------------------------

        $fatture = "";

        if ($array["fatture"] == "Y") {
            $fatture .= "<li class='dropdown'>";
            $fatture .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array["fatture_menu_title"];
            $fatture .= "<span class='caret'></span></a>";
            $fatture .= "<ul class='dropdown-menu'>";

            if ($array["fatture_item_1"] == "Y")
                $fatture .= "<li><a href='../fatture/creaFatturaAziendaConsortileFacade.class.php?modo=start'>" . $array["fatture_item_1_name"] . "</a></li>";
            if ($array["fatture_item_2"] == "Y")
                $fatture .= "<li><a href='../fatture/creaFatturaEntePubblicoFacade.class.php?modo=start'>" . $array["fatture_item_2_name"] . "</a></li>";
            if ($array["fatture_item_3"] == "Y")
                $fatture .= "<li><a href='../fatture/creaFatturaClienteFacade.class.php?modo=start'>" . $array["fatture_item_3_name"] . "</a></li>";
            
            $fatture .= "<li><a href='../fatture/creaFatturaClienteXMLFacade.class.php?modo=start'>FatturaXML</a></li>";            
            $fatture .= "</ul></li>";
        }
        $this->menu .= $fatture;

        // S t r u m e n t i ------------------------------------------------------------

        $strumenti = "";
        
        if ($array["strumenti"] == "Y") {
            $strumenti .= "<li class='dropdown'>";
            $strumenti .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array["strumenti_menu_title"];
            $strumenti .= "<span class='caret'></span></a>";
            $strumenti .= "<ul class='dropdown-menu'>";
            
            if ($array["strumenti_item_1"] == "Y")
                $strumenti .= "<li><a href='../strumenti/cambiaContoStep1Facade.class.php?modo=start'>" . $array["strumenti_item_1_name"] . "</a></li>";

            $strumenti .= "<li role='separator' class='divider'></li>";
            
            if ($array["strumenti_item_2"] == "Y")
                $strumenti .= "<li><a href='../strumenti/importaExcelCorrispettiviNegozioStep1Facade.class.php?modo=start'>" . $array["strumenti_item_2_name"] . "</a></li>";
            
            if ($array["strumenti_item_3"] == "Y")
                $strumenti .= "<li><a href='../strumenti/importaExcelCorrispettiviMercatoStep1Facade.class.php?modo=start'>" . $array["strumenti_item_3_name"] . "</a></li>";
            
            if ($array["strumenti_item_4"] == "Y")
                $strumenti .= "<li><a href='../strumenti/importaExcelPresenzeAssistitiStep1Facade.class.php?modo=start'>" . $array["strumenti_item_4_name"] . "</a></li>";
            
            if ($array["strumenti_item_5"] == "Y")
                $strumenti .= "<li><a href='../strumenti/esecuzioneOnlineLavoriAutomaticiFacade.class.php?modo=start'>" . $array["strumenti_item_5_name"] . "</a></li>";

            $strumenti .= "</ul></li>";
        }
        $this->menu .= $strumenti;
        
        
        return $this->menu;
    }
    
    public function getInfoFromServer($infoName) {        
        if (null !== filter_input(INPUT_SERVER, $infoName)) {
            return filter_input(INPUT_SERVER, $infoName);            
        }
        return null;
    }
    
    public static function getIndexSession($indexName) {    
        return (isset($_SESSION[$indexName])) ? $_SESSION[$indexName] : null;
    }
    
    public static function setIndexSession($indexName, $indexValue) {
        $_SESSION[$indexName] = $indexValue;
    }
    
    public static function unsetIndexSessione($indexName) {
        unset($_SESSION[$indexName]);
    }
    
    function isEmpty($param) {
        if (($param == "") or ( $param == " ") or ( $param == null))
            return TRUE;
        else
            return FALSE;
    }

    function isNotEmpty($param) {
        if (($param != "") and ( $param != " ") and ( $param != null))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Questo metodo setta come da eseguire le prima data utile di riporto saldo e tutte le successive
     * @param type $db
     * @param type $datRegistrazione
     */
    public function ricalcolaSaldi($db, $datRegistrazione) {
        $lavoroPianificato = LavoroPianificato::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        if ($array['lavoriPianificatiAttivati'] == "Si") {
            $lavoroPianificato->setDatRegistrazione(str_replace('/', '-', $datRegistrazione));
            $lavoroPianificato->settaDaEseguire($db);
        }
    }
}
