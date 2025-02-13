<?php

// src/Controller/HomeController.php
namespace App\Controller\Navbar;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $cartService; // Déclaration de la variable pour stocker le service CartService

    // Injecter le service CartService via le constructeur
    public function __construct(CartService $cartService)
    {
        // Initialiser la variable $cartService avec le service passé en paramètre
        $this->cartService = $cartService;
    }

    // Route principale de la page d'accueil
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Utiliser le service CartService pour obtenir le nombre total d'articles dans le panier
        $totalItems = $this->cartService->getTotalItems();

        // Retourner la vue d'accueil avec le nombre total d'articles dans le panier
        return $this->render('navbar/home.html.twig', [
            'totalItems' => $totalItems,  // Passer la variable totalItems à la vue
        ]);
    }
}
