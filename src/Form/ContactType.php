<?php

// src/Form/ContactType.php

namespace App\Form;

use App\Entity\Contact; // Importation de l'entité Contact pour lier les données du formulaire à cette entité.
use Symfony\Component\Form\AbstractType; // Classe de base pour définir des formulaires Symfony.
use Symfony\Component\Form\FormBuilderInterface; // Interface utilisée pour construire le formulaire.
use Symfony\Component\Form\Extension\Core\Type\TextType; // Type de champ pour des textes courts (ex : nom, sujet).
use Symfony\Component\Form\Extension\Core\Type\EmailType; // Type de champ pour l'email.
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Type de champ pour un message (texte long).
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Type de champ pour un bouton de soumission.
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour définir les options du formulaire.

class ContactType extends AbstractType
{
    // Cette méthode définit la structure du formulaire de contact.
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Champ 'name' pour le nom, de type 'TextType' pour un champ de texte.
            ->add('name', TextType::class, [
                'label' => 'Nom', // Libellé du champ affiché dans le formulaire.
                'attr' => ['class' => 'form-control'] // Classe CSS appliquée à ce champ pour la mise en forme.
            ])
            // Champ 'email' pour l'email, de type 'EmailType' pour un champ spécifique aux emails.
            ->add('email', EmailType::class, [
                'label' => 'Email', // Libellé du champ affiché dans le formulaire.
                'attr' => ['class' => 'form-control'] // Classe CSS pour la présentation.
            ])
            // Champ 'subject' pour le sujet du message, de type 'TextType'.
            // Ce champ est optionnel grâce à 'required' => false.
            ->add('subject', TextType::class, [
                'label' => 'Sujet', // Libellé du champ.
                'required' => false, // Champ non obligatoire.
                'attr' => ['class' => 'form-control'] // Classe CSS pour la mise en forme.
            ])
            // Champ 'message' pour le message du formulaire, de type 'TextareaType' pour un champ multiligne.
            ->add('message', TextareaType::class, [
                'label' => 'Message', // Libellé du champ.
                'attr' => ['class' => 'form-control', 'rows' => 5] // Classe CSS et nombre de lignes visibles pour ce champ.
            ])
            // Champ 'submit' pour le bouton de soumission du formulaire, de type 'SubmitType'.
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer', // Libellé du bouton.
                'attr' => ['class' => 'btn btn-primary'] // Classe CSS pour la mise en forme du bouton.
            ]);
    }

    // Configure les options du formulaire, ici l'entité 'Contact' est définie comme le type de données.
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class, // Associe ce formulaire à l'entité 'Contact'.
        ]);
    }
}
