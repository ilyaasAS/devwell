<?php

namespace App\Controller\Navbar;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    // Afficher toutes les catégories pour l'utilisateur sans pagination
    #[Route('/categorie', name: 'app_category_user_index', methods: ['GET'])]
    public function indexu(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les catégories sans pagination
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('navbar/category/user_index.html.twig', [
            'categories' => $categories,
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

