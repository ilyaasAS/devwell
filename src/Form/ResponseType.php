<?php

// src/Form/ResponseType.php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('response', TextareaType::class, [
            'label' => 'Votre réponse',
            'required' => true,
            'attr' => ['class' => 'form-control', 'rows' => 5],
        ])
        ->add('submit', SubmitType::class, [   // Ajouter explicitement le bouton submit ici
            'label' => 'Envoyer',
            'attr' => ['class' => 'btn btn-primary']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class, // Lier au même entité Contact
        ]);
    }
}
