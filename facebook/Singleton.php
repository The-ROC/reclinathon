<?php

require_once('Object.php');

class Singleton extends Object {
	function __construct() {
		// Perform object initializtaion
	}
	
	function &__getInstanceImp($name) {
		static $instances = array();
		if(!isset($instances[$name])) {
			$instances[$name] = new $name();
		}
		return $instances[$name];
	}
	
	function &getInstance() {
		echo "<br>Singleton::getInstance(): " . __CLASS__;
		trigger_error('Singleton::getInstance() needs to be overridden in a subclass');
	}
}
?>