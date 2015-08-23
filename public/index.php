<?php

$loader = require '../vendor/autoload.php';
$loader->register();

use App\Extensions\ControllerResolver;
use App\Http\Kernel;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

$resolver = new ControllerResolver();
$kernel = new Kernel($resolver);
$response = $kernel->handle($request);
$response->send();