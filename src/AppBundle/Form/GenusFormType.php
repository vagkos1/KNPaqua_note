<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenusFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // https://symfony.com/doc/current/reference/forms/types.html
        $builder
            ->add('name')
            // genus.subFamily is a M-1 to subFamily, so the builder will try to add a select dropdown.
            // We'll need a __toString() in subFamily for the values of that dropdown
            // By default Symfony uses a Form EntityType field for this and fetches the options by querying the Entity
            // by using null as the second argument, Symfony will continue to use EntityType field and guess the class
            ->add('subFamily', null, [
                'placeholder' => 'Choose a Sub Family'
            ])
            ->add('speciesCount')
            ->add('funFact')
            ->add('isPublished', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false
                ]
            ])
            ->add('firstDiscoveredAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // bind the form to a class.
        $resolver->setDefaults([
           'data_class' => 'AppBundle\Entity\Genus'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_genus_form_type';
    }
}
