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
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Champ pour le prénom de l'utilisateur
        $builder->add('firstName', TextType::class, [
            'label' => 'Prénom',  // Label pour le champ
            'attr' => [
                'class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw'
            ]  // Classes CSS pour styliser le champ
        ]);

        // Champ pour le nom de famille de l'utilisateur
        $builder->add('lastName', TextType::class, [
            'label' => 'Nom de Famille',  // Label pour le champ
            'attr' => [
                'class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw'
            ]  // Classes CSS pour styliser le champ
        ]);

        // Si l'option 'is_admin_edit' est false (non admin), afficher le champ email
        if (empty($options['is_admin_edit']) || !$options['is_admin_edit']) {
            $builder->add('email', EmailType::class, [
                'label' => 'Email',  // Label pour le champ
                'attr' => [
                    'class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw'
                ]  // Classes CSS pour styliser le champ
            ]);
        }

        // Si l'option 'is_admin_edit' est false (non admin), afficher le champ mot de passe
        if (empty($options['is_admin_edit']) || !$options['is_admin_edit']) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',  // Label pour le champ
                'attr' => [
                    'class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw'
                ]  // Classes CSS pour styliser le champ
            ]);
        }

        // Champ pour les rôles de l'utilisateur
        $builder->add('roles', ChoiceType::class, [
            'label' => 'Roles',  // Label pour le champ
            'multiple' => true,  // Permet de sélectionner plusieurs rôles
            'expanded' => true,  // Affiche les choix sous forme de cases à cocher
            'choices' => [
                'ROLE_USER' => 'ROLE_USER',  // Option pour l'utilisateur
                'ROLE_ADMIN' => 'ROLE_ADMIN'  // Option pour l'administrateur
            ],
            'attr' => [
                'class' => 'w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-primary_dw focus:border-primary_dw'
            ]  // Classes CSS pour styliser le champ
        ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,  // L'entité liée à ce formulaire
            'is_admin_edit' => false,  // Par défaut, on suppose que l'utilisateur ne modifie pas les infos en tant qu'admin
        ]);
    }
}
