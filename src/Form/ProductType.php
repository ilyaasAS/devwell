<?php

// src/Form/ProductType.php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('price', NumberType::class, ['label' => 'Price'])
            ->add('stock', NumberType::class, ['label' => 'Stock'])
            
            // corriger l'erreur //
            ->add('uploadedImage', FileType::class, [
                'label' => 'Upload Image',
                'required' => false,
                'mapped' => false, // Ce champ ne sera pas mappé à l'entité directement
            ])
            
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('category', ChoiceType::class, [
                'choices' => $this->getCategoryChoices($options['categories']),
                'choice_label' => function($category) {
                    return $category->getName();
                },
                'required' => false,
            ]);
            // ->add('newCategory', TextType::class, [
            //     'label' => 'Create a new category',
            //     'required' => false
            // ]);
    }

    private function getCategoryChoices($categories)
    {
        $choices = [];
        foreach ($categories as $category) {
            $choices[$category->getName()] = $category;
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'categories' => [] // Pass categories from controller
        ]);
    }
}
