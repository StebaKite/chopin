<?php

require_once 'chopin.abstract.class.php';

class RiportoSaldoPeriodico extends ChopinAbstract {

	private static $messaggio;
	private static $queryRicercaConto = "/saldi/ricercaConto.sql";
	private static $querySaldoCondo = "/saldi/saldoConto.sql";

	private static $_instance = null;

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();

		self::$testata = self::$root . $array['testataPagina'];
		self::$piede = self::$root . $array['piedePagina'];
		self::$messaggioErrore = self::$root . $array['messaggioErrore'];
		self::$messaggioInfo = self::$root . $array['messaggioInfo'];
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )

			self::$_instance = new Menubanner();

		return self::$_instance;
	}

	// ------------------------------------------------

	public function start() {








?>