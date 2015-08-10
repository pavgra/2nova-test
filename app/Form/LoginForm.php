<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class LoginForm extends AbstractType
{
    /**
     * Builds the LoginForm form
     * @param  \Symfony\Component\Form\FormBuilderInterface $builder
     * @param  array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login', 'text');
        $builder->add('password', 'password');
    }

    /**
     * Mandatory in Symfony2
     * Gets the unique name of this form.
     * @return string
     */
    public function getName()
    {
        return 'login_form';
    }
}