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
        // Ajout du champ stock dans le formulaire pour permettre la modification du stock
$builder
->add('name', TextType::class, ['label' => 'Name'])
->add('price', NumberType::class, ['label' => 'Price'])
->add('stock', NumberType::class, ['label' => 'Stock'])  // Modification ici
->add('uploadedImage', FileType::class, [
    'label' => 'Upload Image',
    'required' => false,
    'mapped' => false, 
])
->add('description', TextareaType::class, ['label' => 'Description'])
->add('category', ChoiceType::class, [
    'choices' => $this->getCategoryChoices($options['categories']),
    'choice_label' => function($category) {
        return $category->getName();
    },
    'required' => false,
]);

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
