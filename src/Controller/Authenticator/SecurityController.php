<?php

namespace App\Controller\Authenticator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur de connexion, s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupère le dernier email utilisé
        $lastUsername = $authenticationUtils->getLastUsername();

        // Si aucune erreur et que la méthode est POST, c'est que la connexion a réussi
        if ($request->isMethod('POST') && !$error) {
            $this->addFlash('success', 'Connexion réussie !');
        }

        return $this->render('authenticator/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }




    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony gère la déconnexion automatiquement
    }
}
