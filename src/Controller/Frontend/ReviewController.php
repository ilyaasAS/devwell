<?php

namespace App\Controller\Frontend;

use App\Document\Review; // Importation du document Review, qui représente l'avis d'un client dans la base de données.
use Doctrine\ODM\MongoDB\DocumentManager; // Importation du DocumentManager, utilisé pour gérer les documents MongoDB dans Doctrine ODM.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe de base pour les contrôleurs dans Symfony.
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request qui gère les requêtes HTTP entrantes.
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response qui gère la réponse HTTP.
use Symfony\Component\Routing\Attribute\Route; // Importation de l'annotation Route pour définir les routes dans Symfony.

class ReviewController extends AbstractController
{
    // Définition de la route pour accéder à la page des avis clients. La route accepte les méthodes GET et POST.
    #[Route('/reviews', name: 'app_reviews_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DocumentManager $dm): Response
    {
        // Vérifie si la requête est de type POST (soumission du formulaire d'avis)
        if ($request->isMethod('POST')) {
            // Récupération des données envoyées dans le formulaire (nom, email, note, commentaire)
            $name = $request->request->get('nom');
            $email = $request->request->get('email');
            $rating = (int)$request->request->get('note'); // Conversion de la note en entier.
            $comment = $request->request->get('commentaire');

            // Validation des champs : on vérifie si les champs obligatoires sont remplis et si la note est dans une plage valide (1 à 5).
            if (empty($name) || empty($email) || empty($comment) || $rating < 1 || $rating > 5) {
                // Si des champs sont vides ou si la note est invalide, on affiche un message d'erreur.
                $this->addFlash('error', 'Tous les champs sont obligatoires et la note doit être entre 1 et 5.');
            } else {
                // Si les données sont valides, on crée un nouvel objet Review.
                $review = new Review();
                $review->setName($name) // On assigne les valeurs récupérées du formulaire.
                    ->setEmail($email)
                    ->setNote($rating)
                    ->setComment($comment);

                // On persiste l'objet Review dans la base de données MongoDB via le DocumentManager.
                $dm->persist($review);
                // On effectue la sauvegarde réelle dans la base de données.
                $dm->flush();

                // On affiche un message de succès après l'enregistrement de l'avis.
                $this->addFlash('success', 'Votre avis a été enregistré avec succès.');
                // On redirige l'utilisateur vers la même page pour afficher les avis mis à jour.
                return $this->redirectToRoute('app_reviews_index');
            }
        }

        // Si la méthode est GET, on récupère tous les avis clients depuis la base de données.
        $reviews = $dm->getRepository(Review::class)->findAll();

        // On rend la vue de la page avec la liste des avis clients.
        return $this->render('frontend/reviews.html.twig', [
            'reviews' => $reviews, // On passe la liste des avis à la vue Twig.
        ]);
    }
}
