<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HealthCheckController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private DocumentManager $documentManager;

    public function __construct(EntityManagerInterface $entityManager, DocumentManager $documentManager)
    {
        $this->entityManager = $entityManager;
        $this->documentManager = $documentManager;
    }

    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function health(Request $request): JsonResponse
    {
        $expectedToken = $_ENV['HEALTH_CHECK_TOKEN'] ?? null;
        $providedToken = $request->headers->get('X-HEALTH-TOKEN');

        if (!$expectedToken || $providedToken !== $expectedToken) {
            return new JsonResponse([], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $this->entityManager->getConnection()->executeQuery('SELECT 1')->fetchOne();
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'status' => 'error',
                'service' => 'mariadb',
                'message' => 'MariaDB is not reachable',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $client = $this->documentManager->getClient();
            $client->selectDatabase($this->documentManager->getConfiguration()->getDefaultDB())
                ->command(['ping' => 1]);
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'status' => 'error',
                'service' => 'mongodb',
                'message' => 'MongoDB is not reachable',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'status' => 'ok',
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}

