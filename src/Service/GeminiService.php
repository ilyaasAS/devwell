<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService implements AiServiceInterface
{
    private HttpClientInterface $httpClient;

    private string $geminiApiKey;

    private ProductRepository $productRepository;

    private ReviewRepository $reviewRepository;

    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        string $geminiApiKey,
        ProductRepository $productRepository,
        ReviewRepository $reviewRepository,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->geminiApiKey = $geminiApiKey;
        $this->productRepository = $productRepository;
        $this->reviewRepository = $reviewRepository;
        $this->logger = $logger;
    }

    public function generateResponse(string $prompt, ?User $user = null): string
    {
        if ($this->geminiApiKey === '') {
            $this->logger->error('Gemini API key is missing from environment.');

            return 'Erreur API Gemini (voir logs)';
        }

        $systemInstruction = 'Tu es l\'assistant de vente officiel de ce site e-commerce. Ton rôle est d\'aider les clients à trouver des produits, de répondre aux questions techniques et de faciliter leurs achats. Ton ton est professionnel, accueillant et efficace. Si on te pose une question hors sujet (politique, personnel), redirige poliment la conversation vers le catalogue produit.';

        $ragContext = $this->buildRagContext($user);

        $fullPrompt = trim($systemInstruction . "\n\nContexte:\n" . $ragContext . "\n\nQuestion utilisateur:\n" . $prompt);

        // Utilisation du modèle 'lite' qui a survécu aux tests de quota
    $url = sprintf(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=%s',
        $this->geminiApiKey
    );

        

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $fullPrompt],
                    ],
                ],
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $payload,
        ]);

        $statusCode = $response->getStatusCode();
        $rawContent = $response->getContent(false);

        if ($statusCode !== 200) {
            $this->logger->error('Gemini API error', ['status' => $statusCode, 'response' => $rawContent]);
            return "Je suis un peu trop sollicité en ce moment. Laissez-moi respirer quelques secondes et reposez-moi votre question !";
        }

        $data = json_decode($rawContent, true);

        if (!is_array($data) || !isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $this->logger->error('Gemini API unexpected payload structure', [
                'response' => $rawContent,
            ]);

            return 'Erreur API Gemini (voir logs)';
        }

        return (string) $data['candidates'][0]['content']['parts'][0]['text'];
    }

    private function buildRagContext(?User $user = null): string
    {
        $lines = [];

        $products = $this->productRepository->findBy([], ['id' => 'DESC'], 10);
        foreach ($products as $product) {
            $name = (string) ($product->getName() ?? '');
            $price = (float) ($product->getPrice() ?? 0.0);
            $description = (string) ($product->getDescription() ?? '');

            $lines[] = sprintf(
                'Product: %s - Price: %0.2f€ - Description: %s',
                $name,
                $price,
                $description
            );
        }

        $reviews = $this->reviewRepository->findBy([], ['createdAt' => 'DESC'], 10);
        foreach ($reviews as $review) {
            $note = (int) ($review->getNote() ?? 0);
            $comment = (string) ($review->getComment() ?? '');

            $lines[] = sprintf(
                'Customer Review: %d/5 - %s',
                $note,
                $comment
            );
        }

        return implode("\n", $lines);
    }
}

