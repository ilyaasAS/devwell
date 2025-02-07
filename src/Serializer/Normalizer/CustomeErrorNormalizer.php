<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomeErrorNormalizer implements NormalizerInterface
{

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = [
            'content' => 'Ceci est une erreur',
            'exception' => [
                'message' => $object->getMessage(),
                'code' => $object->getStatusCode()
            ]
        ];

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            FlattenException::class => true
        ];
    }
}
