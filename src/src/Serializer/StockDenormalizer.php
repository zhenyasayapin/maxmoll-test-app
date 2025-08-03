<?php

namespace App\Serializer;

use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\Warehouse;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class StockDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()]);

        $stock = new Stock();

        $stock->setId($data['id']);

        if (isset($data['product'])) {
            $stock->setProduct($serializer->denormalize(
                $data['product'],
                Product::class
            ));
        }

        $stock->setWarehouse($serializer->denormalize(
            $data['warehouse'],
            Warehouse::class
        ));
        $stock->setStock($data['stock']);

        return $stock;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Stock::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Stock::class => true,
            'object' => false,
            '*' => false,
        ];
    }

}
