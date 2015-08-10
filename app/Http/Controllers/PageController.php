<?php namespace App\Http\Controllers;

use App\Form\LoginForm;
use App\Form\RegisterForm;
use App\Entity\User;
use App\Extensions\AppFormFactory;
use App\Extensions\Auth;
use Doctrine\Common\Cache\ApcCache;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class PageController
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

        $loader = new \Twig_Loader_Filesystem(config('view.path'));
        $this->twig = new \Twig_Environment($loader, [
            'debug' => $isDev,
            'auto_reload' => $isDev,
            'cache' => config('view.cache'),
        ]);

        $this->formFactory = (new AppFormFactory($this->twig))->build();

        $this->request = Request::createFromGlobals();

        $config = Setup::createYAMLMetadataConfiguration([base_path("config/doctrine")], $isDev);
        $config->setQueryCacheImpl(new ApcCache());
        $config->setResultCacheImpl(new ApcCache());
        $conn = config('database');
        $this->em = EntityManager::create($conn, $config);
    }

    function index()
    {
        $userRepo = $this->em->getRepository('App\Entity\User');

        $currentUser = $userRepo->findOneById(Auth::userId());
        $registeredUsers = $userRepo->getRegisteredUserList();

        return $this->twig->render('index.html.twig', [
            'currentUser' => $currentUser,
            'registeredUsers' => $registeredUsers
        ]);
    }

    function register()
    {
        $user = new User();
        $form = $this->formFactory->create(new RegisterForm(), $user);

        if ($this->request->getMethod() == 'POST' && $form->handleRequest() && $form->isValid()) {
            $loginExists = !empty(
                $this->em->getRepository(get_class($user))->findOneByLogin($user->getLogin())
            );
            if (!$loginExists) {
                $this->em->persist($user);
                $this->em->flush();

                // Clear registered users query cache
                $cacheDriver = $this->em->getConfiguration()->getResultCacheImpl();
                $cacheDriver->delete('registered_users_list');

                return new RedirectResponse('/afterReg');
            } else {
                $form->get('login')->addError(new FormError('This login is already used'));
            }
        }

        return $this->twig->render('register.html.twig', [
            'Page' => 'register',
            'form' => $form->createView()
        ]);
    }

    function afterReg() {
        return $this->twig->render('afterReg.html.twig');
    }

    function login()
    {
        $form = $this->formFactory->create(new LoginForm());

        if ($this->request->getMethod() == 'POST' && $form->handleRequest() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->em->getRepository('App\Entity\User')->findOneByLogin($data['login']);

            if (empty($user) || $user->makePassword($data['password']) != $user->getPassword()) {
                $form->get('password')->addError(new FormError('Login or password is incorrect!'));
            } else {
                Auth::logIn($user->getId());
                return new RedirectResponse('/');
            }
        }

        return $this->twig->render('login.html.twig', [
            'Page' => 'login',
            'form' => $form->createView()
        ]);
    }

    function logout() {
        if (!empty($userId = Auth::userId()))
            Auth::logOut($userId);

        return new RedirectResponse('/');
    }
}