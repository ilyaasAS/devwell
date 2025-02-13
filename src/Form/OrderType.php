<?php

namespace App\Form;

use App\Entity\Order; // Entité représentant une commande.
use App\Entity\OrderItem; // Entité représentant un article dans une commande (bien que non utilisée ici, elle peut être utilisée dans un formulaire d'articles).
use Symfony\Component\Form\AbstractType; // Classe de base pour définir un formulaire Symfony.
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Type de champ permettant de choisir une valeur parmi une liste d'options.
use Symfony\Component\Form\Extension\Core\Type\DateTimeType; // Type de champ pour gérer les dates et heures.
use Symfony\Component\Form\Extension\Core\Type\CollectionType; // Permet de créer des champs pour des collections d'entités (pas utilisé ici, mais pourrait être pertinent pour les articles de la commande).
use Symfony\Component\Form\FormBuilderInterface; // Interface pour construire le formulaire.
use Symfony\Component\OptionsResolver\OptionsResolver; // Utilisé pour configurer les options du formulaire.
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Permet de lier une entité à un champ de formulaire (pas utilisé ici, mais pourrait être pertinent pour lier un utilisateur ou un produit).
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Type de champ pour les nombres entiers (pas utilisé ici, mais pourrait être pertinent pour une quantité d'articles).

class OrderType extends AbstractType
{
    // Méthode pour construire le formulaire de la commande.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // Champ pour sélectionner le statut de la commande parmi plusieurs choix.
        ->add('status', ChoiceType::class, [
            'label' => 'Statut de la commande', // Libellé affiché pour le champ.
            'choices' => [
                'En attente' => 'en attente', // Choix "En attente" avec valeur 'en attente'.
                'Payée' => 'payée', // Choix "Payée" avec valeur 'payée'.
                'Traitement' => 'traitement', // Choix "Traitement" avec valeur 'traitement'.
                'Livraison en cours' => 'livraison_en_cours', // Choix "Livraison en cours" avec valeur 'livraison_en_cours'.
                'Livrée' => 'livrée', // Choix "Livrée" avec valeur 'livrée'.
                'Remboursée' => 'remboursée', // Choix "Remboursée" avec valeur 'remboursée'.
                'Annulée' => 'annulée', // Choix "Annulée" avec valeur 'annulée'.
            ],
        ])
        // Champ pour la date et l'heure du statut de la commande.
        ->add('createdAt', DateTimeType::class, [
            'label' => 'Date du statut', // Libellé affiché pour le champ.
            'widget' => 'single_text', // Affiche un champ de texte unique pour la date et l'heure (sans champs séparés pour le jour, mois, année, etc.).
        ]);
    }

    // Méthode pour configurer les options du formulaire.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class, // Ce formulaire est associé à l'entité Order.
        ]);
    }
}
