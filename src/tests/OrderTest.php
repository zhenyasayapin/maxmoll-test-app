<?php

namespace App\Tests;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use App\Factory\OrderFactory;
use App\Factory\WarehouseFactory;
use App\Serializer\OrderDenormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;
use Zenstruck\Foundry\Test\Factories;

class OrderTest extends ApiTestCase
{
    use Factories;

    public function setUp(): void
    {
        self::$alwaysBootKernel = false;
    }

    public function testGetOrders(): void
    {
        OrderFactory::createMany(10);

        $client = static::createClient();
        $response = $client->request('GET', '/api/orders');

        /** @var IriConverterInterface $iriConverter */
        $iriConverter = $client->getContainer()->get('api_platform.iri_converter');

        $serializer = new Serializer([
            new OrderDenormalizer($iriConverter),
            new ArrayDenormalizer(),
        ]);

        $orders = $serializer->denormalize(
            json_decode($response->getContent(), true)['member'] ?? [],
            Order::class . '[]',
        );

        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $orders);

        foreach ($orders as $order) {
            $this->assertNotEmpty($order->getId());
            $this->assertNotEmpty($order->getCustomer());
            $this->assertNotEmpty($order->getCreatedAt());
            $this->assertNotEmpty($order->getWarehouse());
            $this->assertNotEmpty($order->getStatus());
        }
    }

    public function testGetFilteredOrders(): void
    {
        OrderFactory::createMany(10, function () {
            return [
                'status' => OrderStatusEnum::ACTIVE->value,
            ];
        });

        OrderFactory::createMany(10, function () {
            return [
                'status' => OrderStatusEnum::CANCELLED->value,
            ];
        });

        $client = static::createClient();
        $response = $client->request('GET', '/api/orders?status=active');

        /** @var IriConverterInterface $iriConverter */
        $iriConverter = $client->getContainer()->get('api_platform.iri_converter');

        $serializer = new Serializer([
            new OrderDenormalizer($iriConverter),
            new ArrayDenormalizer(),
        ]);

        $orders = $serializer->denormalize(
            json_decode($response->getContent(), true)['member'] ?? [],
            Order::class . '[]',
        );

        $this->assertResponseIsSuccessful();

        foreach ($orders as $order) {
            $this->assertEquals(OrderStatusEnum::ACTIVE->value, $order->getStatus());
        }
    }

    public function testCreateOrder(): void
    {
        $warehouse = WarehouseFactory::createOne();

        $client = static::createClient();

        $response = $client->request('POST', '/api/orders', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'body' => json_encode([
                'customer' => 'John Doe',
                'warehouse' => '/api/warehouses/'. $warehouse->getId(),
                'status' => OrderStatusEnum::ACTIVE->value,
            ]),
        ]);

        /** @var IriConverterInterface $iriConverter */
        $iriConverter = $client->getContainer()->get('api_platform.iri_converter');
        $order = (new Serializer([new OrderDenormalizer($iriConverter)]))->denormalize(
            json_decode($response->getContent(), true),
            Order::class
        );

        $this->assertResponseIsSuccessful();
        $this->assertNotNull($order);
        $this->assertNotEmpty($order->getId());
        $this->assertNotEmpty($order->getCustomer());
        $this->assertNotEmpty($order->getWarehouse());
        $this->assertNotEmpty($order->getCreatedAt());
    }
}
