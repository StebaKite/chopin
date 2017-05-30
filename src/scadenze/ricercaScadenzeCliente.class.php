<?php

require_once 'scadenze.abstract.class.php';
require_once 'scadenze.business.interface.php';
require_once 'ricercaScadenzeCliente.template.php';
require_once 'utility.class.php';
require_once 'database.class.php';
require_once 'scadenzaCliente.class.php';

class RicercaScadenzeCliente extends ScadenzeAbstract implements ScadenzeBusinessInterface
{
	function __construct()
	{
		$this->root = $_SERVER['DOCUMENT_ROOT'];
		$this->utility = Utility::getInstance();
		$this->array = $this->utility->getConfig();

		$this->testata = $this->root . $this->array[self::TESTATA];
		$this->piede = $this->root . $this->array[self::PIEDE];
		$this->messaggioErrore = $this->root . $this->array[self::ERRORE];
		$this->messaggioInfo = $this->root . $this->array[self::INFO];
	}

	public function getInstance()
	{
		if (!isset($_SESSION[self::RICERCA_SCADENZE_CLIENTE])) $_SESSION[self::RICERCA_SCADENZE_CLIENTE] = serialize(new RicercaScadenzeCliente());
		return unserialize($_SESSION[self::RICERCA_SCADENZE_CLIENTE]);
	}

	public function start()
	{
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$scadenzaCliente->prepara();
		// 		unset($_SESSION['referer_function_name']);

		$ricercaScadenzeClienteTemplate = RicercaScadenzeClienteTemplate::getInstance();
		$this->preparaPagina($ricercaScadenzeClienteTemplate);

		// compone la pagina
		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);

		$ricercaScadenzeClienteTemplate->displayPagina();
		include($this->piede);
	}

	public function go()
	{
		$scadenzaCliente = ScadenzaCliente::getInstance();
		$db = Database::getInstance();
		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		$ricercaScadenzeClienteTemplate = RicercaScadenzeClienteTemplate::getInstance();

		$replace = (isset($_SESSION["ambiente"]) ? array('%amb%' => $_SESSION["ambiente"], '%menu%' => $this->makeMenu($utility)) : array('%amb%' => $this->getEnvironment ( $array, $_SESSION ), '%menu%' => $this->makeMenu($utility)));
		$template = $utility->tailFile($utility->getTemplate($this->testata), $replace);
		echo $utility->tailTemplate($template);

		$this->preparaPagina($ricercaScadenzeClienteTemplate);

		if ($ricercaScadenzeClienteTemplate->controlliLogici()) {

			if ($scadenzaCliente->load($db)) {

				$_SESSION[self::SCADENZA_CLIENTE] = serialize($scadenzaCliente);
				$ricercaScadenzeClienteTemplate->displayPagina();

				/**
				 * Gestione del messaggio proveniente dalla cancellazione
	 			 */
				if (isset($_SESSION[self::MSG_DA_CANCELLAZIONE])) {
					$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_CANCELLAZIONE] . "<br>" . "Trovate " . $scadenzaCliente->getQtaScadenze() . " scadenze";
					unset($_SESSION[self::MSG_DA_CANCELLAZIONE]);
				}
				elseif (isset($_SESSION[self::MSG_DA_MODIFICA])) {
					$_SESSION[self::MESSAGGIO] = $_SESSION[self::MSG_DA_MODIFICA] . "<br>" . "Trovate " . $scadenzaCliente->getQtaScadenze() . " scadenze";
					unset($_SESSION[self::MSG_DA_MODIFICA]);
				}
				else {
					$_SESSION[self::MESSAGGIO] = "Trovate " . $scadenzaCliente->getQtaScadenze() . " scadenze";
				}

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);

				if ($scadenzaCliente->getQtaScadenze() > 0) {
					$template = $utility->tailFile($utility->getTemplate($this->messaggioInfo), self::$replace);
				}
				else {
					$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
				}

				echo $utility->tailTemplate($template);
			}
			else {

				$ricercaScadenzeClienteTemplate->displayPagina();
				$_SESSION[self::MESSAGGIO] = self::ERRORE_LETTURA;

				self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
				$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
				echo $utility->tailTemplate($template);
			}
		}
		else {

			$ricercaScadenzeClienteTemplate->displayPagina();

			self::$replace = array('%messaggio%' => $_SESSION[self::MESSAGGIO]);
			$template = $utility->tailFile($utility->getTemplate($this->messaggioErrore), self::$replace);
			echo $utility->tailTemplate($template);
		}
		include($this->piede);
	}

	public function preparaPagina()
	{
		$_SESSION[self::AZIONE] = self::AZIONE_RICERCA_SCADENZE_CLIENTE;
		$_SESSION[self::TIP_CONFERMA] = "%ml.cercaTip%";
		$_SESSION[self::TITOLO_PAGINA] = "%ml.ricercaScadenzeCliente%";
	}
}

?>