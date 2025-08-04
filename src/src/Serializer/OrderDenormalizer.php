<?php

namespace App\Serializer;

use App\Entity\Order;
use App\Entity\Warehouse;
use App\Enum\OrderStatusEnum;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class OrderDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $order = new Order();

        $order->setId($data['id'] ?? null);
        $order->setCustomer($data['customer'] ?? null);
        $order->setCreatedAt(new \DateTimeImmutable($data['createdAt'] ?? null));
        $order->setCompletedAt(new \DateTimeImmutable($data['completedAt'] ?? null));
        $order->setStatus($data['status'] ? OrderStatusEnum::tryFrom($data['status'])?->value : null);

        if (isset($data['warehouse'])) {
            $order->setWarehouse(
                (new ObjectNormalizer())->denormalize($data['warehouse'], Warehouse::class)
            );
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
