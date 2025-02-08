<?php

namespace App\Controller\Authenticator;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer // Ajout du service MailerInterface
    ): Response {
        $user = new User();

        // Crée le formulaire avec la classe UserType
        $form = $this->createForm(UserType::class, $user);

        // Traite le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifie si l'email existe déjà dans la base de données
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            // 2. Si l'email existe déjà, ajoute un message d'erreur
            if ($existingUser) {
                $this->addFlash('error', 'Un compte avec cet email existe déjà. Veuillez en choisir un autre.');

                // 3. Redirige vers la page d'inscription pour afficher le message d'erreur
                return $this->redirectToRoute('app_register'); // Redirige vers le formulaire d'inscription
            }

            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Sauvegarde dans la base de données via l'EntityManager
            $entityManager->persist($user); // Prépare l'entité pour l'enregistrement
            $entityManager->flush(); // Exécute la requête SQL pour sauvegarder l'utilisateur

            // Envoi d'un e-mail de confirmation
            $email = (new Email())
                ->from('no-reply@yourdomain.com') // Adresse d'expéditeur
                ->to($user->getEmail()) // Adresse du nouvel utilisateur
                ->subject('Bienvenue sur notre site !')
                ->html($this->renderView('emails/registration_confirmation.html.twig', [
                    'user' => $user,
                ]));

            $mailer->send($email); // Envoie l'e-mail

            // Après avoir persisté l'utilisateur et envoyé l'email
            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');

            // Redirige vers la page de connexion ou d'accueil après l'inscription
            return $this->redirectToRoute('app_login');
        }

        // Affichage du formulaire d'inscription
        return $this->render('authenticator/registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
