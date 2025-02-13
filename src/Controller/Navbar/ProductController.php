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
        // Récupérer la valeur de la recherche dans la query string, si présente
        $search = $request->query->get('search', '');
        
        // Définir la page courante. Si aucune page n'est spécifiée, on démarre à la page 1.
        $page = max(1, $request->query->getInt('page', 1)); // Numéro de la page (minimum 1)
        
        // Définir le nombre d'éléments à afficher par page
        $limit = 3; // Nombre de produits par page
        
        // Calcul de l'offset pour la pagination
        $offset = ($page - 1) * $limit; // Calcul de l'offset

        // Création de la requête avec pagination, en filtrant par nom et description de produit
        $queryBuilder = $entityManager->getRepository(Product::class)->createQueryBuilder('p')
            ->where('p.name LIKE :search OR p.description LIKE :search') // Recherche par nom ou description
            ->setParameter('search', '%' . $search . '%') // Remplacer les résultats avec le mot recherché
            ->setFirstResult($offset) // Définir l'offset pour la page courante
            ->setMaxResults($limit); // Limiter le nombre de produits à la page en cours

        // Exécuter la requête pour récupérer les produits correspondant à la recherche
        $products = $queryBuilder->getQuery()->getResult();

        // Correction : Créer une autre requête pour compter le nombre total de produits
        $totalProducts = $entityManager->getRepository(Product::class)->createQueryBuilder('p')
            ->select('COUNT(p.id)') // Sélectionner le nombre total de produits
            ->where('p.name LIKE :search OR p.description LIKE :search') // Recherche par nom ou description
            ->setParameter('search', '%' . $search . '%') // Appliquer le critère de recherche
            ->getQuery()
            ->getSingleScalarResult(); // Récupérer un seul résultat (le nombre total)

        // Rendu de la vue avec les résultats, la recherche et la pagination
        return $this->render('navbar/product/products.html.twig', [
            'products' => $products, // Passer les produits à la vue
            'search' => $search, // Passer le terme de recherche à la vue
            'currentPage' => $page, // Passer la page actuelle à la vue
            'totalPages' => ceil($totalProducts / $limit), // Calculer le nombre total de pages
        ]);
    }

    // Voir un produit spécifique (affichage pour l'utilisateur)
    #[Route('/products/{id}', name: 'app_product_details', methods: ['GET'])]
    public function productDetails(Product $product): Response
    {
        // Afficher les détails d'un produit spécifique
        return $this->render('navbar/product/details.html.twig', [
            'product' => $product, // Passer l'objet produit à la vue
        ]);
    }

}
