<?php
namespace App\Http\Controllers;

use App\Extensions\AppFormFactory;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    /** @var \Twig_Environment */
    protected $twig;
    /** @var FormFactory */
    protected $formFactory;
    /** @var Request */
    protected $request;
    /** @var EntityManager */
    protected $em;

    public function __construct()
    {
        $isDev = config('app.dev');

        // Initialize template engine (Twig)
        $loader = new \Twig_Loader_Filesystem(config('view.path'));
        $this->twig = new \Twig_Environment(
            $loader,
            [
                'debug' => $isDev,
                'auto_reload' => $isDev,
                'cache' => config('view.cache'),
            ]
        );

        // Initialize FormFactory with extensions required for app
        $this->formFactory = (new AppFormFactory($this->twig))->build();

        // Get request data
        $this->request = Request::createFromGlobals();

        // Initialize Doctrine EntityManager
        $config = Setup::createYAMLMetadataConfiguration([base_path("config/doctrine")], $isDev);
        $config->setQueryCacheImpl(new ApcCache());
        $config->setResultCacheImpl(new ApcCache());
        $conn = config('database');
        $this->em = EntityManager::create($conn, $config);
    }
}