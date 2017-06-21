<?php

require_once 'primanota.abstract.class.php';

class ConfigurazioneCausale extends primanotaAbstract {

	private static $_instance = null;
	private static $arrayConti = array();

	function __construct() {

		self::$root = $_SERVER['DOCUMENT_ROOT'];

		require_once 'utility.class.php';

		$utility = Utility::getInstance();
		$array = $utility->getConfig();
	}

	private function  __clone() { }

	/**
	 * Singleton Pattern
	 */

	public static function getInstance() {

		if( !is_object(self::$_instance) )
			self::$_instance = new ConfigurazioneCausale();

		return self::$_instance;
	}

	public function setArrayConti($arrayConti) {
		array_push(self::$arrayConti, $arrayConti);
	}
}

