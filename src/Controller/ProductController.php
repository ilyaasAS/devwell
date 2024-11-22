<?php

// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    // Créer un nouveau produit
    #[Route('/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $categories = $entityManager->getRepository(Category::class)->findAll();
        
        // Créer le formulaire pour le produit
        $form = $this->createForm(ProductType::class, $product, [
            'categories' => $categories
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la catégorie
            $category = $product->getCategory();
            
            // Si aucune catégorie n'est associée et que la nouvelle catégorie est fournie
            if (!$category && $product->getNewCategory()) {
                $newCategory = new Category();
                $newCategory->setName($product->getNewCategory());
                $entityManager->persist($newCategory);  // Persister la nouvelle catégorie
                $product->setCategory($newCategory);  // Associer la catégorie au produit
            }

            // Sauvegarder le produit dans la base de données
            $entityManager->persist($product);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_product_index');  // Redirection après création
        }
        
        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),  // Passer le formulaire à la vue
        ]);
    }

    // Liste des produits
    #[Route('/product', name: 'app_product_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
}
