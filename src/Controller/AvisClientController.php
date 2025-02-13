<?php

namespace App\Controller;

use App\Document\AvisClient; // Importation du document AvisClient, qui représente l'avis d'un client dans la base de données.
use Doctrine\ODM\MongoDB\DocumentManager; // Importation du DocumentManager, utilisé pour gérer les documents MongoDB dans Doctrine ODM.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe de base pour les contrôleurs dans Symfony.
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request qui gère les requêtes HTTP entrantes.
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response qui gère la réponse HTTP.
use Symfony\Component\Routing\Annotation\Route; // Importation de l'annotation Route pour définir les routes dans Symfony.

class AvisClientController extends AbstractController
{
    // Définition de la route pour accéder à la page des avis clients. La route accepte les méthodes GET et POST.
    #[Route('/avis-client', name: 'avis_client', methods: ['GET', 'POST'])]
    public function avisClient(Request $request, DocumentManager $dm): Response
    {
        // Vérifie si la requête est de type POST (soumission du formulaire d'avis)
        if ($request->isMethod('POST')) {
            // Récupération des données envoyées dans le formulaire (nom, email, note, commentaire)
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $note = (int)$request->request->get('note'); // Conversion de la note en entier.
            $commentaire = $request->request->get('commentaire');

            // Validation des champs : on vérifie si les champs obligatoires sont remplis et si la note est dans une plage valide (1 à 5).
            if (empty($nom) || empty($email) || empty($commentaire) || $note < 1 || $note > 5) {
                // Si des champs sont vides ou si la note est invalide, on affiche un message d'erreur.
                $this->addFlash('error', 'Tous les champs sont obligatoires et la note doit être entre 1 et 5.');
            } else {
                // Si les données sont valides, on crée un nouvel objet AvisClient.
                $avis = new AvisClient();
                $avis->setNom($nom) // On assigne les valeurs récupérées du formulaire.
                    ->setEmail($email)
                    ->setNote($note)
                    ->setCommentaire($commentaire);

                // On persiste l'objet AvisClient dans la base de données MongoDB via le DocumentManager.
                $dm->persist($avis);
                // On effectue la sauvegarde réelle dans la base de données.
                $dm->flush();

                // On affiche un message de succès après l'enregistrement de l'avis.
                $this->addFlash('success', 'Votre avis a été enregistré avec succès.');
                // On redirige l'utilisateur vers la même page pour afficher les avis mis à jour.
                return $this->redirectToRoute('avis_client');
            }
        }

        // Si la méthode est GET, on récupère tous les avis clients depuis la base de données.
        $avisClients = $dm->getRepository(AvisClient::class)->findAll();

        // On rend la vue de la page avec la liste des avis clients.
        return $this->render('pages/avis_client.html.twig', [
            'avisClients' => $avisClients, // On passe la liste des avis à la vue Twig.
        ]);
    }
}
