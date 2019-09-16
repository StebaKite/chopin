<?php

require_once 'nexus6.abstract.class.php';

abstract class AnagraficaAbstract extends Nexus6Abstract {

    public static $messaggio;

    // Getters e Setters ---------------------------------------------------

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    // ------------------------------------------------

    public function getMessaggio() {
        return self::$messaggio;
    }

    // Metodi comuni di utilita della prima nota ---------------------------

    public function cercaCodiceFornitore($db, $utility, $codfornitore) {

        $array = $utility->getConfig();
        $replace = array(
            '%cod_fornitore%' => trim($codfornitore)
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryLeggiFornitore;
        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->getData($sql);
        return $result;
    }

}