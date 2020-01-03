<?php

require_once 'saldi.abstract.class.php';
require_once 'nexus6.main.interface.php';
require_once 'utility.class.php';

/**
 * Questa classe è rieseguibile.
 * @author stefano
 *
 */
class PianificazioniLavoriPrimoSemestre extends SaldiAbstract implements MainNexus6Interface {

    public static $queryCreaLavoroPianificato = "/main/creaLavoroPianificato.sql";
    public static $ggMese = array(
        '01' => '31',
        '02' => '28',
        '03' => '31',
        '04' => '30',
        '05' => '31',
        '06' => '30',
        '07' => '31',
        '08' => '31',
        '09' => '30',
        '10' => '31',
        '11' => '30',
        '12' => '31'
    );
    public static $mese = array(
        '01' => 'gennaio',
        '02' => 'febbraio',
        '03' => 'marzo',
        '04' => 'aprile',
        '05' => 'maggio',
        '06' => 'giugno',
        '07' => 'luglio',
        '08' => 'agosto',
        '09' => 'settembre',
        '10' => 'ottobre',
        '11' => 'novembre',
        '12' => 'dicembre'
    );

    function __construct() {
        self::$root = '/var/www/html';
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::PIANIFICAZIONE_LAVORI_PRIMO_SEMESTRE) === NULL) {
            parent::setIndexSession(self::PIANIFICAZIONE_LAVORI_PRIMO_SEMESTRE, serialize(new PianificazioniLavoriPrimoSemestre()));
        }
        return unserialize(parent::getIndexSession(self::PIANIFICAZIONE_LAVORI_PRIMO_SEMESTRE));
    }

    public function start($db, $pklavoro) {

        /**
         * Vengono inseriti i lavori per il primo semestre dell'anno prossimo.
         * Questo lavoro viene pianificato per il 30/12 di ogni anno. Se viene eseguito in questa data somma 1 all'anno
         * altrimenti lascia l'anno corrente. Deve essere eseguito al più tardi entro la fine del mese di gennaio. 
         */
        $anno = date("Y");
        
        if (date("m") == "12") {
            $anno = date("Y") + 1;
        }
        if ((date("m") == "01") or ( date("m") == "1")) {
            $anno = date("Y");
        }

        $fileEsecuzioneLavoro = "riportoSaldoPeriodico";
        $classeEsecuzioneLavoro = "RiportoSaldoPeriodico";
        $statoLavoro = "00";

        $utility = Utility::getInstance();
        $lavoroPianificato = LavoroPianificato::getInstance();

        // Riporto saldi Gennaio
        $lavoroPianificato->setDatLavoro($anno . '-01-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['01']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi Febbraio
        $lavoroPianificato->setDatLavoro($anno . '-02-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['02']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi Marzo
        $lavoroPianificato->setDatLavoro($anno . '-03-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['03']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi Aprile
        $lavoroPianificato->setDatLavoro($anno . '-04-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['04']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi Maggio
        $lavoroPianificato->setDatLavoro($anno . '-05-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['05']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi Giugno
        $lavoroPianificato->setDatLavoro($anno . '-06-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['06']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Pianificazione secondo semestre
        $lavoroPianificato->setDatLavoro($anno . '-06-30');
        $lavoroPianificato->setDesLavoro('Pianificazioni semestre 2');
        $lavoroPianificato->setFilEsecuzioneLavoro('pianificazioniLavoriSecondoSemestre');
        $lavoroPianificato->setClaEsecuzioneLavoro('PianificazioniLavoriSecondoSemestre');
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        echo "Pianificazione lavori del primo semestre anno " . $anno;
        $lavoroPianificato->setStaLavoro("10");
        $lavoroPianificato->setPkLavoroPianificato($pklavoro);        
        $lavoroPianificato->cambioStato($db);
        return TRUE;
    }
}