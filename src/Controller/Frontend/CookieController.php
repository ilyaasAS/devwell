<?php

// src/Controller/CookieController.php
namespace App\Controller\Frontend;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CookieController extends AbstractController
{
    // Route pour supprimer un cookie spécifique
    #[Route('/delete-cookie', name: 'delete_cookie', methods: ['GET'])]
    public function deleteCookie(): Response
    {
        // Création d'un cookie avec une valeur vide et une date d'expiration passée pour le supprimer
        $cookie = Cookie::create(
            'my_cookie',       // Nom du cookie
            '',                // Valeur vide
            time() - 3600      // Expiration passée (il sera supprimé)
        );

        // Création d'une réponse indiquant la suppression du cookie
        $response = new Response('Cookie has been deleted!');
        $response->headers->setCookie($cookie); // Ajout du cookie supprimé à l'en-tête de la réponse

        return $response;
    }

    // Route pour afficher la page dédiée à la suppression des cookies
    #[Route('/delete-my-cookies', name: 'delete_my_cookies', methods: ['GET'])]
    public function deleteMyCookiesPage(): Response
    {
        // Affiche une page expliquant la gestion et suppression des cookies
        return $this->render('privacy/delete_cookies.html.twig');
    }

    // Route pour afficher la politique de gestion des cookies
    #[Route('/cookie-policy', name: 'app_cookie_policy', methods: ['GET'])]
    public function cookiePolicy(): Response
    {
        // Affiche une page détaillant la politique des cookies du site
        return $this->render('privacy/cookie_policy.html.twig');
    }
}
