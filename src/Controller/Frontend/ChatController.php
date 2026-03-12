<?php

namespace App\Controller\Frontend;

use App\Service\AiServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChatController extends AbstractController
{
    private AiServiceInterface $aiService;

    public function __construct(AiServiceInterface $aiService)
    {
        $this->aiService = $aiService;
    }

    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $content = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($content['message']) || !is_string($content['message'])) {
            return new JsonResponse(
                ['error' => 'Le champ "message" est requis.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $user = $this->getUser();

        $responseText = $this->aiService->generateResponse(
            $content['message'],
            $user instanceof \App\Entity\User ? $user : null
        );

        return new JsonResponse([
            'response' => $responseText,
        ]);
    }
}

