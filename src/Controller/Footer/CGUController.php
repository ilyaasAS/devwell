<?php

namespace App\Controller\Footer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CGUController extends AbstractController
{
    #[Route('/cgu', name: 'app_cgu')]
    public function index(): Response
    {
        return $this->render('footer/cgu/index.html.twig', [
            'controller_name' => 'CGUController',
        ]);
    }
}
