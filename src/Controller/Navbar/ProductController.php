<?php

namespace App\Controller\Navbar;

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

// Liste des produits avec une fonctionnalité de recherche pour les utilisateurs
#[Route('/products', name: 'app_products', methods: ['GET'])]
public function productsList(Request $request, EntityManagerInterface $entityManager): Response
{
    $search = $request->query->get('search', '');
    $page = max(1, $request->query->getInt('page', 1)); // Numéro de la page (minimum 1)
    $limit = 3; // Nombre de produits par page
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

    return $this->render('navbar/product/products.html.twig', [
        'products' => $products,
        'search' => $search,
        'currentPage' => $page,
        'totalPages' => ceil($totalProducts / $limit),
    ]);
}

    // Voir un produit spécifique (affichage pour l'utilisateur)
    #[Route('/products/{id}', name: 'app_product_details', methods: ['GET'])]
    public function productDetails(Product $product): Response
    {
        return $this->render('navbar/product/details.html.twig', [
            'product' => $product,
        ]);
    }

}
