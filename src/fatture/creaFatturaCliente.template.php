<?php

require_once 'fattura.abstract.class.php';
require_once 'fatture.business.interface.php';
require_once 'database.class.php';
require_once 'utility.class.php';
require_once 'fattura.class.php';
require_once 'cliente.class.php';
require_once 'fornitore.class.php';

class CreaFatturaClienteTemplate extends FatturaAbstract implements FattureBusinessInterface {

    function __construct() {
        $this->root = parent::getInfoFromServer('DOCUMENT_ROOT');
    }

    public static function getInstance() {
        if (parent::getIndexSession(self::CREA_FATTURA_CLIENTE_TEMPLATE) === NULL) {
            parent::setIndexSession(self::CREA_FATTURA_CLIENTE_TEMPLATE, serialize(new CreaFatturaClienteTemplate()));
        }
        return unserialize(parent::getIndexSession(self::CREA_FATTURA_CLIENTE_TEMPLATE));
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
        $cliente = Cliente::getInstance();

        $cliente->load($db);

        $form = $this->root . $array['template'] . self::PAGINA_CREA_FATTURA_CLIENTE;

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
            '%contributo-checked%' => ($fattura->getTipFattura() == self::CONTRIBUTO) ? self::CHECK_THIS_ITEM : "",
            '%vendita-checked%' => ($fattura->getTipFattura() == self::VENDITA) ? self::CHECK_THIS_ITEM : "",
            '%assistito%' => $fattura->getAssistito(),
            '%villa-checked%' => ($fattura->getCodNegozio() == self::ERBA) ? self::CHECK_THIS_ITEM : "",
            '%elenco_clienti%' => $this->caricaElencoClienti($cliente)
        );

        $template = $utility->tailFile($utility->getTemplate($form), $replace);
        echo $utility->tailTemplate($template);
    }

    public function go() {

    }

    public function start() {

    }

}

?>