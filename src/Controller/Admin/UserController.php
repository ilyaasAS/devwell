<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\SecurityBundle\Security;

// Définition de la route principale pour ce contrôleur d'administration
#[Route('/admin/user')]
final class UserController extends AbstractController
{
    private HasherUserPasswordHasherInterface $passwordHasher;  // Service pour le hachage des mots de passe

    // Injection de dépendance pour le service de hachage
    public function __construct(HasherUserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;  // Initialisation du service de hachage
    }

    // Route pour la création d'un utilisateur
    #[Route('/create', name: 'app_user_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); // Crée un nouvel objet utilisateur
        $form = $this->createForm(User1Type::class, $user); // Crée un formulaire basé sur User1Type
        $form->handleRequest($request); // Gère la soumission du formulaire

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Hache le mot de passe avant de persister l'utilisateur
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword); // Affecte le mot de passe haché à l'utilisateur

            // Sauvegarde l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Affiche un message flash de succès
            $this->addFlash('success', 'Utilisateur créé avec succès !');

            // Redirige vers la liste des utilisateurs
            return $this->redirectToRoute('app_user_index');
        }

        // Affiche le formulaire pour la création d'un utilisateur
        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),  // Passe le formulaire à la vue
        ]);
    }

    // Route pour afficher la liste des utilisateurs
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(), // Récupère tous les utilisateurs de la base de données
        ]);
    }

    // Route pour afficher les détails d'un utilisateur
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,  // Passe l'utilisateur à la vue
        ]);
    }

    // Route pour éditer un utilisateur
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur connecté est un administrateur
        $isAdminEdit = $this->isGranted('ROLE_ADMIN');

        // Crée un formulaire pour éditer l'utilisateur en passant l'option 'is_admin_edit'
        $form = $this->createForm(User1Type::class, $user, [
            'is_admin_edit' => $isAdminEdit,
        ]);

        $form->handleRequest($request); // Gère la soumission du formulaire

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'admin modifie le mot de passe, on le hache avant de le sauvegarder
            if ($user->getPassword() && !$isAdminEdit) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword); // Affecte le mot de passe haché à l'utilisateur
            }

            // Sauvegarde les changements en base de données
            $entityManager->flush();

            // Affiche un message flash de succès
            $this->addFlash('success', 'Utilisateur modifié avec succès !');

            return $this->redirectToRoute('app_user_index'); // Redirige vers la liste des utilisateurs
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),  // Passe le formulaire à la vue
        ]);
    }

    // Route pour supprimer un utilisateur
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si le token CSRF est valide
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);  // Supprime l'utilisateur
            $entityManager->flush();  // Applique les changements en base de données

            // Affiche un message flash de succès
            $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        } else {
            // Affiche un message d'erreur en cas d'échec de la suppression
            $this->addFlash('error', 'Échec de la suppression de l\'utilisateur.');
        }

        return $this->redirectToRoute('app_user_index'); // Redirige vers la liste des utilisateurs
    }

    // Route pour supprimer le compte d'un utilisateur
    #[Route('/delete-account/{id}', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(
        EntityManagerInterface $entityManager,
        Request $request,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        Security $security,
        UserRepository $userRepository,
        int $id
    ): Response {
        $user = $security->getUser();  // Récupère l'utilisateur actuellement connecté

        // Vérifie si l'utilisateur est valide
        if (!$user instanceof User) {
            $this->addFlash('error', 'Utilisateur non valide.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si l'utilisateur est bien connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer votre compte.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie que l'utilisateur connecté est celui qui tente de supprimer son propre compte
        if ($user->getId() !== $id) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer le compte d\'un autre utilisateur.');
            return $this->redirectToRoute('app_profile');
        }

        $userToDelete = $userRepository->find($id);  // Récupère l'utilisateur à supprimer

        // Vérifie si l'utilisateur à supprimer existe
        if (!$userToDelete) {
            $this->addFlash('error', 'L\'utilisateur à supprimer n\'existe pas.');
            return $this->redirectToRoute('app_profile');
        }

        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_profile');
        }

        // Tente de supprimer l'utilisateur
        try {
            $entityManager->remove($userToDelete);
            $entityManager->flush();

            // Déconnexion après suppression
            $tokenStorage->setToken(null);  // Supprime le token d'authentification
            $session->invalidate();  // Invalide la session actuelle

            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');

            return $this->redirectToRoute('app_home');  // Redirige vers la page d'accueil
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de votre compte.');
            return $this->redirectToRoute('app_profile');
        }
    }
}
