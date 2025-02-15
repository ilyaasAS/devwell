<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType; // Classe de base pour définir des formulaires Symfony.
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Type de champ pour l'email.
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Type de champ pour le mot de passe.
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Type de champ pour le bouton de soumission.
use Symfony\Component\Form\FormBuilderInterface; // Interface utilisée pour construire le formulaire.
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour définir les options du formulaire.
use Symfony\Component\Validator\Constraints\NotBlank; // Contraintes pour vérifier si un champ est non vide.
use Symfony\Component\Validator\Constraints\Email; // Contrainte pour valider l'email.

class LoginType extends AbstractType
{
    // Méthode pour construire le formulaire.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ 'email' de type 'EmailType', utilisé pour saisir une adresse e-mail.
            ->add('email', EmailType::class, [
                'label' => 'Email', // Libellé du champ.
                'attr' => ['placeholder' => 'Entrez votre email'], // Placeholder et attributs CSS pour la présentation.
                'constraints' => [ // Contraintes de validation pour l'email.
                    new NotBlank([ // Le champ ne doit pas être vide.
                        'message' => 'Veuillez entrer votre adresse e-mail.', // Message si le champ est vide.
                    ]),
                    new Email([ // Le champ doit être une adresse email valide.
                        'message' => 'Veuillez entrer une adresse e-mail valide.', // Message si l'email est invalide.
                    ]),
                ]
            ])
            // Champ 'password' de type 'PasswordType', utilisé pour saisir un mot de passe.
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe', // Libellé du champ.
                'attr' => ['placeholder' => 'Entrez votre mot de passe'], // Placeholder et attributs CSS pour la présentation.
                'constraints' => [ // Contraintes de validation pour le mot de passe.
                    new NotBlank([ // Le champ ne doit pas être vide.
                        'message' => 'Veuillez entrer votre mot de passe.', // Message si le champ est vide.
                    ]),
                ]
            ])
            // Champ 'submit' pour le bouton de soumission du formulaire.
            ->add('submit', SubmitType::class, [
                'label' => 'Se connecter', // Libellé du bouton.
            ]);
    }

    // Méthode pour configurer les options du formulaire.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true // Activer la protection CSRF
        ]);
    }
}
