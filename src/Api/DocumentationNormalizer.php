<?php
/**
 * Source:
 * https://timobakx.dev/php/api-platform/2021/06/06/altering-api-documentation-generated-by-apip.html
 */
declare(strict_types=1);

namespace App\Api;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DocumentationNormalizer implements NormalizerInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var array $docs */
        $docs = $this->decorated->normalize($object, $format, $context);

        $paths = [
            '/login' => [201, 422],
            '/doctor/{id}/favorite' => [201, 400, 422],
            '/doctor/{id}/appointment/{appointment_id}' => [200, 400, 422]
        ];

        foreach ($paths as $path => $statusCodes) {
            foreach ($statusCodes as $statusCode) {
                unset($docs['paths'][$path]['post']['responses'][$statusCode]);
                unset($docs['paths'][$path]['put']['responses'][$statusCode]);
            }
        }

        return $docs;
    }
}