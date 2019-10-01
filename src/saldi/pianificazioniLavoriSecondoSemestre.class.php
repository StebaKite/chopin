<?php

require_once 'saldi.abstract.class.php';
require_once 'nexus6.main.interface.php';
require_once 'utility.class.php';

/**
 * Questa classe è rieseguibile.
 * @author stefano
 *
 */
class PianificazioniLavoriSecondoSemestre extends SaldiAbstract implements MainNexus6Interface {

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
        if (!isset($_SESSION[self::PIANIFICAZIONE_LAVORI_SECONDO_SEMESTRE])) {
            $_SESSION[self::PIANIFICAZIONE_LAVORI_SECONDO_SEMESTRE] = serialize(new PianificazioniLavoriSecondoSemestre());
        }
        return unserialize($_SESSION[self::PIANIFICAZIONE_LAVORI_SECONDO_SEMESTRE]);
    }

    public function start($db, $pklavoro) {

        /**
         * Vengono pianificati i lavori per il secondo semestre dell'anno in corso.
         * Questa esecuzione viene eseguita l'ultimo giorno del primo semestre, presumibilmente il 30/6
         */
        $anno = date("Y");
        $fileEsecuzioneLavoro = "riportoSaldoPeriodico";
        $classeEsecuzioneLavoro = "RiportoSaldoPeriodico";
        $statoLavoro = "00";

        $utility = Utility::getInstance();
        $lavoroPianificato = LavoroPianificato::getInstance();

        // Riporto saldi luglio
        $lavoroPianificato->setDatLavoro($anno . '-07-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['07']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi agosto
        $lavoroPianificato->setDatLavoro($anno . '-08-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['08']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi settembre
        $lavoroPianificato->setDatLavoro($anno . '-09-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['09']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi ottobre
        $lavoroPianificato->setDatLavoro($anno . '-10-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['10']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi novembre
        $lavoroPianificato->setDatLavoro($anno . '-11-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['11']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Riporto saldi dicembre
        $lavoroPianificato->setDatLavoro($anno . '-12-01');
        $lavoroPianificato->setDesLavoro('Riporto saldi ' . SELF::$mese['12']);
        $lavoroPianificato->setFilEsecuzioneLavoro($fileEsecuzioneLavoro);
        $lavoroPianificato->setClaEsecuzioneLavoro($classeEsecuzioneLavoro);
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        // Pianificazione primo semestre
        $lavoroPianificato->setDatLavoro($anno . '-12-30');
        $lavoroPianificato->setDesLavoro('Pianificazioni semestre 1');
        $lavoroPianificato->setFilEsecuzioneLavoro('pianificazioniLavoriPrimoSemestre');
        $lavoroPianificato->setClaEsecuzioneLavoro('PianificazioniLavoriPrimoSemestre');
        $lavoroPianificato->setStaLavoro($statoLavoro);
        if (!$lavoroPianificato->inserisci($db)) return FALSE;

        echo "Pianificazione lavori del secondo semestre anno " . $anno;
        $lavoroPianificato->setStaLavoro("10");
        $lavoroPianificato->setPkLavoroPianificato($pklavoro);        
        $lavoroPianificato->cambioStato($db);
        return TRUE;
    }
}