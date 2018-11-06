<?php

require_once 'nexus6.abstract.class.php';
require_once 'saldo.class.php';

abstract class SaldiAbstract extends Nexus6Abstract {

    public static $messaggio;

    const SALDO = "Obj_saldo";

    /*
     * Query ---------------------------------------------------------------
     */

    public static $queryCreaLavoroPianificato = "/main/creaLavoroPianificato.sql";

    /*
     * Getters e Setters ---------------------------------------------------
     */

    public function setMessaggio($messaggio) {
        self::$messaggio = $messaggio;
    }

    public function getMessaggio() {
        return self::$messaggio;
    }

    /**
     * Metodi comuni di utilita della prima note
     */
    public function gestioneSaldo($db) {

        $saldo = Saldo::getInstance();

        if ($saldo->leggiSaldo($db)) {

            /**
             * Se il saldo calcolato è significativo, aggiorno il saldo del conto
             * altrimenti elimino il saldo del conto
             */
            if ($saldo->getImpSaldo() != 0) {
                $saldo->aggiornaSaldo($db);
            } else {
                $saldo->cancellaSaldo($db);
            }
        } else {

            /**
             * Se il saldo calcolato è significativo, creo il saldo del conto
             */
            if ($saldo->getImpSaldo() != 0) {
                $saldo->creaSaldo($db);
            }
        }
    }

    public function inserisciLavoroPianificato($db, $utility, $dat_lavoro, $des_lavoro, $fil_esecuzione_lavoro, $cla_esecuzione_lavoro, $sta_lavoro) {

        $array = $utility->getConfig();
        $replace = array(
            '%dat_lavoro%' => $dat_lavoro,
            '%des_lavoro%' => $des_lavoro,
            '%fil_esecuzione_lavoro%' => $fil_esecuzione_lavoro,
            '%cla_esecuzione_lavoro%' => $cla_esecuzione_lavoro,
            '%sta_lavoro%' => $sta_lavoro
        );
        $sqlTemplate = self::$root . $array['query'] . self::$queryCreaLavoroPianificato;

        $sql = $utility->tailFile($utility->getTemplate($sqlTemplate), $replace);
        $result = $db->execSql($sql);
        return $result;
    }
}