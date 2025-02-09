<?php

namespace App\Controller\Navbar;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    // Afficher toutes les catégories pour l'utilisateur avec pagination
#[Route('/categorie', name: 'app_category_user_index', methods: ['GET'])]
public function indexu(Request $request, EntityManagerInterface $entityManager): Response
{
    $page = max(1, $request->query->getInt('page', 1)); // Numéro de la page (minimum 1)
    $limit = 1; // Nombre de catégories par page
    $offset = ($page - 1) * $limit; // Calcul de l'offset

    // Récupérer les catégories avec pagination
    $queryBuilder = $entityManager->getRepository(Category::class)->createQueryBuilder('c')
        ->setFirstResult($offset)
        ->setMaxResults($limit);

    $categories = $queryBuilder->getQuery()->getResult();

    // Nombre total de catégories
    $totalCategories = $entityManager->getRepository(Category::class)->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->getQuery()
        ->getSingleScalarResult();

    return $this->render('navbar/category/user_index.html.twig', [
        'categories' => $categories,
        'currentPage' => $page,
        'totalPages' => ceil($totalCategories / $limit),
    ]);
}


    // Afficher les détails d'une catégorie (produits associés)
    #[Route('/categorie/{id}', name: 'app_category_user_show', methods: ['GET'])]
    public function showu(Category $category): Response
    {
        // Vérification si la catégorie existe
        if (!$category) {
            throw $this->createNotFoundException('The category does not exist.');
        }

        return $this->render('navbar/category/user_show.html.twig', [
            'category' => $category,
        ]);
    }
}
