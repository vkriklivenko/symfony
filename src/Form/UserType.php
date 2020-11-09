<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => 'Email'
            ))
            ->add('company', TextType::class, array(
                'label' => 'Miejsce pracy'
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                    'first_options' => array(
                        'label' => 'Enter password',
                    ),
                    'second_options' => array(
                        'label' => 'Repead password',
                )
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Zapisz',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ))
            ->add('delete',SubmitType::class, array(
                'label' => 'Usun',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ))  ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
