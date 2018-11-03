<?php

require_once 'lavoroPianificato.class.php';

abstract class Nexus6Abstract {

    public static $root;
    public static $testata;
    public static $piede;
    public static $messaggioInfo;
    public static $messaggioErrore;
    public static $azione;
    public static $testoAzione;
    public static $titoloPagina;
    public static $confermaTip;
    public static $replace;
    public static $elenco_causali;
    public static $elenco_fornitori;
    public static $elenco_clienti;
    public static $elenco_conti;
    public static $elenco_mercati;
    public static $errorStyle = "border-color:#ff0000; border-width:1px;";

    /*
     * Query ------------------------------------------------------------------------------
     */
    public static $queryControllaScadenzeFornitoreSuperate = "/main/controllaScadenzeFornitoreSuperate.sql";
    public static $queryControllaScadenzeClienteSuperate = "/main/controllaScadenzeClienteSuperate.sql";
    public static $queryControllaRegistrazioniInErrore = "/main/controllaRegistrazioniInErrore.sql";

    // Setters -----------------------------------------------------------------------------

    public function setTestata($testata) {
        self::$testata = $testata;
    }

    public function setPiede($piede) {
        self::$piede = $piede;
    }

    public function setAzione($azione) {
        self::$azione = $azione;
    }

    public function setTestoAzione($testoAzione) {
        self::$testoAzione = $testoAzione;
    }

    public function setTitoloPagina($titoloPagina) {
        self::$titoloPagina = $titoloPagina;
    }

    public function setConfermaTip($tip) {
        self::$confermaTip = $tip;
    }

    // Getters -----------------------------------------------------------------------------

    public function getTestata() {
        return self::$testata;
    }

    public function getPiede() {
        return self::$piede;
    }

    public function getAzione() {
        return self::$azione;
    }

    public function getTestoAzione() {
        return self::$testoAzione;
    }

    public function getTitoloPagina() {
        return self::$titoloPagina;
    }

    public function getConfermaTip() {
        return self::$confermaTip;
    }

    // Composizione del menu in testata pagine --------------------------------------------

    public function makeMenu($utility): string {

        $array = $utility->getConfig();

        $ambiente = isset($_SESSION["ambiente"]) ? $_SESSION["ambiente"] : $this->getEnvironment($array, $_SESSION);

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
        $menu .= $home;

        // O p er a z i o n i ------------------------------------------------------------

        $operazioni = "";

        if ($array["operazioni"] == "Y") {
            $operazioni .= "<li class='dropdown'>";
            $operazioni .= "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>" . $array['operazioni_menu_title'];
            $operazioni .= "<span class='caret'></span></a>";
            $operazioni .= "<ul class='dropdown-menu'>";

            if ($array["operazioni_item_1"] == "Y")
                $operazioni .= "<li><a href='../primanota/ricercaRegistrazioneFacade.class.php?modo=start'>" . $array["operazioni_item_1_name"] . "</a></li>";

            $operazioni .= "<li role='separator' class='divider'></li>";
            
            if ($array["operazioni_item_2"] == "Y")
                $operazioni .= "<li><a href='../strumenti/cambiaContoStep1Facade.class.php?modo=start'>" . $array["operazioni_item_2_name"] . "</a></li>";
            
            $operazioni .= "</ul></li>";
        }
        $menu .= $operazioni;

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
        $menu .= $anagrafiche;

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
        $menu .= $configurazioni;

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
        $menu .= $scadenze;

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

            $riepiloghi .= "<li role='separator' class='divider'></li>";

            if ($array["riepiloghi_item_5"] == "Y")
                $riepiloghi .= "<li><a href='../saldi/ricercaSaldiFacade.class.php?modo=start'>" . $array["riepiloghi_item_5_name"] . "</a></li>";
            if ($array["riepiloghi_item_6"] == "Y")
                $riepiloghi .= "<li><a href='../saldi/creaSaldoFacade.class.php?modo=start'>" . $array["riepiloghi_item_6_name"] . "</a></li>";

            $riepiloghi .= "</ul></li>";
        }
        $menu .= $riepiloghi;

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
        $menu .= $fatture;

        return $menu;
    }

    public function isAnnoBisestile($anno) {

        $annoBisestile = false;

        if (($anno % 4 == 0 && $anno % 100 != 0) || $anno % 400 == 0) {
            $annoBisestile = true;
        }
        return $annoBisestile;
    }

    public function sommaGiorniData($data, $carattereSeparatore, $giorniDaSommare) {

        list($giorno, $mese, $anno) = explode($carattereSeparatore, $data);
        return date("d-m-Y", mktime(0, 0, 0, $mese, $giorno + $giorniDaSommare, $anno));
    }

    public function sommaGiorniDataYMD($data, $carattereSeparatore, $giorniDaSommare) {

        list($anno, $mese, $giorno) = explode($carattereSeparatore, $data);
        return date("Y-m-d", mktime(0, 0, 0, $mese, $giorno + $giorniDaSommare, $anno));
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

    public function caricaElencoFornitori($fornitore) {
        $elencoFornitori = "<option value=' '>&nbsp;</option>";
        foreach ($fornitore->getFornitori() as $unFornitore) {
            $elencoFornitori .= "<option value='" . $unFornitore[Fornitore::ID_FORNITORE] . "'>" . $unFornitore[Fornitore::DES_FORNITORE] . "</option>";
        }
        return $elencoFornitori;
    }

    public function caricaElencoClienti($cliente) {
        $elencoClienti = "<option value=' '>&nbsp;</option>";
        foreach ($cliente->getClienti() as $unCliente) {
            $elencoClienti .= "<option value='" . $unCliente[Cliente::ID_CLIENTE] . "'>" . $unCliente[Cliente::DES_CLIENTE] . "</option>";
        }
        return $elencoClienti;
    }

    public function caricaElencoConti($conto) {
        $elencoConti = "<option value=' '>&nbsp;</option>";
        foreach ($conto->getConti() as $unConto) {            
            $value = trim($unConto[Sottoconto::COD_CONTO]) . "." . trim($unConto[Sottoconto::COD_SOTTOCONTO]);
            $descr = $unConto[Sottoconto::COD_CONTO] . "." . $unConto[Sottoconto::COD_SOTTOCONTO] . " - " . $unConto[Sottoconto::DES_SOTTOCONTO]; 
            $selected = (trim($conto->getCodContoSel()) === trim($value)) ? "selected" : "";
            $elencoConti .= "<option value='" . $value . "' " . $selected . " >" . $descr . "</option>";
        }
        return $elencoConti;
    }

    /**
     * Questo metodo determina l'ambiente sulla bae degli utenti preenti loggati
     * @param array
     * @param _SESSION
     */
    public function getEnvironment($array) {

        $users = shell_exec("who | cut -d' ' -f1 | sort | uniq");
        $_SESSION["users"] = $users;

        if (strpos($users, $array['usernameProdLogin']) === false) {
            $_SESSION["ambiente"] = "TEST";
        } else {
            $_SESSION["ambiente"] = "PROD";
        }
    }

// *******************************************
// *******************************************
// *******************************************

    /**
     * Questo metodo effettua un controllo sullo scadenziario dei fornitori.
     * Se ci sono scadenze superate restituisce un testo di notifica
     *
     * @param unknown $utility
     * @param unknown $db
     * @return string
     */
    public function controllaScadenzeFornitoriSuperate($utility, $db): string {

        $array = $utility->getConfig();
        $replace = array();
        $sqlTemplate = $this->root . $array['query'] . self::$queryControllaScadenzeFornitoreSuperate;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        $scadenze = "";

        foreach (pg_fetch_all($result) as $row) {
            $scadenze .= "&ndash; Pagamento scaduto il " . $row['dat_scadenza'] . " : " . $row['nota_scadenza'] . "<br/>";
        }
        return $scadenze;
    }

    /**
     * Questo metodo effettua un controllo sullo scadenziario dei clienti.
     * Se ci sono scadenze superate restituisce un testo di notifica
     *
     * @param unknown $utility
     * @param unknown $db
     * @return string
     */
    public function controllaScadenzeClientiSuperate($utility, $db): string {

        $array = $utility->getConfig();
        $replace = array();
        $sqlTemplate = $this->root . $array['query'] . self::$queryControllaScadenzeClienteSuperate;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        $scadenze = "";

        foreach (pg_fetch_all($result) as $row) {
            $scadenze .= "&ndash; Incasso scaduto il " . $row['dat_registrazione'] . " : " . $row['nota'] . "<br/>";
        }
        return $scadenze;
    }

    public function controllaRegistrazioniInErrore($utility, $db): string {

        $array = $utility->getConfig();
        $replace = array();
        $sqlTemplate = $this->root . $array['query'] . self::$queryControllaRegistrazioniInErrore;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);

        $scadenze = "";

        foreach (pg_fetch_all($result) as $row) {
            $scadenze .= "&ndash; Operazione errata del " . $row['dat_registrazione'] . " : " . $row['cod_negozio'] . " - " . $row['des_registrazione'] . "<br/>";
        }
        return $scadenze;
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

?>
