<?php

namespace App\Controller;

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

    return $this->render('category/user_index.html.twig', [
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

        return $this->render('category/user_show.html.twig', [
            'category' => $category,
        ]);
    }


    // Liste des catégories
    #[Route('/categories', name: 'app_category_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les catégories
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    // Ajouter une nouvelle catégorie
    #[Route('/categories/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function test(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category successfully created!');
            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // Afficher les détails d'une catégorie (facultatif)
    #[Route('/categories/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        // Vérification ajoutée pour éviter des erreurs si la catégorie n'existe pas
        if (!$category) {
            throw $this->createNotFoundException('The category does not exist.');
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }




    #[Route('/categories/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Category successfully updated!');
            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }


    #[Route('/categories/{id}/delete', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category successfully deleted!');
        }

        return $this->redirectToRoute('app_category_index');
    }
}
