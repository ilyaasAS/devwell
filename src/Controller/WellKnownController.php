<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Répond aux requêtes système (ex. Chrome DevTools) pour éviter 404 et pollution des logs.
 */
class WellKnownController extends AbstractController
{
    #[Route('/.well-known/appspecific/com.chrome.devtools.json', name: 'well_known_chrome_devtools', requirements: ['_format' => 'json'], methods: ['GET'])]
    public function chromeDevTools(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
