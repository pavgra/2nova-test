<?php namespace App\Http;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use App\Extensions\View;

class Kernel
{
	/** @var RouteCollection */
	private $routes;
	/** @var ControllerResolver */
	private $resolver;

	public function __construct($resolver)
	{
		$locator = new FileLocator([__DIR__]);
		$loader = new YamlFileLoader($locator);
		$this->routes = $loader->load("routes.yml");

		$this->resolver  = $resolver;
	} 

	/**
	 * Handle an incoming HTTP request.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle($request)
	{
		$context = new RequestContext();
		$context->fromRequest($request);
		
		$matcher = new UrlMatcher($this->routes, $context);

		try {
			$request->attributes = $matcher->match($request->getPathInfo());
			$controller = $this->resolver->getController($request);
			$attributes =  $this->resolver->getAttributes($request, $controller);
			$response = call_user_func_array($controller, $attributes);
			if (is_string($response))
				$response = new Response($response);
		} catch (ResourceNotFoundException $e) {
			$response = new Response(View::make("errors.404"), Response::HTTP_NOT_FOUND);
		}

		return $response;
	}
}