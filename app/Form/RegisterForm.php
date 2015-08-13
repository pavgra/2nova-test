<?php namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;


class RegisterForm extends AbstractType
{
    /**
     * Builds the LoginForm form
     * @param  \Symfony\Component\Form\FormBuilderInterface $builder
     * @param  array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'required' => true
        ]);
        $builder->add('login', 'text', [
            'required' => true,
            'constraints' => [
                new Length([
                    'min' => 4,
                    'minMessage' => 'Login is too short. It should have {{ limit }} characters or more.'
                ])
            ]
        ]);
        $builder->add('password', 'repeated', [
            'type' => 'password',
            'required' => true,
            'invalid_message' => 'The password fields must match.',
            'constraints' => [
                new Length([
                    'min' => 6,
                    'minMessage' => 'Password is too short. It should have {{ limit }} characters or more.'
                ])
            ],
            'first_options'  => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeat Password']
        ]);
    }

    /*public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\User',
            'constraints' => [
                new UniqueEntity()
            ]
        ]);
    }*/

    /**
     * Mandatory in Symfony2
     * Gets the unique name of this form.
     * @return string
     */
    public function getName()
    {
        return 'register_form';
    }
}