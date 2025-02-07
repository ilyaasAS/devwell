<?php

namespace App\Controller\Footer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MentionLegaleController extends AbstractController
{
    #[Route('footer//mention_legale', name: 'app_mention_legale')]
    public function index(): Response
    {
        return $this->render('footer/mention_legale/index.html.twig', [
            'controller_name' => 'MentionLegaleController',
        ]);
    }
}
