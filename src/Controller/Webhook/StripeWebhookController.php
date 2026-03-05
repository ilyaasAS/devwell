<?php

namespace App\Controller\Webhook;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Webhook;

/**
 * Webhook Stripe : pas de formulaire Symfony, pas de validation CSRF.
 * Sécurité assurée par Stripe\Webhook::constructEvent (signature) uniquement.
 * Aucune vérification du Host/origine : Docker modifie l'origine des appels (stripe-cli → app).
 * Tous les logs utilisent php://stderr pour être visibles dans les logs Docker.
 */
class StripeWebhookController extends AbstractController
{
    #[Route('/webhook/stripe', name: 'webhook_stripe', methods: ['POST'])]
    public function stripe(Request $request, OrderRepository $orderRepository): Response
    {
        file_put_contents('php://stderr', "[StripeWebhook] Requête POST reçue\n");

        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';

        if ('' === $webhookSecret) {
            file_put_contents('php://stderr', "[StripeWebhook] ERREUR: Webhook secret non configuré\n");
            return new Response('Webhook secret not configured', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            file_put_contents('php://stderr', "[StripeWebhook] ERREUR: Invalid payload - " . $e->getMessage() . "\n");
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            file_put_contents('php://stderr', "[StripeWebhook] ERREUR: Invalid signature - " . $e->getMessage() . "\n");
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        // order_id : envoyé par CheckoutController via Charge::create(['metadata' => ['order_id' => (string) $order->getId()]]).
        // En achat réel (token + Charge), Stripe envoie charge.succeeded avec ces métadonnées → statut 'payée'.
        $orderId = null;
        if ('charge.succeeded' === $event->type) {
            $object = $event->data->object;
            $orderId = $object->metadata->order_id ?? null;
            file_put_contents('php://stderr', '[StripeWebhook] charge.succeeded reçu, order_id=' . ($orderId ?? 'null') . "\n");
        }
        if ('payment_intent.succeeded' === $event->type) {
            $object = $event->data->object;
            $orderId = $object->metadata->order_id ?? null;
            file_put_contents('php://stderr', '[StripeWebhook] payment_intent.succeeded reçu, order_id=' . ($orderId ?? 'null') . "\n");
        }

        if ('charge.succeeded' !== $event->type && 'payment_intent.succeeded' !== $event->type) {
            return new Response('', Response::HTTP_OK);
        }

        if (null === $orderId || '' === $orderId) {
            file_put_contents('php://stderr', '[StripeWebhook] metadata order_id absent ou vide, event.type=' . $event->type . "\n");
            return new Response('', Response::HTTP_OK);
        }

        $orderIdInt = (int) $orderId;
        file_put_contents('php://stderr', '[StripeWebhook] recherche commande id=' . $orderIdInt . "\n");
        $order = $orderRepository->find($orderIdInt);
        if (!$order) {
            file_put_contents('php://stderr', '[StripeWebhook] commande introuvable en base, id=' . $orderIdInt . "\n");
            return new Response('', Response::HTTP_OK);
        }

        $order->setStatus('payée');
        $orderRepository->save($order, true);
        file_put_contents('php://stderr', '[StripeWebhook] commande #' . $orderIdInt . ' passée à payée' . "\n");

        return new Response('', Response::HTTP_OK);
    }
}
