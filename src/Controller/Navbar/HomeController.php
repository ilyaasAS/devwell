<?php

// src/Controller/HomeController.php

namespace App\Controller\Navbar;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $cartService;

    // Injecter le service CartService via le constructeur
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Utiliser le service pour obtenir le nombre total d'articles dans le panier
        $totalItems = $this->cartService->getTotalItems();

        return $this->render('navbar/home/index.html.twig', [
            'totalItems' => $totalItems,  // Passer le total d'articles à la vue
        ]);
    }
}
