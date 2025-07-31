<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Warehouse;
use App\Factory\WarehouseFactory;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Zenstruck\Foundry\Test\Factories;

class WarehouseTest extends ApiTestCase
{
    use Factories;

    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
    }

    public function testGetWarehouses(): void
    {
        WarehouseFactory::createMany(10);

        $response = static::createClient()->request('GET', '/api/warehouses');


        $serializer = new Serializer(
            [new ArrayDenormalizer(), new ObjectNormalizer()],
        );

        $warhouses = $serializer->denormalize(
            json_decode($response->getContent(), true)['member'] ?? [],
            Warehouse::class.'[]',
        );

        foreach ($warhouses as $warehouse) {
            $this->assertNotEmpty($warehouse->getId());
            $this->assertNotEmpty($warehouse->getName());
        }
    }
}
