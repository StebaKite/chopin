<?php

require_once 'nexus6.abstract.class.php';
require_once 'strumenti.presentation.interface.php';

abstract class StrumentiAbstract extends Nexus6Abstract implements StrumentiPresentationInterface {

    public function     intestazione($dati): string {
        
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
}

?>
