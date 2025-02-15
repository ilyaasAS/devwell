<?php

namespace App\Form;

use App\Entity\User; // Importation de l'entité User pour lier les données du formulaire à l'entité User
use Symfony\Component\Form\AbstractType; // Classe de base pour créer des formulaires Symfony
use Symfony\Component\Form\FormBuilderInterface; // Interface pour construire le formulaire
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Type de champ pour le mot de passe
use Symfony\Component\Form\Extension\Core\Type\TextType; // Type de champ pour les chaînes de caractères
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Type de champ pour les emails (commenté ici)
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour configurer les options du formulaire
use Symfony\Component\Validator\Constraints\NotBlank; // Validation pour s'assurer qu'un champ n'est pas vide (commenté ici pour le mot de passe)

class ProfileType extends AbstractType
{
    // Méthode pour construire le formulaire de profil
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le prénom de l'utilisateur
            ->add('firstName', TextType::class, [
                'label' => 'Prénom', // Libellé affiché dans le formulaire
                'attr' => ['class' => 'form-control'] // Classe CSS ajoutée au champ
            ])
            // Champ pour le nom de famille de l'utilisateur
            ->add('lastName', TextType::class, [
                'label' => 'Nom de famille', // Libellé affiché dans le formulaire
                'attr' => ['class' => 'form-control'] // Classe CSS ajoutée au champ
            ])
            // Champ pour l'email, commenté car non utilisé ici
            // ->add('email', EmailType::class, [
            //     'label' => 'Email', 
            //     'attr' => ['class' => 'form-control']
            // ])
            // Champ pour le mot de passe, optionnel (l'utilisateur peut laisser vide)
            ->add('password', PasswordType::class, [
                'required' => false, // Le mot de passe n'est pas requis ici (peut être laissé vide)
                'label' => 'Mot de passe', // Libellé affiché dans le formulaire
                'attr' => ['class' => 'form-control'], // Classe CSS ajoutée au champ
                'empty_data' => '', // Si le champ est laissé vide, une chaîne vide sera utilisée
                'constraints' => [
                    // Supprimé NotBlank ici car le mot de passe peut être vide
                ],
            ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // L'entité associée au formulaire est l'entité User
            'csrf_protection' => true, // Activer la protection CSRF
        ]);
    }
}
