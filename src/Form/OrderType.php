<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\OrderItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('status', ChoiceType::class, [
            'label' => 'Statut de la commande',
            'choices' => [
                'En attente' => 'en attente',
                'Payée' => 'payée',
                'Traitement' => 'traitement',
                'Livraison en cours' => 'livraison_en_cours',
                'Livrée' => 'livrée',
                'Remboursée' => 'remboursée',
                'Annulée' => 'annulée',
            ],
        ])
        ->add('createdAt', DateTimeType::class, [
            'label' => 'Date de création',
            'widget' => 'single_text',
        ])
        ->add('orderItems', CollectionType::class, [
            'entry_type' => OrderItemType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'Articles de la commande',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
