<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // https://symfony.com/doc/current/reference/forms/types.html
        $builder
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // bind the form to a class.
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration'],
        ]);
    }
}
