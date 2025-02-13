<?php

namespace App\Security;  // Déclaration du namespace qui indique que ce fichier appartient à la couche sécurité de l'application.

use Symfony\Component\HttpFoundation\RedirectResponse;  // Permet de rediriger l'utilisateur vers une autre page après l'authentification.
use Symfony\Component\HttpFoundation\Request;  // Permet d'obtenir les données de la requête HTTP (comme les informations de connexion).
use Symfony\Component\HttpFoundation\Response;  // Utilisé pour envoyer une réponse HTTP au client.
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;  // Permet de générer des URLs (comme la page de login ou la page d'accueil).
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;  // Interface pour manipuler les tokens d'authentification (représente l'utilisateur authentifié).
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;  // Classe de base pour un authentificateur basé sur un formulaire de login.
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;  // Badge pour valider les tokens CSRF (protection contre les attaques CSRF).
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;  // Badge pour gérer la fonctionnalité "Se souvenir de moi" (remember me).
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;  // Badge pour associer l'utilisateur à son email.
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;  // Badge pour gérer les informations d'identification de l'utilisateur (ici le mot de passe).
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;  // Représente toutes les informations nécessaires à l'authentification.
use Symfony\Component\Security\Http\SecurityRequestAttributes;  // Permet de manipuler les attributs de sécurité dans la requête.
use Symfony\Component\Security\Http\Util\TargetPathTrait;  // Permet de gérer la redirection de l'utilisateur après l'authentification.
use App\Entity\User;  // Entité qui représente un utilisateur dans la base de données.
use Doctrine\ORM\EntityManagerInterface;  // Permet d'interagir avec la base de données via Doctrine.
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;  // Permet de vérifier les mots de passe et de les hasher.
use Symfony\Component\Security\Core\Exception\AuthenticationException;  // Exception levée lorsqu'une authentification échoue.

class LoginAuthenticator extends AbstractLoginFormAuthenticator  // Déclaration de la classe LoginAuthenticator, qui étend AbstractLoginFormAuthenticator pour gérer l'authentification via un formulaire de login.
{
    use TargetPathTrait;  // Utilisation du trait TargetPathTrait pour gérer les redirections après l'authentification.

    public const LOGIN_ROUTE = 'app_login';  // Définition de la route pour accéder au formulaire de login.

    private $urlGenerator;  // Déclaration d'une propriété pour générer des URLs.
    private $entityManager;  // Déclaration d'une propriété pour interagir avec la base de données (via Doctrine).
    private $passwordHasher;  // Déclaration d'une propriété pour vérifier et hasher les mots de passe.

    // Constructeur de la classe, il initialise les services nécessaires à l'authentification.
    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->urlGenerator = $urlGenerator;  // Initialisation de l'URL generator.
        $this->entityManager = $entityManager;  // Initialisation de l'Entity Manager pour accéder à la base de données.
        $this->passwordHasher = $passwordHasher;  // Initialisation du password hasher pour vérifier et hasher les mots de passe.
    }

    // Cette méthode est appelée pour authentifier un utilisateur.
    public function authenticate(Request $request): Passport
    {
        // Récupérer les informations de l'utilisateur depuis la requête HTTP (email et mot de passe).
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Recherche de l'utilisateur dans la base de données avec l'email.
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Si l'utilisateur n'est pas trouvé, lancer une exception d'authentification.
        if (!$user) {
            throw new AuthenticationException('Utilisateur introuvable');
        }

        // Vérification de la validité du mot de passe de l'utilisateur.
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Mot de passe incorrect');
        }

        // Si l'utilisateur et le mot de passe sont valides, créer un "passport" (un objet contenant les informations nécessaires à l'authentification).
        return new Passport(
            new UserBadge($email),  // Créer un badge d'utilisateur à partir de l'email.
            new PasswordCredentials($password),  // Créer un badge de crédentiales avec le mot de passe.
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),  // Validation du token CSRF pour la sécurité.
                new RememberMeBadge(),  // Activer le "remember me" pour garder l'utilisateur connecté.
            ]
        );
    }

    // Cette méthode est appelée lorsqu'une authentification réussie a lieu.
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Vérifier si l'utilisateur était en train de visiter une page avant de se rendre sur la page de login.
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            // Si oui, rediriger l'utilisateur vers cette page.
            return new RedirectResponse($targetPath);
        }

        // Sinon, rediriger l'utilisateur vers la page d'accueil.
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    // Cette méthode retourne l'URL de la page de login.
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);  // Génère l'URL de la route de login définie plus haut.
    }
}
