<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType; // Classe de base pour créer des formulaires Symfony
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Type de champ pour l'email
use Symfony\Component\Form\FormBuilderInterface; // Interface pour construire le formulaire
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour configurer les options du formulaire
use Symfony\Component\Validator\Constraints\NotBlank; // Validation pour s'assurer qu'un champ n'est pas vide

class ResetPasswordRequestFormType extends AbstractType
{
    // Méthode pour construire le formulaire pour la demande de réinitialisation du mot de passe
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour l'email de l'utilisateur
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email'], // Ajout de l'attribut "autocomplete" pour aider avec le remplissage automatique du champ
                'constraints' => [
                    // Contrainte pour s'assurer que le champ n'est pas vide
                    new NotBlank([
                        'message' => 'Please enter your email', // Message d'erreur si le champ est laissé vide
                    ]),
                ],
            ])
        ;
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Aucune option spécifique n'est définie ici pour le moment
        ]);
    }
}
