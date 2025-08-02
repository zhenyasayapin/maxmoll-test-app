<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Stock;
use App\Factory\ProductFactory;
use App\Factory\StockFactory;
use App\Factory\WarehouseFactory;
use App\Serializer\StockDenormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;
use Zenstruck\Foundry\Test\Factories;

class StockTest extends ApiTestCase
{
    use Factories;

    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
    }

    public function testGetStock(): void
    {
        ProductFactory::createMany(10);
        WarehouseFactory::createMany(10);

        StockFactory::createMany(10, function () {
            return [
                'product' => ProductFactory::random(),
                'warehouse' => WarehouseFactory::random(),
            ];
        });

        $response = static::createClient()->request('GET', '/api/stocks');

        $stocks = json_decode($response->getContent(), true)['member'] ?? [];

        $serializer = new Serializer([
                new StockDenormalizer(),
                new ArrayDenormalizer()
        ]);

        $serializedStock = $serializer->denormalize(
            $stocks,
            Stock::class . '[]',
        );

        foreach ($serializedStock as $stock) {
            $this->assertNotEmpty($stock->getId());
            $this->assertNotEmpty($stock->getProduct());
            $this->assertNotEmpty($stock->getWarehouse());
            $this->assertNotEmpty($stock->getStock());
        }

        $this->assertCount(10, $serializedStock);
    }
}
