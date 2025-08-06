<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use App\Factory\ProductFactory;
use App\Serializer\ProductDenormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Zenstruck\Foundry\Test\Factories;

class ProductTest extends ApiTestCase
{
    use Factories;

    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
    }

    public function testGetProducts(): void
    {
        ProductFactory::createMany(10);

        $response = static::createClient()->request('GET', '/api/products');

        $products = json_decode($response->getContent(), true)['member'] ?? [];

        $serializer = new Serializer(
            [new ArrayDenormalizer(), new ProductDenormalizer()],
        );

        $serializedProducts = $serializer->denormalize(
            $products,
            Product::class.'[]',
        );

        foreach ($serializedProducts as $product) {
            $this->assertNotEmpty($product->getId());
            $this->assertNotEmpty($product->getName());
            $this->assertNotEmpty($product->getPrice());
        }

        $this->assertGreaterThanOrEqual(10, $products);
    }
}
