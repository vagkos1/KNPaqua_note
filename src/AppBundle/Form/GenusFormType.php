<?php

namespace AppBundle\Form;

use AppBundle\Entity\SubFamily;
use AppBundle\Repository\SubFamilyRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            // but we can also explicitly set it so that it's more obvious
            ->add('subFamily', EntityType::class, [
                'placeholder' => 'Choose a Sub Family',
                'class' => SubFamily::class,
                'query_builder' => function (SubFamilyRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                },
            ])
            ->add('speciesCount')
            ->add('funFact')
            ->add('isPublished', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
            ])
            ->add('firstDiscoveredAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'html5' => false,
            ])
            ->add('genusScientists', CollectionType::class, [
                'entry_type' => GenusScientistEmbeddedForm::class,
                'allow_delete' => true,
                'by_reference' => false,
            ])
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
