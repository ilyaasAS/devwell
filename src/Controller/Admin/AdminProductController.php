<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class AdminProductController extends AbstractController
{
    // Liste des produits pour l'administration
    #[Route('/admin/product', name: 'app_product_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les produits de la base de données
        $products = $entityManager->getRepository(Product::class)->findAll();

        // Rendu de la vue avec la liste des produits
        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // Créer un nouveau produit
    #[Route('/admin/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();  // Créer un objet de type Product
        $categories = $entityManager->getRepository(Category::class)->findAll();  // Récupérer toutes les catégories

        // Création du formulaire pour le produit
        $form = $this->createForm(ProductType::class, $product, [
            'categories' => $categories,
        ]);

        $form->handleRequest($request);  // Traitement de la soumission du formulaire

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la valeur du stock et la définir dans l'entité Product
            $stock = $form->get('stock')->getData();
            $product->setStock($stock);

            // Gestion de l'upload de l'image
            $uploadedFile = $form->get('uploadedImage')->getData();
            if ($uploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();  // Générer un nom unique pour le fichier
                $uploadedFile->move(
                    $this->getParameter('product_images_directory'),  // Déplacer le fichier vers le dossier cible
                    $newFilename
                );
                $product->setImage($newFilename);  // Affecter le nom de l'image au produit
            }

            // Sauvegarder le produit dans la base de données
            $entityManager->persist($product);
            $entityManager->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Le produit a été créé avec succès.');

            // Rediriger vers la liste des produits après la création
            return $this->redirectToRoute('app_product_index');
        }

        // Rendu du formulaire
        return $this->render('admin/product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Voir un produit spécifique (affichage pour l'admin)
    #[Route('/admin/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function productShow(Product $product): Response
    {
        // Rendu de la vue pour afficher un produit spécifique
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    // Modifier un produit existant
    #[Route('/admin/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les catégories pour le formulaire
        $categories = $entityManager->getRepository(Category::class)->findAll();

        // Créer et gérer le formulaire de modification du produit
        $form = $this->createForm(ProductType::class, $product, [
            'categories' => $categories,
        ]);

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la valeur du stock et la définir dans l'entité Product
            $stock = $form->get('stock')->getData();
            $product->setStock($stock);

            // Gestion de l'upload d'image
            $uploadedFile = $form->get('uploadedImage')->getData();
            if ($uploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();  // Générer un nouveau nom de fichier unique
                $uploadedFile->move(
                    $this->getParameter('product_images_directory'),  // Déplacer le fichier vers le dossier cible
                    $newFilename
                );
                $product->setImage($newFilename);  // Mettre à jour l'image du produit
            }

            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Le produit a été modifié avec succès.');

            // Rediriger vers la liste des produits
            return $this->redirectToRoute('app_product_index');
        }

        // Rendu de la vue pour modifier un produit
        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un produit
    #[Route('/admin/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            // Supprimer le produit de la base de données
            $entityManager->remove($product);
            $entityManager->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        // Rediriger vers la liste des produits
        return $this->redirectToRoute('app_product_index');
    }
}
