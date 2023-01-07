<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Exception\BadInputException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CustomExceptionNormalizer implements NormalizerInterface
{

    /**
     * @param FlattenException $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'content' => $object->getMessage(),
            'exception'=> [
                'message' => $object->getMessage(),
                'code' => $object->getStatusCode(),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof FlattenException;
    }
}
