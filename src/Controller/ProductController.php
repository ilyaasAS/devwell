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
    // Liste des produits pour l'administration
    #[Route('/product', name: 'app_product_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // Liste des produits avec une fonctionnalité de recherche pour les utilisateurs
    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function productsList(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la valeur de la recherche (si elle existe)
        $search = $request->query->get('search', '');

        // Construire la requête pour chercher des produits par nom ou description
        $productsQuery = $entityManager->getRepository(Product::class)->createQueryBuilder('p')
            ->where('p.name LIKE :search OR p.description LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery();

        // Exécuter la requête pour récupérer les produits filtrés
        $products = $productsQuery->getResult();

        return $this->render('product/products.html.twig', [
            'products' => $products,
            'search' => $search // Passer le terme de recherche pour le pré-remplir dans le champ de recherche
        ]);
    }

    // Créer un nouveau produit
    #[Route('/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    // public function neww(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $product = new Product();
    //     $categories = $entityManager->getRepository(Category::class)->findAll();

    //     $form = $this->createForm(ProductType::class, $product, [
    //         'categories' => $categories,
    //     ]);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $category = $product->getCategory();

    //         if (!$category && $product->getNewCategory()) {
    //             $newCategory = new Category();
    //             $newCategory->setName($product->getNewCategory());
    //             $entityManager->persist($newCategory);
    //             $product->setCategory($newCategory);
    //         }

    //         $entityManager->persist($product);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_product_index');
    //     }

    //     return $this->render('product/new.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $product = new Product();
    $categories = $entityManager->getRepository(Category::class)->findAll();

    $form = $this->createForm(ProductType::class, $product, [
        'categories' => $categories,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'upload de l'image
        $uploadedFile = $form->get('uploadedImage')->getData();

        if ($uploadedFile) {
            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension(); // Générer un nom unique

            // Déplacer le fichier vers le répertoire d'upload
            $uploadedFile->move(
                $this->getParameter('product_images_directory'), // Chemin défini dans services.yaml
                $newFilename
            );

            // Mettre à jour l'image dans l'entité Product
            $product->setImage($newFilename);
        }

        // Sauvegarder le produit dans la base de données
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_product_index');
    }

    return $this->render('product/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    // Voir un produit spécifique (affichage pour l'utilisateur)
    #[Route('/products/{id}', name: 'app_product_show', methods: ['GET'])]
    public function productDetails(Product $product): Response
    {
        return $this->render('product/details.html.twig', [
            'product' => $product,
        ]);
    }

    // Modifier un produit existant
    #[Route('/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    // {
    //     $categories = $entityManager->getRepository(Category::class)->findAll();

    //     $form = $this->createForm(ProductType::class, $product, [
    //         'categories' => $categories,
    //     ]);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_product_index');
    //     }

    //     return $this->render('product/edit.html.twig', [
    //         'product' => $product,
    //         'form' => $form->createView(),
    //     ]);
    // }
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
{
    $categories = $entityManager->getRepository(Category::class)->findAll();

    $form = $this->createForm(ProductType::class, $product, [
        'categories' => $categories,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'upload d'image
        $uploadedFile = $form->get('uploadedImage')->getData();
        if ($uploadedFile) {
            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

            // Déplacez le fichier vers le répertoire d'upload
            $uploadedFile->move(
                $this->getParameter('product_images_directory'), // Chemin défini dans services.yaml
                $newFilename
            );

            // Met à jour le champ `image` avec le nouveau nom de fichier
            $product->setImage($newFilename);
        }

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
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully.');
        }

        return $this->redirectToRoute('app_product_index');
    }
}

