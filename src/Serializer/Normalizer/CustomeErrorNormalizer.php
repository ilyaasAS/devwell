<?php

namespace App\Serializer\Normalizer; // Déclare l'espace de noms pour cette classe qui fait partie de la sérialisation personnalisée.

use Symfony\Component\DependencyInjection\Attribute\Autowire; // Importation d'Autowire pour une gestion automatique des services, non utilisé ici.
use Symfony\Component\ErrorHandler\Exception\FlattenException; // Importation de FlattenException, utilisé pour aplatir des exceptions afin de les sérialiser.
use Symfony\Component\Serializer\Normalizer\NormalizerInterface; // Importation de l'interface NormalizerInterface, nécessaire pour implémenter un normaliseur personnalisé.

class CustomeErrorNormalizer implements NormalizerInterface // Déclaration de la classe CustomeErrorNormalizer qui implémente NormalizerInterface.
{

    // La méthode normalize transforme l'objet d'exception en un tableau de données qui sera sérialisé en réponse.
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        // Construction du tableau de données à renvoyer avec un message d'erreur et les informations de l'exception.
        $data = [
            'content' => 'Ceci est une erreur', // Message générique d'erreur.
            'exception' => [
                'message' => $object->getMessage(), // Message de l'exception.
                'code' => $object->getStatusCode() // Code de statut de l'exception (par exemple, 404, 500, etc.).
            ]
        ];

        // Retourne le tableau des données de l'erreur.
        return $data;
    }

    // La méthode supportsNormalization vérifie si l'objet à normaliser est une instance de FlattenException.
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        // Retourne true si l'objet est une instance de FlattenException, sinon false.
        return $data instanceof FlattenException;
    }

    // La méthode getSupportedTypes indique les types d'objets que ce normaliseur peut gérer.
    public function getSupportedTypes(?string $format): array
    {
        // Retourne un tableau avec le type de classe supporté, ici FlattenException.
        return [
            FlattenException::class => true
        ];
    }
}
