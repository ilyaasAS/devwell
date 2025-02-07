<?php

namespace App\Controller\Footer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CGVController extends AbstractController
{
    #[Route('/cgv', name: 'app_cgv')]
    public function index(): Response
    {
        return $this->render('footer/cgv/index.html.twig', [
            'controller_name' => 'CGVController',
        ]);
    }
}
