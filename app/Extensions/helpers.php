<?php

function nvl($arr, $key, $default = null) {
	return isset($arr[$key]) ? $arr[$key] : $default;
}

function base_path($path = "") {
	return $_SERVER["DOCUMENT_ROOT"] . "/../" . $path;
}

function config($dotPath) {
	$config = null;
	
	$path = explode(".", $dotPath);
	$cnt = count($path);

	if ($cnt > 0) {
		$config = include base_path("config/{$path[0]}.php");	
	}

	if (isset($config) && is_array($config)) {
		for ($i=1; $i < $cnt; $i++) {		
			$config = $config[$path[$i]];
		}
	}

	return $config;
}

function view($path, $params) {
	return \App\Extensions\View::make($path, $params);
}