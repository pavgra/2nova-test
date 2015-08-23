<?php
namespace App\Http\Controllers;

use App\Entity\User;
use App\Extensions\Auth;
use App\Form\LoginForm;
use App\Form\RegisterForm;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PageController extends Controller
{
    /** @var \App\Entity\UserRepository */
    protected $userRepo;

    public function __construct()
    {
        parent::__construct();
        $this->userRepo = $this->em->getRepository('App\Entity\User');
    }

    public function index()
    {
        // Get current user info
        $currentUser = $this->userRepo->findOneById(Auth::userId());
        // Get all registered users info
        $registeredUsers = $this->userRepo->getRegisteredUserList();

        // Render index page with registered users list (highlight current user if authed)
        return $this->twig->render(
            'index.html.twig',
            [
                'currentUser' => $currentUser,
                'registeredUsers' => $registeredUsers
            ]
        );
    }

    public function register()
    {
        // Build register form
        $user = new User();
        $form = $this->formFactory->create(new RegisterForm(), $user);

        // On registration attempt (if form was POST-ed and data is valid)
        if ($this->request->getMethod() == 'POST' && $form->handleRequest() && $form->isValid()) {
            // Check whether login is not used
            $login = $user->getLogin();
            if (!$this->userRepo->findOneByLogin($login)) {
                // Create new user
                $this->em->persist($user);
                $this->em->flush();

                // Clear registered users query cache
                $cacheDriver = $this->em->getConfiguration()->getResultCacheImpl();
                $cacheDriver->delete('registered_users_list');

                // Redirect to successful registration page
                return new RedirectResponse('/afterReg');
            } else {
                // Display that such login is already used
                $form->get('login')->addError(new FormError('This login is already used'));
            }
        }

        // Render page with register form
        return $this->twig->render(
            'register.html.twig',
            [
                'Page' => 'register',
                'form' => $form->createView()
            ]
        );
    }

    public function afterReg()
    {
        return $this->twig->render('afterReg.html.twig');
    }

    public function login()
    {
        // Build login form
        $form = $this->formFactory->create(new LoginForm());

        // On login attempt (if form was POST-ed and data is valid)
        if ($this->request->getMethod() == 'POST' && $form->handleRequest() && $form->isValid()) {
            // Retrieve POST-ed data
            $data = $form->getData();
            // Try to find user by given login
            /** @var $user \App\Entity\User */
            $user = $this->userRepo->findOneByLogin($data['login']);

            // If user was not found or password check failed
            if (!$user || $user->makePassword($data['password']) != $user->getPassword()) {
                // Display error
                $form->get('password')->addError(new FormError('Login or password is incorrect!'));
            } else {
                // Login user and redirect to home page
                Auth::logIn($user->getId());
                return new RedirectResponse('/');
            }
        }

        // Render page with login form
        return $this->twig->render(
            'login.html.twig',
            [
                'Page' => 'login',
                'form' => $form->createView()
            ]
        );
    }

    public function logout()
    {
        if (Auth::userId()) {
            Auth::logOut();
        }

        return new RedirectResponse('/');
    }
}