<?php
namespace App\Http;

use App\Extensions\ControllerResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Kernel
{
    /** @var RouteCollection */
    private $routes;
    /** @var \App\Extensions\ControllerResolver */
    private $resolver;

    /**
     * @param ControllerResolver $resolver
     */
    public function __construct($resolver)
    {
        // Load routes list
        $locator = new FileLocator([__DIR__]);
        $loader = new YamlFileLoader($locator);
        $this->routes = $loader->load('routes.yml');

        // Set ControllerResolver
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming HTTP request.
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);

        try {
            // Find suitable route
            $request->attributes = $matcher->match($request->getPathInfo());
            // Retrieve route's callback
            $controller = $this->resolver->getController($request);
            // Get attributes for callback
            $attributes = $this->resolver->getAttributes($request, $controller);
            // Execute route's callback and get response
            $response = call_user_func_array($controller, $attributes);
            // If callback's result is string, convert it to Response
            if (is_string($response)) {
                $response = new Response($response);
            }
        } catch (ResourceNotFoundException $e) {
            // Return 404, if no suitable route was found
            $response = new Response('Page not found!', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }
}