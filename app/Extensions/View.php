<?php namespace App\Extensions;

use Symfony\Component\HttpFoundation\Response;

class View {

	public static function make($path, $params = [])
	{
		$pathParts = explode(".", $path);
		$template = array_pop($pathParts);
		$folder = implode("/", $pathParts);

		$loader = new \Twig_Loader_Filesystem(config("view.path") . $folder);
		$twig = new \Twig_Environment($loader, array(
			"auto_reload" => true,
			"cache" => config("view.cache"),
		));

		return $twig->render($template . ".html.twig", $params);
	}

}