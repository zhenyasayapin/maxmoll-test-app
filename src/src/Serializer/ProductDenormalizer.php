<?php

namespace App\Serializer;

use App\Entity\Product;
use App\Entity\Stock;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class ProductDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $product = new Product();

        $product->setId($data['id']);
        $product->setName($data['name']);
        $product->setPrice($data['price']);

        $stockSerializer = new Serializer([new StockDenormalizer(), new ArrayDenormalizer()]);
        $data['stocks'] = $stockSerializer->denormalize($data['stocks'], Stock::class.'[]');

        $product->setStocks(new ArrayCollection($data['stocks']));

        return $product;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Product::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Product::class => true,
        ];
    }
}
