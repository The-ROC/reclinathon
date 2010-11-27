<?php
class Object {
	function Object() {
		$args = func_get_args();
		if (method_exists($this, '__destruct')) {
			register_shutdown_function(array(&$this, '__destruct'));
		}
		call_user_func_array(array(&$this, '__construct'), $args);
	}

	function __construct() {
	}
}
?>