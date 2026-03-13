<?php

namespace App\Controller\Api;

use App\Service\AiServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChatApiController extends AbstractController
{
    private AiServiceInterface $aiService;

    private LoggerInterface $logger;

    public function __construct(AiServiceInterface $aiService, LoggerInterface $logger)
    {
        $this->aiService = $aiService;
        $this->logger = $logger;
    }

    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        try {
            $content = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->logger->error('ChatApiController JSON decode failed', [
                'exception' => $exception->getMessage(),
            ]);

            return new JsonResponse(
                ['error' => 'La requête envoyée est invalide. Merci de rafraîchir la page et de réessayer.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if (!isset($content['message']) || !is_string($content['message'])) {
            return new JsonResponse(
                ['error' => 'Le champ "message" est requis.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $message = $content['message'];

        if (mb_strlen($message) > 1500) {
            return new JsonResponse(
                ['error' => 'Message trop long pour l\'assistant (max 1500 caractères)'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $user = $this->getUser();

        try {
            $responseText = $this->aiService->generateResponse(
                $message,
                $user instanceof \App\Entity\User ? $user : null
            );

            return new JsonResponse([
                'response' => $responseText,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('ChatApiController chat action failed', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new JsonResponse([
                'response' => 'Notre assistant rencontre un problème inattendu. Merci de réessayer dans quelques instants.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

