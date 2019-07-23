<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'cliente.class.php';
require_once 'registrazione.class.php';
require_once 'scadenzaCliente.class.php';

class CalcolaDataScadenzaCliente extends PrimanotaAbstract implements PrimanotaBusinessInterface {

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
        '12' => '31',
    );

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::CALCOLA_DATA_SCADENZA_CLIENTE]))
            $_SESSION[self::CALCOLA_DATA_SCADENZA_CLIENTE] = serialize(new CalcolaDataScadenzaCliente());
        return unserialize($_SESSION[self::CALCOLA_DATA_SCADENZA_CLIENTE]);
    }

    public function start() {
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $cliente = Cliente::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();

        $array = $utility->getConfig();

        if ($registrazione->getIdCliente() == "") {

            /**
             * Devo eliminare da DB le scadenze del cliente indicato nella registrazione
             * Queste scadenze si trovano nell'oggetto ScadenzeCliente, elimino solo quelle da incassare.
             */
            $scadenzaCliente->rimuoviScadenzeRegistrazione($db);
            echo "<div class='alert alert-warning' role='alert'>Scadenze esistenti eliminate, nessuna scadenza presente.</div>";
        } else {

            $cliente->setIdCliente($registrazione->getIdCliente());
            $cliente->leggi($db);

            /**
             * Verifico se ci sono gia' scadenze significa che è stato cambiato il cliente.
             * Verifico se l'id del nuovo cliente è diverso da quello delle scadenze,
             *   - se si, aggiorno l'id del cliente e il tipo addebito di tutte le scadenze della registrazione
             *   - se no, calcolo la nuova scadenza del cliente
             */
            if ($scadenzaCliente->getQtaScadenzeDaIncassare() == 0) {
                /**
                 * I giorni di scadenza fattura per i clienti sono configurati in configurazione per tutti
                 * Se questo valore è = 0 la scadenza non viene calcolata
                 */
                if ($array['giorniScadenzaFattureCliente'] > 0) {
                    $scadenzaCliente->setQtaScadenzeDaIncassare(0);
                    $scadenzaCliente->setScadenzeDaIncassare("");
                    $dataScadenza = $this->calcolaDataScadenza($registrazione->getDatRegistrazione(), $array['giorniScadenzaFattureCliente']);

                    /**
                     * Se i giorni scadenza fattura del cliente sono = 0 non viene calcolata da data scadenza
                     */
                    if ($dataScadenza != "") {

                        $scadenzaCliente->setDatRegistrazione($dataScadenza);
                        $scadenzaCliente->setIdCliente($cliente->getIdCliente());
                        $scadenzaCliente->setImpRegistrazione(0);
                        $scadenzaCliente->setNumFattura("0");
                        $scadenzaCliente->aggiungi();

                        echo $this->makeTabellaScadenzeCliente($scadenzaCliente, $dettaglioRegistrazione);
                    } else {
                        echo "<div class='alert alert-info' role='alert'>Il cliente ha il numero di giorni scadenza fatture impostato a zero.</div>";
                    }
                } else {
                    echo "<div class='alert alert-info' role='alert'>Il cliente ha il numero di giorni scadenza fatture impostato a zero.</div>";
                }
            } else {

                foreach ($scadenzaCliente->getScadenzeDaIncassare() as $unaScadenza) {

                    $scadenzaCliente->setIdClienteOrig($unaScadenza[ScadenzaCliente::ID_CLIENTE]);
                    $scadenzaCliente->setDatRegistrazione($unaScadenza[ScadenzaCliente::DAT_REGISTRAZIONE]);
                    $scadenzaCliente->setNumFatturaOrig($unaScadenza[ScadenzaCliente::NUM_FATTURA]);

                    $scadenzaCliente->setImpRegistrazione($unaScadenza[ScadenzaCliente::IMP_REGISTRAZIONE]);
                    $scadenzaCliente->setNota($unaScadenza[ScadenzaCliente::NOTA]);
                    $scadenzaCliente->setTipAddebito($cliente->getTipAddebito());
                    $scadenzaCliente->setCodNegozio($unaScadenza[ScadenzaCliente::COD_NEGOZIO]);
                    $scadenzaCliente->setIdCliente($cliente->getIdCliente());
                    $scadenzaCliente->setNumFattura($unaScadenza[ScadenzaCliente::NUM_FATTURA]);
                    $scadenzaCliente->setStaScadenza($unaScadenza[ScadenzaCliente::STA_SCADENZA]);

                    /**
                     * L'aggiornamento delle scadenze non riguarda le date di scadenza impostate con il cliente
                     * precedente. Viene aggiornato solo l'id del cliente e il tipo di addebito.
                     */
                    $scadenzaCliente->aggiorna($db);
                }
            }
        }
    }

    public function go() {
        
    }

}

?>