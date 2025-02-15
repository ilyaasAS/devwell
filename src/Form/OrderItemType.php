<?php

namespace App\Form;

use App\Entity\OrderItem; // Entité représentant un article de commande.
use App\Entity\Product; // Entité représentant un produit.
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Type de champ permettant de lier une entité (ici Product) au formulaire.
use Symfony\Component\Form\AbstractType; // Classe de base pour définir un formulaire Symfony.
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Type de champ pour un nombre entier (ici la quantité).
use Symfony\Component\Form\FormBuilderInterface; // Interface utilisée pour construire le formulaire.
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour définir les options du formulaire.

class OrderItemType extends AbstractType
{
    // Méthode pour construire le formulaire.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ 'product' pour sélectionner un produit de la base de données.
            ->add('product', EntityType::class, [
                'class' => Product::class, // Entité Product utilisée pour peupler la liste déroulante.
                'choice_label' => 'name', // Le label de chaque option dans la liste est le nom du produit.
                'label' => 'Produit', // Libellé affiché pour ce champ.
            ])
            // Champ 'quantity' pour spécifier la quantité du produit commandé.
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité', // Libellé affiché pour ce champ.
            ]);
    }

    // Méthode pour configurer les options du formulaire.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class, // Associe ce formulaire à l'entité OrderItem.
            'csrf_protection' => true, // Activer la protection CSRF
        ]);
    }
}
