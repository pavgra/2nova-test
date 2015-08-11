<?php

function nvl($arr, $key, $default = null) {
	return isset($arr[$key]) ? $arr[$key] : $default;
}

function base_path($path = "") {
	return __DIR__ . "/../../" . $path; //TODO
}

function config($dotPath) {
	$path = explode(".", $dotPath);
	$cnt = count($path);

	$config = include base_path("config/{$path[0]}.php");

	if (isset($config) && is_array($config)) {
		for ($i=1; $i < $cnt; $i++) {		
			$config = $config[$path[$i]];
		}
	}

	return $config;
}

function str_random($length = 32) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}