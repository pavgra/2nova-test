<?php namespace App\Extensions;

class ControllerResolver {

	static private $controllersPath = "App\Http\Controllers\\";

	public function getController($request) {
		$_controller = $request->attributes['_controller'];

		if (is_callable($_controller)) {
			$controller = $_controller; //Closure
		} else {
			$_controller = explode('@', $_controller); // Laravel-like controller-method line

			$class = static::$controllersPath . $_controller[0];
			$method = isset($_controller[1]) ? $_controller[1] : 'index';

			$controller = [new $class(), $method];
		}

		return $controller;
		
	}

	public function getAttributes($request, $controller) {
		if (is_array($controller)) {
			$reflection = new \ReflectionMethod($controller[0], $controller[1]);
		} else {
			$reflection = new \ReflectionFunction($controller);
		}

		$params = $reflection->getParameters();
		$attributes = [];
		
		foreach ($params as $param) {
			$attributes[] = nvl($request->attributes, $param->getName(), null);
		}

		return $attributes;
	}
}