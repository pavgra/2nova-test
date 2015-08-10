<?php namespace App\Extensions;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;

class AppFormFactory
{

    /** @var FormFactoryBuilderInterface */
    private $formFactoryBuilder;
    /** @var string */
    private $vendorDir;
    /** @var \Twig_Environment */
    private $twig;
    /** @var string */
    private $lang;
    /** @var string[] */
    private $componentDir;

    public function __construct($twig, $lang = 'en', $vendorDir = null)
    {
        $this->formFactoryBuilder = Forms::createFormFactoryBuilder();
        $this->twig = $twig;
        $this->lang = $lang;
        $this->vendorDir = $vendorDir ?: base_path('vendor');
        $this->loadComponentDirs();
    }

    private function loadComponentDirs()
    {
        $this->componentDir = [
            'form' => $this->vendorDir . '/symfony/form',
            'validator' => $this->vendorDir . '/symfony/validator',
            'twigBridge' => $this->vendorDir . '/symfony/twig-bridge'
        ];
    }

    public function build()
    {
        $this->addExtensions();
        return $this->formFactoryBuilder->getFormFactory();
    }

    private function addExtensions()
    {
        $csrfProvider = null;

        $extensions = [
            $this->getCsrfExtension($csrfProvider),
            $this->getValidatorExtension()
        ];

        foreach ($extensions as $extension) {
            $this->formFactoryBuilder->addExtension($extension);
        }

        $this->extendTwig($csrfProvider);
    }

    private function getCsrfExtension(&$csrfProvider)
    {
        $csrfSecret = 'MDFp86iuAt50XeSCOfgINhOgzm4xPgbS';
        $session = new Session();
        $csrfProvider = new SessionCsrfProvider($session, $csrfSecret);
        return new CsrfExtension($csrfProvider);
    }

    private function getValidatorExtension()
    {
        $validator = Validation::createValidator();
        return new ValidatorExtension($validator);
    }

    private function extendTwig($csrfProvider)
    {
        $translator = new Translator($this->lang);
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource('xlf', $this->componentDir['form'] . '/Resources/translations/validators.en.xlf', 'en', 'validators');
        $translator->addResource('xlf', $this->componentDir['validator'] . '/Resources/translations/validators.en.xlf', 'en', 'validators');

        $formTheme = 'bootstrap_3_layout.html.twig';//'form_div_layout.html.twig';
        $formEngine = new TwigRendererEngine([$formTheme]);

        $twigLoader = $this->twig->getLoader();
        $newTwigLoader = new \Twig_Loader_Chain([
            $twigLoader,
            new \Twig_Loader_Filesystem([$this->componentDir['twigBridge'] . '/Resources/views/Form',]),
        ]);

        $this->twig->setLoader($newTwigLoader);
        $formEngine->setEnvironment($this->twig);
        $this->twig->addExtension(new TranslationExtension($translator));
        $this->twig->addExtension(new FormExtension(new TwigRenderer($formEngine, $csrfProvider)));
    }

}