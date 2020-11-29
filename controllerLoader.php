<?php

//load all controllers

if (!function_exists('load_controller')) {
	function load_controller ($cfunction, $pDB, $smarty, $module_name, $local_templates_dir) {

		if (file_exists("modules/$module_name/controllers")) {
			$class = explode('_', $cfunction)[0];
			$class = ucfirst($class);
			$function = explode('_', $cfunction)[1];
			$classpath = "modules/$module_name/controllers/".$class.".php";
			if (file_exists($classpath)) {
				include_once $classpath;

				$object = new $class($pDB, $smarty, $module_name, $local_templates_dir);

				return call_user_func(array($object, $function));
			} else {
				return "$classpath not found";
			}
		}
	}
}

?>