<?php

require_once 'strumenti.abstract.class.php';
require_once 'strumenti.presentation.interface.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'conto.class.php';
require_once 'sottoconto.class.php';

class CambiaContoStep1Template extends StrumentiAbstract implements StrumentiPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CAMBIA_CONTO_STEP1_TEMPLATE) === NULL) {
            parent::setIndexSession(self::CAMBIA_CONTO_STEP1_TEMPLATE, serialize(new CambiaContoStep1Template()));
        }
        return unserialize(parent::getIndexSession(self::CAMBIA_CONTO_STEP1_TEMPLATE));
    }

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        $esito = TRUE;
        $msg = "<br>";

        if ($msg != "<br>") {
            parent::setIndexSession(self::MESSAGGIO, $msg);
        } else {
            parent::unsetIndexSessione(self::MESSAGGIO);
        }
        return $esito;
    }

    public function displayPagina() {

        $registrazione = Registrazione::getInstance();
        $conto = Conto::getInstance();
        $utility = Utility::getInstance();
        $db = Database::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_CAMBIO_CONTO_STEP1;
        $risultato_ricerca = "";
        $dati = "";

        // Creo l'elenco delle registrazioni trovate
        
         if ($registrazione->getQtaRegistrazioni() > 0) {

            $dati = array(
                "labeldatReg" => "%ml.datregistrazione%",
                "labeldesReg" => "%ml.desReg%",
                "labelstaReg" => "%ml.staReg%",
                "labelimpReg" => "%ml.impReg%",
                "labelindDareAvere" => "%ml.indDareAvere%",
                "labelconto" => "%ml.conto%",
                "labelsottoconto" => "%ml.sottoconto%"
            );
            
            $risultato_ricerca = $this->intestazione($dati);
            
            foreach ($registrazione->getRegistrazioni() as $row) {

                $risultato_ricerca .= "" . 
                    "<tr>" .
                    "   <td>" . trim($row[Registrazione::DAT_REGISTRAZIONE]) . "</td>" .
                    "	<td>" . trim($row[Registrazione::DES_REGISTRAZIONE]) . "</td>" .
                    "	<td>" . trim($row[Registrazione::STA_REGISTRAZIONE]) . "</td>" .
                    "	<td>" . trim($row[DettaglioRegistrazione::IMP_REGISTRAZIONE]) . "</td>" .
                    "	<td>" . trim($row[DettaglioRegistrazione::IND_DAREAVERE]) . "</td>" .
                    "	<td>" . trim($row[DettaglioRegistrazione::COD_CONTO]) . "</td>" .
                    "	<td>" . trim($row[DettaglioRegistrazione::COD_SOTTOCONTO]) . "</td>" .
                    "</tr>";
            }
            $risultato_ricerca .= "</tbody></table>";

            $bottone_avanti = "<button class='button' title='%ml.avantiTip%' >%ml.avanti%</button>";
        } else {
        }

        $conto->leggiTuttiConti($db);
        
        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%confermaTip%' => parent::getIndexSession(self::TIP_CONFERMA),
            '%datareg_da%' => $registrazione->getDatRegistrazioneDa(),
            '%datareg_a%' => $registrazione->getDatRegistrazioneA(),
            '%villa-selected%' => ($registrazione->getCodNegozioSel() == self::ERBA) ? self::SELECT_THIS_ITEM : self::EMPTYSTRING,
            '%elenco_conti%' => $conto->preparaElencoConti(),
            '%risultato_ricerca%' => $risultato_ricerca
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {
        
    }

    public function start() {
        
    }

}