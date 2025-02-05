<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'label' => 'Date de création', // Ajout du label pour 'createdAt'
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
