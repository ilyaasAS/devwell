<?php

// src/Form/ProductType.php

namespace App\Form;

use App\Entity\Product; // Entité Product représentant un produit
use App\Entity\Category; // Entité Category représentant une catégorie
use Symfony\Component\Form\AbstractType; // Classe de base pour créer des formulaires Symfony
use Symfony\Component\Form\FormBuilderInterface; // Interface pour construire le formulaire
use Symfony\Component\Form\Extension\Core\Type\TextType; // Type de champ pour les chaînes de caractères
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Type de champ pour les nombres
use Symfony\Component\Form\Extension\Core\Type\FileType; // Type de champ pour télécharger un fichier
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Type de champ pour une zone de texte
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Type de champ pour un choix parmi plusieurs options
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour configurer les options du formulaire

class ProductType extends AbstractType
{
    // Méthode pour construire le formulaire du produit
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Champ pour le nom du produit
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])

            // Champ pour le prix du produit
            ->add('price', NumberType::class, ['label' => 'Prix'])

            // Champ pour la quantité en stock du produit
            ->add('stock', NumberType::class, ['label' => 'Stock'])  // Modification ici : ajout du champ stock

            // Champ pour télécharger une image du produit (fichier)
            ->add('uploadedImage', FileType::class, [
                'label' => 'Mettre une image',  // Libellé pour l'upload de l'image
                'required' => false,  // L'image est optionnelle
                'mapped' => false,  // Le champ n'est pas mappé directement à une propriété de l'entité
            ])

            // Champ pour la description du produit
            ->add('description', TextareaType::class, ['label' => 'Description'])

            // Champ pour sélectionner une catégorie parmi les choix possibles
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',  // Libellé pour le champ catégorie
                'choices' => $this->getCategoryChoices($options['categories']),  // Les catégories disponibles sont passées via les options du formulaire
                'choice_label' => function ($category) {  // Définit l'affichage des choix
                    return $category->getName();  // Affiche le nom de la catégorie
                },
                'required' => false,  // Le champ catégorie est optionnel
            ]);
    }

    // Méthode pour récupérer les choix de catégories
    private function getCategoryChoices($categories)
    {
        $choices = [];
        // Pour chaque catégorie passée, ajouter son nom comme clé et l'objet catégorie comme valeur
        foreach ($categories as $category) {
            $choices[$category->getName()] = $category;
        }
        return $choices;
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,  // L'entité associée au formulaire est Product
            'categories' => []  // Les catégories disponibles sont passées depuis le contrôleur
        ]);
    }
}
