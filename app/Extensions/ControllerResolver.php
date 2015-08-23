<?php
namespace App\Extensions;

use Symfony\Component\HttpFoundation\Request;

class ControllerResolver
{

    private static $controllersPath = "App\Http\Controllers\\";

    /**
     * Returns callback for route
     * @param Request $request
     * @return string[]|callable
     */
    public function getController($request)
    {
        $_controller = $request->attributes['_controller'];

        if (is_callable($_controller)) {
            $controller = $_controller; //Closure
        } else {
            $_controller = explode('@', $_controller); // Laravel-like controller-method line

            $class = static::$controllersPath . $_controller[0];
            $method = nvl($_controller, 1, 'index');

            $controller = [new $class(), $method];
        }

        return $controller;

    }

    /**
     * Returns attributes for route's callback
     * @param Request $request
     * @param string[]|callable $controller
     * @return array
     */
    public function getAttributes($request, $controller)
    {
        if (is_array($controller)) {
            //if controller's class and function names
            $reflection = new \ReflectionMethod($controller[0], $controller[1]);
        } else {
            //if callable
            $reflection = new \ReflectionFunction($controller);
        }

        // Get function's params
        $params = $reflection->getParameters();
        $attributes = [];

        // Filter required attributes
        foreach ($params as $param) {
            $attributes[] = nvl($request->attributes, $param->getName(), null);
        }

        return $attributes;
    }
}