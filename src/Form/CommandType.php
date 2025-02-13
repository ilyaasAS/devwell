<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType; // Classe de base pour les formulaires Symfony.
use Symfony\Component\Form\Extension\Core\Type\TextType; // Type de champ pour un texte court (par exemple, l'adresse).
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Type de champ pour un texte long (par exemple, un commentaire).
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Type de champ pour un bouton de soumission.
use Symfony\Component\Form\FormBuilderInterface; // Interface utilisée pour construire le formulaire.
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour configurer les options du formulaire.

class CommandType extends AbstractType
{
    // Cette méthode permet de définir les champs du formulaire pour passer une commande.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ 'address' pour l'adresse de livraison, de type 'TextType' pour un champ de texte.
            ->add('address', TextType::class, [
                'label' => 'Adresse de livraison', // Label affiché pour ce champ.
                'attr' => [
                    'placeholder' => 'Votre adresse', // Texte d'exemple à afficher dans le champ.
                    'class' => 'form-control', // Classe CSS associée à ce champ pour la mise en forme.
                ],
            ])
            // Champ 'comment' pour un commentaire facultatif, de type 'TextareaType' pour un champ de texte multiligne.
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire (facultatif)', // Label affiché pour ce champ.
                'required' => false, // Ce champ est facultatif.
                'attr' => [
                    'placeholder' => 'Ajoutez une note pour le livreur', // Texte d'exemple pour le champ.
                    'class' => 'form-control', // Classe CSS associée à ce champ pour la mise en forme.
                    'rows' => 4, // Le champ de texte aura 4 lignes visibles.
                ],
            ])
            // Champ 'submit' pour le bouton de soumission du formulaire, de type 'SubmitType'.
            ->add('submit', SubmitType::class, [
                'label' => 'Passer commande', // Texte du bouton de soumission.
                'attr' => [
                    'class' => 'w-full bg-primary_dw text-tertiary_dw py-3 rounded-lg text-lg font-semibold shadow hover:bg-primary_hover transition', // Classe CSS pour la mise en forme du bouton.
                ],
            ]);
    }

    // Configure les options du formulaire, ici il n'y a pas de paramètres supplémentaires définis.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
