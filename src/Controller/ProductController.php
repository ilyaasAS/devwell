<?php

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
    // Liste des produits
    #[Route('/product', name: 'app_product_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // Créer un nouveau produit
    #[Route('/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        $form = $this->createForm(ProductType::class, $product, [
            'categories' => $categories,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $product->getCategory();

            if (!$category && $product->getNewCategory()) {
                $newCategory = new Category();
                $newCategory->setName($product->getNewCategory());
                $entityManager->persist($newCategory);
                $product->setCategory($newCategory);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Voir un produit spécifique
    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    // Modifier un produit existant
    #[Route('/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        $form = $this->createForm(ProductType::class, $product, [
            'categories' => $categories,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un produit
    #[Route('/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Vérification CSRF pour éviter les suppressions non autorisées
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully.');
        }

        return $this->redirectToRoute('app_product_index');
    }


    // Afficher tous les produits pour les utilisateurs
    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function productsList(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_details', methods: ['GET'])]
public function productDetails(Product $product): Response
{
    return $this->render('product/details.html.twig', [
        'product' => $product,
    ]);
}
}
