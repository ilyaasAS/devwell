<?php

namespace App\Security; // Déclaration de l'espace de noms pour le fichier LoginAuthenticator.

use Symfony\Component\HttpFoundation\RedirectResponse; // Importation de RedirectResponse pour effectuer des redirections HTTP.
use Symfony\Component\HttpFoundation\Request; // Importation de Request pour manipuler les requêtes HTTP entrantes.
use Symfony\Component\HttpFoundation\Response; // Importation de Response pour envoyer une réponse HTTP.
use Symfony\Component\Routing\Generator\UrlGeneratorInterface; // Importation de l'interface pour générer des URL.
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; // Importation de TokenInterface pour manipuler les tokens d'authentification.
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator; // Importation de la classe de base pour créer un authentificateur personnalisé.
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge; // Importation pour valider les tokens CSRF.
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge; // Importation pour ajouter une gestion du "remember me".
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge; // Importation pour obtenir l'identité de l'utilisateur.
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials; // Importation pour les informations d'identification (mot de passe).
use Symfony\Component\Security\Http\Authenticator\Passport\Passport; // Importation pour créer un "passport" qui contient toutes les informations nécessaires à l'authentification.
use Symfony\Component\Security\Http\SecurityRequestAttributes; // Importation pour manipuler les attributs de sécurité de la requête.
use Symfony\Component\Security\Http\Util\TargetPathTrait; // Importation pour gérer la redirection après une authentification réussie.
use App\Entity\User; // Importation de l'entité User pour interagir avec les utilisateurs.
use Doctrine\ORM\EntityManagerInterface; // Importation de l'interface pour interagir avec la base de données via Doctrine.
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Importation pour hasher les mots de passe et les valider.

class LoginAuthenticator extends AbstractLoginFormAuthenticator // Déclaration de la classe LoginAuthenticator, qui étend AbstractLoginFormAuthenticator pour créer un authentificateur personnalisé.
{
    use TargetPathTrait; // Utilisation du trait TargetPathTrait pour gérer les redirections.

    public const LOGIN_ROUTE = 'app_login'; // Définition de la route de connexion.

    private $urlGenerator; // Déclaration de la variable pour générer des URL.
    private $entityManager; // Déclaration de la variable pour interagir avec la base de données.
    private $passwordHasher; // Déclaration de la variable pour hasher et valider les mots de passe.

    // Constructeur qui initialise les services nécessaires pour l'authentification.
    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->urlGenerator = $urlGenerator; // Initialisation de l'URL Generator.
        $this->entityManager = $entityManager; // Initialisation de l'EntityManager.
        $this->passwordHasher = $passwordHasher; // Initialisation du hasher de mot de passe.
    }

    // La méthode authenticate est responsable de l'authentification de l'utilisateur.
    public function authenticate(Request $request): Passport
    {
        // Récupérer les informations de connexion envoyées via la requête HTTP.
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Récupérer l'utilisateur correspondant à l'email.
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('Utilisateur introuvable'); // Si l'utilisateur n'est pas trouvé, une exception est lancée.
        }

        // Vérification du mot de passe.
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new \Exception('Mot de passe incorrect'); // Si le mot de passe est invalide, une exception est lancée.
        }

        // Crée un passport (un objet qui contient les informations nécessaires à l'authentification).
        return new Passport(
            new UserBadge($email), // Le badge de l'utilisateur identifie l'utilisateur par son email.
            new PasswordCredentials($password), // Le badge des informations d'identification contient le mot de passe.
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')), // Validation du token CSRF pour protéger contre les attaques CSRF.
                new RememberMeBadge(), // Activation de la fonctionnalité "remember me" pour maintenir l'utilisateur connecté.
            ]
        );
    }

    // La méthode onAuthenticationSuccess est appelée après une authentification réussie.
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Si l'utilisateur venait d'une page spécifique avant d'être redirigé vers la page de connexion, il est renvoyé à cette page.
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath); // Redirection vers la page demandée.
        }

        // Sinon, l'utilisateur est redirigé vers la page d'accueil.
        return new RedirectResponse($this->urlGenerator->generate('app_home')); // Redirection vers la page d'accueil.
    }

    // La méthode getLoginUrl est utilisée pour obtenir l'URL de la page de connexion.
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE); // Génération de l'URL de la page de connexion.
    }
}
