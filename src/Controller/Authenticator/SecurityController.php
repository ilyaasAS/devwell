<?php

namespace App\Controller\Authenticator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe de base pour les contrôleurs dans Symfony.
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request pour récupérer les données de la requête HTTP.
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response pour renvoyer une réponse HTTP.
use Symfony\Component\Routing\Annotation\Route; // Importation de l'annotation Route pour définir les routes dans Symfony.
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; // Importation de la classe AuthenticationUtils pour gérer les erreurs d'authentification et récupérer l'email utilisé lors de la connexion.

class SecurityController extends AbstractController
{
    // Route pour afficher la page de connexion. Cette route est associée à l'URL /login.
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur d'authentification, s'il y en a une, par exemple en cas de mauvais identifiants.
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupère le dernier nom d'utilisateur (email) utilisé lors de la tentative de connexion.
        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérifie si la méthode de la requête est POST et s'il n'y a pas d'erreur. Cela signifie que l'utilisateur vient de se connecter avec succès.
        if ($request->isMethod('POST') && !$error) {
            // Si la connexion réussie, un message de succès est ajouté à la session.
            $this->addFlash('success', 'Connexion réussie !');
        }

        // Rendu de la vue de la page de connexion avec les variables last_username et error.
        return $this->render('authenticator/security/login.html.twig', [
            'last_username' => $lastUsername, // Affiche l'email utilisé lors de la dernière tentative de connexion.
            'error' => $error, // Affiche l'erreur d'authentification, si présente.
        ]);
    }

    // Route pour gérer la déconnexion. Cette route est associée à l'URL /logout.
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony gère automatiquement la déconnexion, donc cette méthode peut rester vide.
        // Il est important de noter que la déconnexion sera déclenchée automatiquement par Symfony grâce au système de sécurité.
    }
}
