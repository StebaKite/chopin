<?php

require_once 'primanota.abstract.class.php';
require_once 'primanota.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'registrazione.class.php';
require_once 'dettaglioRegistrazione.class.php';
require_once 'causale.class.php';
require_once 'ricercaRegistrazione.template.php';
require_once 'scadenzaCliente.class.php';
require_once 'scadenzaFornitore.class.php';

class RicercaRegistrazione extends PrimanotaAbstract implements PrimanotaBusinessInterface {

    private $refererFunctionName;

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RICERCA_REGISTRAZIONE) === null) {
            parent::setIndexSession(self::RICERCA_REGISTRAZIONE, serialize(new RicercaRegistrazione()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_REGISTRAZIONE));
    }

    public function start() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        parent::unsetIndexSessione(self::FUNCTION_REFERER);
        parent::unsetIndexSessione(self::MSG);

        $registrazione->prepara();
        $dettaglioRegistrazione->prepara();
        $scadenzaCliente->prepara();
        $scadenzaFornitore->prepara();
        
        $registrazione->preparaFiltri();
        $this->setRefererFunctionName("");

        $ricercaRegistrazioneTemplate = RicercaRegistrazioneTemplate::getInstance();
        $this->preparaPagina($ricercaRegistrazioneTemplate);

        $replace = (parent::getIndexSession("ambiente") !== NULL ? array('%amb%' => parent::getIndexSession("ambiente"), '%users%' => parent::getIndexSession("users"), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $ricercaRegistrazioneTemplate->displayPagina();
        include($this->piede);
    }

    public function go() {
        $registrazione = Registrazione::getInstance();
        $dettaglioRegistrazione = DettaglioRegistrazione::getInstance();
        $scadenzaCliente = ScadenzaCliente::getInstance();
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        parent::unsetIndexSessione(self::FUNCTION_REFERER);

        $registrazione->preparaFiltri();
        $dettaglioRegistrazione->prepara();
        $scadenzaCliente->prepara();
        $scadenzaFornitore->prepara();

        $ricercaRegistrazioneTemplate = RicercaRegistrazioneTemplate::getInstance();
        $this->preparaPagina($ricercaRegistrazioneTemplate);

        $replace = (parent::getIndexSession("ambiente") !== NULL ? array('%amb%' => parent::getIndexSession("ambiente"), '%users%' => parent::getIndexSession("users"), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array, $_SESSION), '%menu%' => $this->makeMenu($utility)));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        if ($ricercaRegistrazioneTemplate->controlliLogici()) {

            if ($registrazione->load($db)) {

                parent::setIndexSession(self::REGISTRAZIONE, serialize($registrazione));

                /**
                 * Gestione del messaggio proveniente da altre funzioni
                 */
                if (parent::getIndexSession(self::MSG_DA_CANCELLAZIONE) !== NULL) {
                    parent::setIndexSession(self::MESSAGGIO, $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni");
                    parent::unsetIndexSessione(self::MSG_DA_CANCELLAZIONE);
                } elseif (parent::getIndexSession(self::MSG_DA_MODIFICA) !== NULL) {
                    parent::setIndexSession(self::MESSAGGIO, $_SESSION[self::MSG_DA_MODIFICA] . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni");
                    parent::unsetIndexSessione(self::MSG_DA_MODIFICA);
                } elseif (parent::getIndexSession(self::MSG_DA_CREAZIONE) !== NULL) {
                    parent::setIndexSession(self::MESSAGGIO, $_SESSION[self::MSG_DA_CREAZIONE] . "<br>" . "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni");
                    parent::unsetIndexSessione(self::MSG_DA_CREAZIONE);
                } else {
                    parent::setIndexSession(self::MESSAGGIO, "Trovate " . $registrazione->getQtaRegistrazioni() . " registrazioni");
                }

                self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));

                $pos = strpos(parent::getIndexSession(self::MESSAGGIO), "ERRORE");
                if ($pos === false) {
                    if ($registrazione->getQtaRegistrazioni() > 0)
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                    else
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                } else
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);

                $_SESSION[self::MSG] = $utility->tailTemplate($template);
            }
            else {

                parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);
                self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
                $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
            }
        } else {

            self::$replace = array('%messaggio%' => parent::getIndexSession("messaggio"));
            $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
            $_SESSION[self::MSG] = $utility->tailTemplate($template);
        }
        $ricercaRegistrazioneTemplate->displayPagina();

        include($this->piede);
    }

    public function preparaPagina($ricercaRegistrazioneTemplate) {

        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_REGISTRAZIONE);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.confermaRicercaRegistrazione%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaRegistrazione%");
    }

    public function getRefererFunctionName() {
        return $this->refererFunctionName;
    }

    public function setRefererFunctionName($refererFunctionName) {
        $this->refererFunctionName = $refererFunctionName;
    }

}