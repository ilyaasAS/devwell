<?php

// Déclare l'espace de noms pour cette classe, indiquant qu'elle fait partie du formulaire de gestion des catégories dans l'application.
namespace App\Form;

use App\Entity\Category; // Importation de la classe Category pour lier ce formulaire à l'entité Category.
use Symfony\Component\Form\AbstractType; // Importation de la classe de base pour créer un formulaire Symfony.
use Symfony\Component\Form\FormBuilderInterface; // Interface pour construire le formulaire.
use Symfony\Component\Form\Extension\Core\Type\TextType; // Type de champ de formulaire pour un champ de texte.
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Type de champ de formulaire pour un champ de texte multilignes.
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour définir les options de configuration du formulaire.

class CategoryType extends AbstractType
{
    // Cette méthode permet de définir les champs du formulaire.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ 'name' qui sera un champ texte, avec un label personnalisé.
            ->add('name', TextType::class, ['label' => 'Nom de la catégorie'])

            // Champ 'description' qui sera un champ de texte multilignes (textarea), avec un label personnalisé.
            // Ce champ n'est pas requis.
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false]);
    }

    // Cette méthode configure les options du formulaire, ici elle lie le formulaire à l'entité Category.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class, // Définit la classe de données associée au formulaire.
            'csrf_protection' => true, // Activer la protection CSRF
        ]);
    }
}
