<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'ricercaScadenzeFornitore.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';

class RicercaScadenzeFornitore extends ScadenzeAbstract implements ScadenzeBusinessInterface {

    function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (!isset($_SESSION[self::RICERCA_SCADENZE_FORNITORE])) {
            $_SESSION[self::RICERCA_SCADENZE_FORNITORE] = serialize(new RicercaScadenzeFornitore());
        }
        return unserialize($_SESSION[self::RICERCA_SCADENZE_FORNITORE]);
    }

    public function start() {
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $_SESSION[self::FUNCTION_REFERER] = self::RICERCA_SCADENZE_FORNITORE;
        unset($_SESSION[self::MSG]);

        $scadenzaFornitore->prepara();

        $ricercaScadenzeTemplate = RicercaScadenzeTemplate::getInstance();
        $this->preparaPagina($ricercaScadenzeTemplate);

        // compone la pagina
        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $ricercaScadenzeTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $_SESSION[self::FUNCTION_REFERER] = self::RICERCA_SCADENZE_FORNITORE;

        $ricercaScadenzeTemplate = RicercaScadenzeTemplate::getInstance();

        $replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%users%' => $_SESSION["users"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $this->preparaPagina($ricercaScadenzeTemplate);

        if ($ricercaScadenzeTemplate->controlliLogici()) {

            if ($scadenzaFornitore->load($db)) {

                $_SESSION[self::SCADENZA_FORNITORE] = serialize($scadenzaFornitore);

                /**
                 * Gestione del messaggio proveniente da altre funzioni
                 */
                if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
                    $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovate " . $scadenzaFornitore->getQtaScadenzeDaPagare() . " scadenze";
                    unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
                } elseif (isset($_SESSION[self::MSG_DA_MODIFICA])) {
                    $_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_MODIFICA] . "<br>" . "Trovate " . $scadenzaFornitore->getQtaScadenzeDaPagare() . " scadenze";
                    unset($_SESSION[self::MSG_DA_MODIFICA]);
                } else {
                    $_SESSION[self::MESSAGGIO] = "Trovate " . $scadenzaFornitore->getQtaScadenzeDaPagare() . " scadenze";
                }

                self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);

                $pos = strpos($_SESSION[self::MESSAGGIO], "ERRORE");
                if ($pos === false) {
                    if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0)
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                    else
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                } else
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);

                $_SESSION[self::MSG] = $utility->tailTemplate($template);
            }
            else {

                $_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;
                self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
                $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
                $_SESSION[self::MSG] = $utility->tailTemplate($template);
            }
        } else {

            self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        $ricercaScadenzeTemplate->displayPagina();

        include($this->piede);
    }

    public function preparaPagina() {
        $_SESSION[self::AZIONE] = self::AZIONE_RICERCA_SCADENZE_FORNITORE;
        $_SESSION[self::TIP_CONFERMA] = "%ml.cercaTip%";
        $_SESSION[self::TITOLO_PAGINA] = "%ml.ricercaScadenze%";
    }

}

?>