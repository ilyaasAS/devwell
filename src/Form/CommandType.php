<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'label' => 'Adresse de livraison',
                'attr' => [
                    'placeholder' => 'Votre adresse',
                    'class' => 'form-control',
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire (facultatif)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ajoutez une note pour le livreur',
                    'class' => 'form-control',
                    'rows' => 4,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Passer commande',
                'attr' => [
                    'class' => 'btn btn-primary mt-4',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
