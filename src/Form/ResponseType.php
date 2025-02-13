<?php

// src/Form/ResponseType.php

namespace App\Form;

use App\Entity\Contact; // Utilisation de l'entité Contact, probablement pour récupérer des informations sur un contact auquel répondre
use Symfony\Component\Form\AbstractType; // Classe de base pour créer un formulaire dans Symfony
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Type de champ pour le bouton de soumission
use Symfony\Component\Form\FormBuilderInterface; // Interface utilisée pour construire un formulaire
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Type de champ pour les zones de texte (textarea)
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour configurer les options du formulaire

class ResponseType extends AbstractType
{
    // Méthode pour construire le formulaire de réponse
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Champ de texte pour la réponse
            ->add('response', TextareaType::class, [
                'label' => 'Votre réponse', // Étiquette affichée pour le champ
                'required' => true, // Ce champ est obligatoire
                'attr' => ['class' => 'form-control', 'rows' => 5], // Attributs HTML ajoutés, dont la classe CSS et le nombre de lignes du textarea
            ])
            // Ajout du bouton de soumission du formulaire
            ->add('submit', SubmitType::class, [   // Type SubmitType pour un bouton de soumission
                'label' => 'Envoyer', // Texte affiché sur le bouton
                'attr' => ['class' => 'btn btn-primary'] // Classe CSS pour styliser le bouton
            ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        // Définit la classe de données à laquelle ce formulaire est lié, ici l'entité Contact
        $resolver->setDefaults([
            'data_class' => Contact::class, // Le formulaire est lié à l'entité Contact
        ]);
    }
}
