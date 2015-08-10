<?php

$loader = require '../vendor/autoload.php';
$loader->register();

use Symfony\Component\HttpFoundation\Request;
use App\Http\Kernel;
use App\Extensions\ControllerResolver;

$request = Request::createFromGlobals();

$resolver = new ControllerResolver();
$kernel = new Kernel($resolver);
$response = $kernel->handle($request);
$response->send();