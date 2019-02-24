<?php

require_once 'nexus6.abstract.class.php';
require_once 'strumenti.presentation.interface.php';

abstract class StrumentiAbstract extends Nexus6Abstract implements StrumentiPresentationInterface {

    public static $queryTrovaCorrispettivo = "/primanota/trovaCorrispettivo.sql";
    
    public function intestazione($dati): string {
        
        return "<div class='row'>" .
                "    <div class='col-sm-4'>" .
                "        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
                "    </div>" .
                "    <div class='col-sm-8'>" . $_SESSION[self::MSG] . "</div>" .
                "</div>" .
                "<br/>" .
                "<table class='table table-bordered table-hover'>" .
                "   <thead>" .
                "       <tr>" .
                "           <th>" . $dati["labeldatReg"] . "</th>" .
                "           <th>" . $dati["labeldesReg"] . "</th>" .
                "           <th>" . $dati["labelstaReg"] . "</th>" .
                "           <th>" . $dati["labelimpReg"] . "</th>" .
                "           <th>" . $dati["labelindDareAvere"] . "</th>" .
                "           <th>" . $dati["labelconto"] . "</th>" .
                "           <th>" . $dati["labelsottoconto"] . "</th>" .
                "       </tr>" .
                "   </thead>" .
                "   <tbody id='myTable'>";
    }
    
    public function intestazioneCorrispettiviNegozio($dati): string {
        
        return  "<table class='table table-bordered table-hover'>" .
                "   <thead>" .
                "       <tr>" .
                "           <th>" . $dati["labeldata"] . "</th>" .
                "           <th>" . $dati["labeltotale"] . "</th>" .
                "           <th>" . $dati["labelrep1"] . "</th>" .
                "           <th>" . $dati["labelrep2"] . "</th>" .
                "       </tr>" .
                "   </thead>" .
                "   <tbody id='myTable'>";
    }

    public function isNew($db, $utility, $datareg, $codneg, $conto, $importo) {

        $array = $utility->getConfig();
        $replace = array(
            '%dat_registrazione%' => trim($datareg),
            '%cod_negozio%' => trim($codneg),
            '%cod_conto%' => substr(trim($conto), 0, 3),
            '%imp_registrazione%' => str_replace(",", ".", trim($importo))
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryTrovaCorrispettivo;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);

        if (pg_num_rows($db->execSql($sql)) > 0) {
            return false;
        }
        return true;
    }
}
