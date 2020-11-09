<?php

namespace App\Form;

use App\Entity\Creator;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Tytul',
                'attr' => [
                    'placeholder' => 'Wprowadz tytul',
                    'class' => 'col-11 float-right'
                ]
            ))
            ->add('year', DateType::class, array(
                'label' => 'Rok wydania',
                'attr' => [
                    'placeholder' => 'Wprowadz rok wydania publikacji',
                    'class' => 'col-11 float-right'
                ]
            ))
            ->add('creator', CollectionType::class, array(
                'entry_type' => CreatorType::class,
                'entry_options' => [
                'label' => 'Kto stworzyl'
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
//                'class' => Creator::class,
//                'choice_label' => 'user',
                'attr' => [
                    'placeholder' => 'Tworca',
                    'class' => 'col-11 float-right'
                ]
            ))

            ->add('numOfPoints', TextType::class, array(
                'label' => 'Points',
                'attr' => [
                    'placeholder' => 'Liczba punktow',
                    'class' => 'col-11 float-right'
                ]
            ))
            ->add('conference', TextType::class, array(
                'label' => 'Konferencja',
                'attr' => [
                    'placeholder' => 'Gdzie zostalo ogloszone',
                    'class' => 'col-11 float-right'
                ]
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Zapisz',
                'attr' => [
                    'class' => 'btn btn-success float-left'
                ]
            ))
            ->add('delete',SubmitType::class, array(
                'label' => 'Usun',
                'attr' => [
                'class' => 'btn btn-danger'
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
