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
        // L'utilisation de findAll() récupère toutes les entrées de la table Category
        $categories = $entityManager->getRepository(Category::class)->findAll();

        // Renvoyer la vue 'user_index.html.twig' avec les catégories récupérées
        return $this->render('navbar/category/user_index.html.twig', [
            'categories' => $categories, // Passer les catégories à la vue
        ]);
    }

    // Afficher les détails d'une catégorie (produits associés)
    #[Route('/categorie/{id}', name: 'app_category_user_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        // Vérification si la catégorie existe, cette vérification est déjà gérée par Symfony
        // Grâce à l'auto-binding de l'argument Category, Symfony récupère la catégorie avec l'ID donné dans l'URL
        if (!$category) {
            // Si la catégorie n'est pas trouvée, lever une exception 404
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas.');
        }

        // Renvoyer la vue 'user_show.html.twig' avec les détails de la catégorie
        return $this->render('navbar/category/user_show.html.twig', [
            'category' => $category, // Passer la catégorie à la vue pour afficher ses détails
        ]);
    }
}
