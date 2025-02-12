<?php

// src/Controller/CookieController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CookieController extends AbstractController
{
    // Route pour supprimer le cookie (déjà existant)
    #[Route('/delete-cookie', name: 'delete_cookie', methods: ['GET'])]
    public function deleteCookie()
    {
        $cookie = Cookie::create(
            'my_cookie',       // Nom du cookie
            '',                // Valeur vide
            time() - 3600      // Expiration passée pour supprimer
        );

        $response = new Response('Cookie has been deleted!');
        $response->headers->setCookie($cookie);

        return $response;
    }

    // Route pour afficher la page de suppression des cookies
    #[Route('/delete-my-cookies', name: 'delete_my_cookies', methods: ['GET'])]
    public function deleteMyCookiesPage()
    {
        return $this->render('cookie/delete_cookies.html.twig');
    }

    // Route pour afficher la politique de cookies
#[Route('/politique-cookies', name: 'politique_cookies', methods: ['GET'])]
public function politiqueCookies()
{
    return $this->render('cookie/politique_cookies.html.twig');
}

}
