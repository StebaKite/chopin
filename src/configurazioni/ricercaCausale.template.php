<?php

require_once 'configurazioni.abstract.class.php';
require_once 'configurazioni.presentation.interface.php';
require_once 'utility.class.php';
require_once 'causale.class.php';

class RicercaCausaleTemplate extends ConfigurazioniAbstract implements ConfigurazioniPresentationInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RICERCA_CAUSALI_TEMPLATE) === NULL) {
            parent::setIndexSession(self::RICERCA_CAUSALI_TEMPLATE, serialize(new RicercaCausaleTemplate()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_CAUSALI_TEMPLATE));
    }

    public function inizializzaPagina() {
        
    }

    public function controlliLogici() {
        
    }

    public function displayPagina() {

        // Template --------------------------------------------------------------

        $causale = Causale::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $form = $this->root . $array['template'] . self::PAGINA_RICERCA_CAUSALE;
        $risultato_ricerca = "";

        if ($causale->getQtaCausali() > 0) {

            $risultato_ricerca = "<div class='row'>" .
                    "    <div class='col-sm-4'>" .
                    "        <input class='form-control' id='myInput' type='text' placeholder='Ricerca in tabella...'>" .
                    "    </div>" .
                    "    <div class='col-sm-8'>" . parent::getIndexSession(self::MSG) . "</div>" .
                    "</div>" .
                    "<br/>" .
                    "<table class='table table-bordered table-hover'>" .
                    "	<thead>" .
                    "		<th>%ml.codcausale%</th>" .
                    "		<th>%ml.descausale%</th>" .
                    "		<th>%ml.catcausale%</th>" .
                    "		<th>%ml.qtareg%</th>" .
                    "		<th></th>" .
                    "		<th></th>" .
                    "		<th></th>" .
                    "	</thead>" .
                    "	<tbody id='myTable'>";

            foreach ($causale->getCausali() as $row) {

                if ($row[self::NUM_REG_CAUSALE] == 0) {
                    $class = "class='bg-danger'";
                } else {
                    $class = "class=''";
                }

                if ($row[self::NUM_REG_CAUSALE] == 0) {
                    $bottoneModifica = self::MODIFICA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::MODIFICA_ICON;
                    $bottoneConfigura = self::CONFIGURA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::CONFIGURA_ICON;
                    $bottoneCancella = self::CANCELLA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::CANCELLA_ICON;
                } else {
                    $bottoneModifica = self::MODIFICA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::MODIFICA_ICON;
                    $bottoneConfigura = self::CONFIGURA_CAUSALE_HREF . trim($row[$causale::COD_CAUSALE]) . self::CONFIGURA_ICON;
                    $bottoneCancella = "&nbsp;";
                }

                $risultato_ricerca .= "<tr>" .
                        "	<td>" . trim($row[$causale::COD_CAUSALE]) . "</td>" .
                        "	<td>" . trim($row[$causale::DES_CAUSALE]) . "</td>" .
                        "	<td>" . trim($row[$causale::CAT_CAUSALE]) . "</td>" .
                        "	<td " . $class . ">" . trim($row[self::NUM_REG_CAUSALE]) . "</td>" .
                        "	<td>" . $bottoneModifica . "</td>" .
                        "	<td>" . $bottoneConfigura . "</td>" .
                        "	<td>" . $bottoneCancella . "</td>" .
                        "</tr>";
            }
            $risultato_ricerca .= "</tbody></table>";
        }

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%risultato_ricerca%' => $risultato_ricerca
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

}