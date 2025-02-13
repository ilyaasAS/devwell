<?php

namespace App\Form;

use App\Entity\User; // Utilisation de l'entité User pour lier ce formulaire à cette entité
use Symfony\Component\Form\AbstractType; // Classe de base pour la création des formulaires dans Symfony
use Symfony\Component\Form\FormBuilderInterface; // Interface pour construire les champs du formulaire
use Symfony\Component\OptionsResolver\OptionsResolver; // Permet de configurer les options du formulaire
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Type de champ pour le mot de passe
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Type de champ pour l'email
use Symfony\Component\Form\Extension\Core\Type\TextType; // Type de champ pour les champs texte
use Symfony\Component\Validator\Constraints\Length; // Contrainte pour la longueur des champs (ex. mot de passe)
use Symfony\Component\Validator\Constraints\NotBlank; // Contrainte pour vérifier que le champ n'est pas vide
use Symfony\Component\Validator\Constraints\Email; // Contrainte pour vérifier que l'email est valide

class UserType extends AbstractType
{
    // Méthode pour construire le formulaire de création/édition d'un utilisateur
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le prénom de l'utilisateur
            ->add('firstName', TextType::class, [
                'constraints' => [
                    // La contrainte NotBlank s'assure que ce champ n'est pas vide
                    new NotBlank(['message' => 'First name cannot be empty.'])
                ]
            ])
            // Champ pour le nom de famille de l'utilisateur
            ->add('lastName', TextType::class, [
                'constraints' => [
                    // La contrainte NotBlank s'assure que ce champ n'est pas vide
                    new NotBlank(['message' => 'Last name cannot be empty.'])
                ]
            ])
            // Champ pour l'email de l'utilisateur
            ->add('email', EmailType::class, [
                'constraints' => [
                    // Vérifie que l'email n'est pas vide
                    new NotBlank(['message' => 'Email cannot be empty.']),
                    // Vérifie que l'email est dans un format valide
                    new Email(['message' => 'Please provide a valid email address.'])
                ]
            ])
            // Champ pour le mot de passe de l'utilisateur
            ->add('password', PasswordType::class, [
                'constraints' => [
                    // Vérifie que le mot de passe n'est pas vide
                    new NotBlank(['message' => 'Password cannot be empty.']),
                    // Vérifie la longueur du mot de passe
                    new Length([
                        'min' => 8, // Longueur minimale du mot de passe
                        'minMessage' => 'Password should be at least {{ limit }} characters.', // Message d'erreur si trop court
                        'max' => 255, // Longueur maximale du mot de passe
                    ])
                ]
            ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        // Lient ce formulaire à l'entité User (les données du formulaire seront liées à un objet User)
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
