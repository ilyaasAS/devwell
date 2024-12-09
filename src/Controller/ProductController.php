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
use Knp\Component\Pager\PaginatorInterface;


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
    $search = $request->query->get('search', '');
    $page = max(1, $request->query->getInt('page', 1)); // Numéro de la page (minimum 1)
    $limit = 1; // Nombre de produits par page
    $offset = ($page - 1) * $limit; // Calcul de l'offset

    // Création de la requête avec pagination
    $queryBuilder = $entityManager->getRepository(Product::class)->createQueryBuilder('p')
        ->where('p.name LIKE :search OR p.description LIKE :search')
        ->setParameter('search', '%' . $search . '%')
        ->setFirstResult($offset)
        ->setMaxResults($limit);

    $products = $queryBuilder->getQuery()->getResult();

    // Correction: Utiliser un autre QueryBuilder pour compter le nombre total de produits
    $totalProducts = $entityManager->getRepository(Product::class)->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('p.name LIKE :search OR p.description LIKE :search')
        ->setParameter('search', '%' . $search . '%')
        ->getQuery()
        ->getSingleScalarResult();

    return $this->render('product/products.html.twig', [
        'products' => $products,
        'search' => $search,
        'currentPage' => $page,
        'totalPages' => ceil($totalProducts / $limit),
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
            // Récupérer la valeur du stock et la définir dans l'entité Product
            $stock = $form->get('stock')->getData();
            $product->setStock($stock);

            // Gestion de l'upload de l'image
            $uploadedFile = $form->get('uploadedImage')->getData();
            if ($uploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('product_images_directory'),
                    $newFilename
                );
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
    #[Route('/products/{id}', name: 'app_product_details', methods: ['GET'])]
    public function productDetails(Product $product): Response
    {
        return $this->render('product/details.html.twig', [
            'product' => $product,
        ]);
    }

    // Voir un produit spécifique (affichage pour l'admin)
    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function productShow(Product $product): Response
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
            // Récupérer la valeur du stock et la définir dans l'entité Product
            $stock = $form->get('stock')->getData();
            $product->setStock($stock);

            // Gestion de l'upload d'image
            $uploadedFile = $form->get('uploadedImage')->getData();
            if ($uploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('product_images_directory'),
                    $newFilename
                );
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
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully.');
        }

        return $this->redirectToRoute('app_product_index');
    }
}
