<?php

namespace App\Controller\Authenticator;

use App\Entity\User; // Importation de l'entité User pour interagir avec les utilisateurs dans la base de données.
use App\Form\UserType; // Importation du formulaire de type User, qui est utilisé pour l'inscription.
use Doctrine\ORM\EntityManagerInterface; // Interface pour manipuler les entités en base de données via Doctrine.
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request pour gérer les requêtes HTTP.
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response pour générer des réponses HTTP.
use Symfony\Component\Routing\Annotation\Route; // Annotation pour définir les routes.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base pour les contrôleurs dans Symfony.
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Interface pour hacher les mots de passe.
use Symfony\Component\Mailer\MailerInterface; // Interface pour envoyer des emails via Symfony Mailer.
use Symfony\Component\Mime\Email; // Importation de la classe Email pour envoyer des emails avec Symfony Mailer.

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')] // Route définissant l'URL pour l'inscription
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer // Injection du service MailerInterface pour l'envoi d'emails
    ): Response {
        // Création d'un nouvel objet User
        $user = new User();

        // Création du formulaire avec le type UserType, pour lier les données au modèle User
        $form = $this->createForm(UserType::class, $user);

        // Traitement de la requête pour le formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on procède à l'inscription
        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérification si un utilisateur avec le même email existe déjà dans la base de données
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            // 2. Si un utilisateur existe avec cet email, on affiche un message d'erreur
            if ($existingUser) {
                $this->addFlash('error', 'Un compte avec cet email existe déjà. Veuillez en choisir un autre.');

                // 3. Redirection vers la page d'inscription pour afficher le message d'erreur
                return $this->redirectToRoute('app_register'); // Redirige vers le formulaire d'inscription
            }

            // 4. Hashage du mot de passe avant de l'enregistrer
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword); // Définit le mot de passe hashé pour l'utilisateur

            // 5. Sauvegarde de l'utilisateur dans la base de données
            $entityManager->persist($user); // Prépare l'entité User pour l'enregistrement
            $entityManager->flush(); // Sauvegarde l'utilisateur dans la base de données

            // 6. Envoi d'un email de confirmation après l'inscription
            $email = (new Email())
                ->from('no-reply@yourdomain.com') // Adresse de l'expéditeur
                ->to($user->getEmail()) // L'email de l'utilisateur qui vient de s'inscrire
                ->subject('Bienvenue sur notre site !') // Sujet de l'email
                ->html($this->renderView('emails/registration_confirmation.html.twig', [
                    'user' => $user, // Passer l'utilisateur à la vue pour personnaliser l'email
                ]));

            // Envoi de l'email de confirmation
            $mailer->send($email);

            // 7. Message de succès après l'inscription
            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');

            // 8. Redirection vers la page de connexion ou d'accueil
            return $this->redirectToRoute('app_login');
        }

        // 9. Affichage du formulaire d'inscription si il n'est pas soumis ou si des erreurs se produisent
        return $this->render('authenticator/registration/index.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue pour affichage
        ]);
    }
}
