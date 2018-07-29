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
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->utility = Utility::getInstance();
        $this->array = $this->utility->getConfig();
    }

    public function getInstance() {
        if (!isset($_SESSION[self::CREA_FATTURA_ENTE_PUBBLICO_TEMPLATE]))
            $_SESSION[self::CREA_FATTURA_ENTE_PUBBLICO_TEMPLATE] = serialize(new CreaFatturaEntePubblicoTemplate());
        return unserialize($_SESSION[self::CREA_FATTURA_ENTE_PUBBLICO_TEMPLATE]);
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

        $form = $this->root . $array['template'] . self::PAGINA_CREA_FATTURA_ENTE_PUBBLICO;

        $replace = array(
            '%titoloPagina%' => $_SESSION[self::TITOLO_PAGINA],
            '%azione%' => $_SESSION[self::AZIONE],
            '%confermaTip%' => $_SESSION[self::TIP_CONFERMA],
            '%titolo%' => $_SESSION[$fattura->getDesTitolo()],
            '%numfat%' => $fattura->getNumFattura(),
            '%datafat%' => $fattura->getDatFattura(),
            '%tipoadd%' => $fattura->getTipAddebito(),
            '%ragsocbanca%' => str_replace("'", "&apos;", $fattura->getDesRagsocBanca()),
            '%ibanbanca%' => $fattura->getCodIbanBanca(),
            '%descli%' => $fattura->getDesCliente(),
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