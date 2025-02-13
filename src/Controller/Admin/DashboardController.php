<?php

// src/Controller/DashboardController.php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DashboardController extends AbstractController
{
    // Route pour accéder au dashboard de l'administration
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index()
    {
        // Vérifier si l'utilisateur a le rôle 'ROLE_ADMIN'
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Si l'utilisateur n'a pas ce rôle, une exception est lancée
            throw new AccessDeniedException('Accès refusé');
        }

        // Si l'utilisateur est admin, afficher la page du dashboard
        return $this->render('admin/dashboard/dashboard.html.twig');
    }
}
