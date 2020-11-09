<?php

namespace App\Form;

use App\Entity\Creator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class, array(
                'label' => 'Wpisz email wspolautora',
                'attr' => [
                    'class' => 'col-8 float-right'
                ]
            ))
            ->add('name', TextType::class, array(
                'label' => 'Wpisz imie',
                'attr' => [
                    'class' => 'col-8 float-right'
                ]
            ))
            ->add('surname', TextType::class, array(
                'label' => 'Wpisz nazwisko',
                'attr' => [
                    'class' => 'col-8 float-right'
                ]
            ))
            ->add('participation', TextType::class, array(
                'label' => 'Udzial',
                'attr' => [
                    'placeholder' => 'Udzial',
                    'class' => 'col-8 float-right'
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Creator::class,
        ]);
    }
}
