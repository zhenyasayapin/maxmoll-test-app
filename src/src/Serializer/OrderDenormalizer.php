<?php

namespace App\Serializer;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Order;
use App\Entity\Warehouse;
use App\Enum\OrderStatusEnum;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class OrderDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
    )
    {}

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $order = $context['object_to_populate'] ?? new Order();

        if (isset($data['id'])) {
            $order->setId($data['id']);
        }

        if (isset($data['customer'])) {
            $order->setCustomer($data['customer']);
        }

        if (isset($data['createdAt'])) {
            $order->setCreatedAt(new \DateTimeImmutable($data['createdAt']));
        }
        if (isset($data['completedAt'])) {
            $order->setCompletedAt(new \DateTimeImmutable($data['completedAt']));
        }

        if (isset($data['status'])) {
            $order->setStatus(OrderStatusEnum::tryFrom($data['status'])?->value);
        }

        if (isset($data['warehouse'])) {
            $warehouse = match (true) {
                is_string($data['warehouse']) => $this->iriConverter->getResourceFromIri($data['warehouse']),
                default => (new ObjectNormalizer())->denormalize($data['warehouse'], Warehouse::class)
            };

            $order->setWarehouse($warehouse);
        }

        return $order;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return Order::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Order::class => true
        ];
    }
}
