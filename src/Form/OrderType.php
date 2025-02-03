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
                'label' => 'Statut de la commande', // Ajout du label pour 'status'
                'choices' => [
                    'En attente' => 'en attente',
                    'Traitement' => 'traitement',
                    'Expédié' => 'expédié',
                    'Livrée' => 'livrée',
                    'Annulé' => 'annulé',
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
