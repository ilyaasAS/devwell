<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{
    // Affichage de la liste des catégories
    #[Route('/admin/categories', name: 'app_category_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les catégories de la base de données
        $categories = $entityManager->getRepository(Category::class)->findAll();

        // Rendre la vue avec la liste des catégories
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories, // Passer les catégories à la vue
        ]);
    }

    // Ajouter une nouvelle catégorie
    #[Route('/admin/categories/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function test(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de la catégorie
        $category = new Category();

        // Créer le formulaire de catégorie
        $form = $this->createForm(CategoryType::class, $category);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persister la nouvelle catégorie dans la base de données
            $entityManager->persist($category);
            $entityManager->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Catégorie créée avec succès !');

            // Rediriger vers la page des catégories
            return $this->redirectToRoute('app_category_index');
        }

        // Rendre la vue avec le formulaire de création
        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
    }

    // Afficher les détails d'une catégorie
    #[Route('/admin/categories/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        // Vérification pour s'assurer que la catégorie existe
        if (!$category) {
            throw $this->createNotFoundException("La catégorie n'existe pas.");
        }

        // Rendre la vue avec les détails de la catégorie
        return $this->render('admin/category/show.html.twig', [
            'category' => $category, // Passer les informations de la catégorie à la vue
        ]);
    }

    // Modifier une catégorie existante
    #[Route('/admin/categories/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire de modification de catégorie
        $form = $this->createForm(CategoryType::class, $category);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Mise à jour réussie de la catégorie !');

            // Rediriger vers la page des catégories
            return $this->redirectToRoute('app_category_index');
        }

        // Rendre la vue avec le formulaire de modification
        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
            'category' => $category, // Passer la catégorie existante à la vue
        ]);
    }

    // Supprimer une catégorie
    #[Route('/admin/categories/{id}/delete', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            // Supprimer la catégorie de la base de données
            $entityManager->remove($category);
            $entityManager->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Catégorie supprimée avec succès !');
        }

        // Rediriger vers la page des catégories
        return $this->redirectToRoute('app_category_index');
    }
}
