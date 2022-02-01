<?php

namespace App\Form;

use App\Controller\Classe\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string', TextType::class,[
                'label' => false,
                'required'=> false,
                'attr' =>[
                    'placeholder' => 'Votre recherche',
                    'class' => 'form-control-sm'
                ]

            ])
            ->add('categories',EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                //vue en checkbox
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]

            ])
            ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false,
        ]);

    }

    //Retourne un tableau préfixé du nom de la classe: A chercher
    public function getBlockPrefix()
    {
        return '';
    }
}