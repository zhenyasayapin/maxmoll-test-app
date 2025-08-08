<?php

namespace App\Serializer;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\OrderItem;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class OrderItemDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly IriConverterInterface $iriConverter
    )
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $orderItem = $context['object_to_populate'] ?? new OrderItem();

        if (is_null($orderItem->getId())) {
            $orderItem->setId($data['id'] ?? null);
        }

        if (isset($data['product'])) {
            if (is_string($data['product'])) {
               $product = $this->iriConverter->getResourceFromIri($data['product']);
            }

            if (is_array($data['product'])) {
               $product = (new ProductDenormalizer())->denormalize($data['product'], Product::class);
            }
        }

        if (isset($product)) {
            $orderItem->setProduct($product);
        }

        $orderItem->setCount($data['count'] ?? 0);

        return $orderItem;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return OrderItem::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            OrderItem::class => true,
        ];
    }
}
