<?php

// tests/Controller/CookieControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CookieControllerTest extends WebTestCase
{
    // Test pour la suppression du cookie
    public function testDeleteCookie()
    {
        // Création d'une requête vers la route /delete-cookie
        $client = static::createClient();
        $client->request('GET', '/delete-cookie');

        // Vérifie que la réponse est correcte
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Cookie has been deleted!', $client->getResponse()->getContent());

        // Vérifie que le cookie "my_cookie" est bien supprimé
        $cookies = $client->getCookieJar();
        $cookie = $cookies->get('my_cookie');
        $this->assertNull($cookie, 'Le cookie "my_cookie" a bien été supprimé');
    }

    // Test pour la page de suppression des cookies
    public function testDeleteMyCookiesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/delete-my-cookies');

        // Vérifie que la page est bien affichée
        $this->assertResponseIsSuccessful();
        // Mise à jour du texte attendu dans l'élément h1
        $this->assertSelectorTextContains('h1', 'Gérer les cookies');
    }

    // Test pour la politique des cookies
    public function testPolitiqueCookies()
    {
        $client = static::createClient();
        $client->request('GET', '/politique-cookies');

        // Vérifie que la page est bien affichée
        $this->assertResponseIsSuccessful();
        // Mise à jour du texte attendu dans l'élément h1
        $this->assertSelectorTextContains('h1', '📜 Politique de Cookies');
    }
}
