<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Ajouté pour les champs de texte
use Symfony\Component\OptionsResolver\OptionsResolver;

class User1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom de Famille',
                'attr' => ['class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN'
                ],
                'attr' => ['class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
