<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'ricercaScadenzeFornitore.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaFornitore.class.php';

class RicercaScadenzeFornitore extends ScadenzeAbstract implements ScadenzeBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();

        $this->testata = $this->root . $this->array[self::TESTATA];
        $this->piede = $this->root . $this->array[self::PIEDE];
        $this->messaggioErrore = $this->root . $this->array[self::ERRORE];
        $this->messaggioInfo = $this->root . $this->array[self::INFO];
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::RICERCA_SCADENZE_FORNITORE) === NULL) {
            parent::setIndexSession(self::RICERCA_SCADENZE_FORNITORE, serialize(new RicercaScadenzeFornitore()));
        }
        return unserialize(parent::getIndexSession(self::RICERCA_SCADENZE_FORNITORE));
    }

    public function start() {
        $scadenzaFornitore = ScadenzaFornitore::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        parent::setIndexSession(self::FUNCTION_REFERER, self::RICERCA_SCADENZE_FORNITORE);
        parent::unsetIndexSessione(self::MSG);

        $scadenzaFornitore->prepara();

        $ricercaScadenzeTemplate = RicercaScadenzeTemplate::getInstance();
        $this->preparaPagina($ricercaScadenzeTemplate);

        // compone la pagina
        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
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

        parent::setIndexSession(self::FUNCTION_REFERER, self::RICERCA_SCADENZE_FORNITORE);

        $ricercaScadenzeTemplate = RicercaScadenzeTemplate::getInstance();

        $replace = parent::getIndexSession(self::AMBIENTE) !== NULL ? array('%amb%' => parent::getIndexSession(self::AMBIENTE), '%users%' => parent::getIndexSession(self::USERS), '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment($array), '%menu%' => $this->makeMenu($utility));
        $template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
        echo $utility->tailTemplate($template);

        $this->preparaPagina($ricercaScadenzeTemplate);

        if ($ricercaScadenzeTemplate->controlliLogici()) {

            if ($scadenzaFornitore->load($db)) {

                parent::setIndexSession(self::SCADENZA_FORNITORE, serialize($scadenzaFornitore));

                /**
                 * Gestione del messaggio proveniente da altre funzioni
                 */
                if (parent::getIndexSession(self::MSG_DA_CANCELLAZIONE) !== NULL) {
                    parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_CANCELLAZIONE) . "<br>" . "Trovate " . $scadenzaFornitore->getQtaScadenzeDaPagare() . " scadenze");
                    parent::unsetIndexSessione(self::MSG_DA_CANCELLAZIONE);
                } elseif (parent::getIndexSession (self::MSG_DA_MODIFICA) !== NULL) {
                    parent::setIndexSession(self::MESSAGGIO, parent::getIndexSession(self::MSG_DA_MODIFICA) . "<br>" . "Trovate " . $scadenzaFornitore->getQtaScadenzeDaPagare() . " scadenze");
                    parent::unsetIndexSessione(self::MSG_DA_MODIFICA);
                } else {
                    parent::setIndexSession(self::MESSAGGIO, "Trovate " . $scadenzaFornitore->getQtaScadenzeDaPagare() . " scadenze");
                }

                self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));

                $pos = strpos(parent::getIndexSession(self::MESSAGGIO), "ERRORE");
                if ($pos === false) {
                    if ($scadenzaFornitore->getQtaScadenzeDaPagare() > 0)
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
                    else
                        $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
                } else
                    $template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);

                parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
            }
            else {

                parent::setIndexSession(self::MESSAGGIO, self::ERRORE_LETTURA);
                self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
                $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
                parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
            }
        } else {

            self::$replace = array('%messaggio%' => parent::getIndexSession(self::MESSAGGIO));
            $template = $utility->tailFile($utility->getTemplate(self::$messaggioErrore), self::$replace);
            parent::setIndexSession(self::MSG, $utility->tailTemplate($template));
        }
        $ricercaScadenzeTemplate->displayPagina();

        include($this->piede);
    }

    public function preparaPagina() {
        parent::setIndexSession(self::AZIONE, self::AZIONE_RICERCA_SCADENZE_FORNITORE);
        parent::setIndexSession(self::TIP_CONFERMA, "%ml.cercaTip%");
        parent::setIndexSession(self::TITOLO_PAGINA, "%ml.ricercaScadenze%");
    }

}

?>