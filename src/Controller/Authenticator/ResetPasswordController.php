<?php

namespace App\Controller\Authenticator;

use App\Entity\User; // Importation de l'entité User, utilisée pour récupérer les utilisateurs dans la base de données.
use App\Form\ChangePasswordFormType; // Importation du formulaire pour changer le mot de passe.
use App\Form\ResetPasswordRequestFormType; // Importation du formulaire pour demander une réinitialisation de mot de passe.
use Doctrine\ORM\EntityManagerInterface; // Importation du gestionnaire d'entité pour manipuler la base de données.
use Symfony\Bridge\Twig\Mime\TemplatedEmail; // Importation pour envoyer des emails avec des templates Twig.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base pour les contrôleurs dans Symfony.
use Symfony\Component\HttpFoundation\RedirectResponse; // Importation de la classe RedirectResponse pour les redirections.
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request pour gérer les requêtes HTTP.
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response pour générer une réponse HTTP.
use Symfony\Component\Mailer\MailerInterface; // Interface pour envoyer des emails via Symfony Mailer.
use Symfony\Component\Mime\Address; // Importation de la classe Address pour définir l'adresse de l'email.
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Interface pour hacher le mot de passe utilisateur.
use Symfony\Component\Routing\Attribute\Route; // Utilisation de l'annotation Route pour définir les routes.
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait; // Utilisation du trait pour la gestion de la réinitialisation de mot de passe.
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface; // Interface pour gérer les exceptions de réinitialisation de mot de passe.
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface; // Interface pour gérer la logique de réinitialisation de mot de passe.

#[Route('/reset-password')] // Définition de la route de base pour toutes les actions liées à la réinitialisation du mot de passe.
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait; // Utilisation du trait pour la gestion de la réinitialisation du mot de passe.

    // Injection des services nécessaires : ResetPasswordHelperInterface et EntityManagerInterface.
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Affiche et traite le formulaire pour demander la réinitialisation du mot de passe.
     */
    #[Route('/forgot_password_request', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        // Création du formulaire pour demander la réinitialisation du mot de passe.
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on traite la demande.
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $form->get('email')->getData();

            // Message de succès affiché à l'utilisateur.
            $this->addFlash('success', 'Si un compte existe avec cet email, un email de réinitialisation vous a été envoyé.');

            // On appelle la méthode pour envoyer l'email de réinitialisation.
            return $this->processSendingPasswordResetEmail($email, $mailer);
        }

        // Rendu de la vue avec le formulaire de demande de réinitialisation.
        return $this->render('authenticator/reset_password/request.html.twig', [
            'requestForm' => $form, // On passe le formulaire à la vue.
        ]);
    }

    /**
     * Page de confirmation après qu'un utilisateur a demandé la réinitialisation du mot de passe.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // On récupère le token de réinitialisation depuis la session.
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        // Rendu de la vue de confirmation avec le token.
        return $this->render('authenticator/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken, // On passe le token à la vue.
        ]);
    }

    /**
     * Valide et traite l'URL de réinitialisation que l'utilisateur a cliquée dans son email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        // Si un token est fourni dans l'URL, on le stocke dans la session et on redirige vers la même page.
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
        }

        // On récupère le token depuis la session.
        $token = $this->getTokenFromSession();

        // Si aucun token n'est trouvé, on lance une exception.
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        // On tente de valider le token et de récupérer l'utilisateur associé.
        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            // Si le token est invalide, on affiche un message d'erreur et on redirige vers la page de demande de réinitialisation.
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Création du formulaire pour changer le mot de passe.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on procède à la réinitialisation du mot de passe.
        if ($form->isSubmitted() && $form->isValid()) {
            // On supprime le token une fois utilisé.
            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // On hache le nouveau mot de passe et on l'assigne à l'utilisateur.
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush(); // Sauvegarde des modifications en base de données.

            // On nettoie la session après la réinitialisation du mot de passe.
            $this->cleanSessionAfterReset();

            // Message de succès après la réinitialisation.
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_home');
        }

        // Rendu de la vue avec le formulaire pour changer le mot de passe.
        return $this->render('authenticator/reset_password/reset.html.twig', [
            'resetForm' => $form, // On passe le formulaire à la vue.
        ]);
    }

    // Fonction qui envoie l'email de réinitialisation du mot de passe.
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        // Recherche de l'utilisateur par email.
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Si l'utilisateur n'existe pas, on ne révèle pas si le compte est trouvé, mais on affiche un message informatif.
        if (!$user) {
            $this->addFlash('info', 'Si un compte existe avec cet email, un email vous a été envoyé pour réinitialiser votre mot de passe.');
            return $this->redirectToRoute('app_check_email');
        }

        try {
            // Génération du token de réinitialisation.
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // Si une erreur survient lors de la génération du token, on affiche un message d'erreur.
            $this->addFlash('reset_password_error', 'Une erreur est survenue lors de la génération du token de réinitialisation.');

            return $this->redirectToRoute('app_check_email');
        }

        // Création et envoi de l'email contenant le token de réinitialisation.
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@exemple.com', 'Support DevWell'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('authenticator/reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken, // Le token est passé à la vue pour être utilisé dans l'email.
            ]);

        // Envoi de l'email.
        $mailer->send($email);

        // Stockage du token dans la session pour la suite du processus.
        $this->setTokenObjectInSession($resetToken);

        // Message de succès après l'envoi de l'email.
        $this->addFlash('success', 'Un email a été envoyé pour réinitialiser votre mot de passe.');

        return $this->redirectToRoute('app_check_email');
    }
}
