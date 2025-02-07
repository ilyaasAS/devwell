<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

// Admin
#[Route('/admin/user')]
final class UserController extends AbstractController
{
    private HasherUserPasswordHasherInterface $passwordHasher;  // Déclaration de la variable pour le service de hachage

    // Injection du service dans le constructeur
    public function __construct(HasherUserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;  // Initialisation du service de hachage
    }

    // Admin créer un user
    #[Route('/create', name: 'app_user_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); // Créer un nouvel utilisateur
        $form = $this->createForm(User1Type::class, $user); // Utiliser ton formulaire pour définir les rôles
        $form->handleRequest($request); // Traiter la soumission du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe avant de persister l'utilisateur
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());  // Utiliser hashPassword
            $user->setPassword($hashedPassword);  // Affectation du mot de passe haché

            // Sauvegarder l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger vers la page de liste des utilisateurs après la création
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    // Admin voir un user
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    // Admin edit un user
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le mot de passe est modifié, hacher le nouveau mot de passe
            if ($user->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());  // Utiliser hashPassword
                $user->setPassword($hashedPassword);  // Affectation du mot de passe haché
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // Admin supprimer un user
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


    
}
