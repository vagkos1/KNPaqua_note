<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenusFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            // genus.subFamily is a M-1 to subFamily, so the builder will try to add a select box.
            // We'll need a __toString() in subFamily for the values of that dropdown
            ->add('subFamily')
            ->add('speciesCount')
            ->add('funFact')
            ->add('isPublished')
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
