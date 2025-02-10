<?php

namespace App\Controller;

use App\Document\AvisClient;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisClientController extends AbstractController
{
    #[Route('/avis-client', name: 'avis_client', methods: ['GET', 'POST'])]
    public function avisClient(Request $request, DocumentManager $dm): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $note = (int)$request->request->get('note');
            $commentaire = $request->request->get('commentaire');

            if (empty($nom) || empty($email) || empty($commentaire) || $note < 1 || $note > 5) {
                $this->addFlash('error', 'Tous les champs sont obligatoires et la note doit être entre 1 et 5.');
            } else {
                $avis = new AvisClient();
                $avis->setNom($nom)
                    ->setEmail($email)
                    ->setNote($note)
                    ->setCommentaire($commentaire);

                $dm->persist($avis);
                $dm->flush();

                $this->addFlash('success', 'Votre avis a été enregistré avec succès.');
                return $this->redirectToRoute('avis_client');
            }
        }

        $avisClients = $dm->getRepository(AvisClient::class)->findAll();

        return $this->render('pages/avis_client.html.twig', [
            'avisClients' => $avisClients,
        ]);
    }
}
