<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fattura.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';

class CreaFatturaEntePubblicoTemplate extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_FATTURA_ENTE_PUBBLICO_TEMPLATE) === NULL) {
            parent::setIndexSession(self::CREA_FATTURA_ENTE_PUBBLICO_TEMPLATE, serialize(new CreaFatturaEntePubblicoTemplate()));
        }
        return unserialize(parent::getIndexSession(self::CREA_FATTURA_ENTE_PUBBLICO_TEMPLATE));
    }

    public function inizializzaPagina() {

    }

    public function controlliLogici() {

    }

    public function displayPagina() {

        $db = Database::getInstance();
        $utility = Utility::getInstance();
        $array = $utility->getConfig();

        $fattura = Fattura::getInstance();
        $dettaglioFattura = DettaglioFattura::getInstance();
        $cliente = Cliente::getInstance();

        $disableNuovoDettButton = '';
        if ($dettaglioFattura->getQtaDettagliFattura() > 0) {
            $disableNuovoDettButton = 'disabled';
        }
                
        $cliente->load($db);

        $form = $this->root . $array['template'] . self::PAGINA_CREA_FATTURA_ENTE_PUBBLICO;

        $replace = array(
            '%titoloPagina%' => parent::getIndexSession(self::TITOLO_PAGINA),
            '%azione%' => parent::getIndexSession(self::AZIONE),
            '%confermaTip%' => parent::getIndexSession(self::TIP_CONFERMA),
            '%titolo%' => parent::getIndexSession($fattura->getDesTitolo()),
            '%numfat%' => $fattura->getNumFattura(),
            '%datafat%' => $fattura->getDatFattura(),
            '%tipoadd%' => $fattura->getTipAddebito(),
            '%ragsocbanca%' => str_replace("'", "&apos;", $fattura->getDesRagsocBanca()),
            '%ibanbanca%' => $fattura->getCodIbanBanca(),
            '%descli%' => $fattura->getDesCliente(),
            '%disableNuovoDettaglioButton%' => $disableNuovoDettButton,
            '%contributo-checked%' => ($fattura->getTipFattura() == self::CONTRIBUTO) ? self::CHECK_THIS_ITEM : "",
            '%vendita-checked%' => ($fattura->getTipFattura() == self::VENDITA) ? self::CHECK_THIS_ITEM : "",
            '%assistito%' => $fattura->getAssistito(),
            '%villa-checked%' => ($fattura->getCodNegozio() == self::VILLA) ? self::CHECK_THIS_ITEM : "",
            '%brembate-checked%' => ($fattura->getCodNegozio() == self::BREMBATE) ? self::CHECK_THIS_ITEM : "",
            '%trezzo-checked%' => ($fattura->getCodNegozio() == self::TREZZO) ? self::CHECK_THIS_ITEM : "",
            '%elenco_clienti%' => $this->caricaElencoClienti($cliente)
        );

        $utility = Utility::getInstance();

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {

    }

    public function start() {

    }

}

?>