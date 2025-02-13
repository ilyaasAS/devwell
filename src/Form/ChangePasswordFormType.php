<?php

// Déclare l'espace de noms pour ce formulaire qui est utilisé pour changer le mot de passe de l'utilisateur.
namespace App\Form;

use Symfony\Component\Form\AbstractType; // Importation de la classe de base pour les formulaires Symfony.
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Importation du type de champ pour le mot de passe.
use Symfony\Component\Form\Extension\Core\Type\RepeatedType; // Type de champ pour les champs répétés, comme les champs de mot de passe.
use Symfony\Component\Form\FormBuilderInterface; // Interface pour construire le formulaire.
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour configurer les options du formulaire.
use Symfony\Component\Validator\Constraints\Length; // Contrainte pour la longueur d'un champ.
use Symfony\Component\Validator\Constraints\NotBlank; // Contrainte pour empêcher un champ vide.
use Symfony\Component\Validator\Constraints\NotCompromisedPassword; // Contrainte qui empêche d'utiliser un mot de passe compromis.
use Symfony\Component\Validator\Constraints\PasswordStrength; // Contrainte qui vérifie la solidité du mot de passe.

class ChangePasswordFormType extends AbstractType
{
    // Cette méthode permet de définir les champs du formulaire pour changer le mot de passe.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Le champ 'plainPassword' est de type 'RepeatedType' pour demander à l'utilisateur de saisir deux fois son mot de passe.
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class, // Les champs répétés seront de type 'Password'.
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password', // Empêche l'auto-complétion du mot de passe par le navigateur.
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        // Contrainte pour s'assurer que le mot de passe n'est pas vide.
                        new NotBlank([
                            'message' => 'Please enter a password', // Message d'erreur si vide.
                        ]),
                        // Contrainte de longueur minimale et maximale.
                        new Length([
                            'min' => 12, // Le mot de passe doit avoir au moins 12 caractères.
                            'minMessage' => 'Your password should be at least {{ limit }} characters', // Message d'erreur en cas de mot de passe trop court.
                            'max' => 4096, // Limite maximale de caractères, par défaut dans Symfony.
                        ]),
                        // Contrainte pour vérifier la solidité du mot de passe.
                        new PasswordStrength(),
                        // Contrainte pour s'assurer que le mot de passe n'a pas été compromis.
                        new NotCompromisedPassword(),
                    ],
                    'label' => 'Nouveau mot de passe', // Label du premier champ de mot de passe.
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe', // Label du second champ de mot de passe.
                ],
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.', // Message d'erreur si les mots de passe ne correspondent pas.
                // Ne pas mapper ce champ sur l'entité, car il sera traité dans le contrôleur.
                'mapped' => false,
            ]);
    }

    // Configure les options du formulaire, mais ici aucune option par défaut n'est définie.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
