<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use App\Factory\OrderFactory;
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

        $response = static::createClient()->request('GET', '/api/orders');

        $serializer = new Serializer([
            new OrderDenormalizer(),
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

        $response = static::createClient()->request('GET', '/api/orders?status=active');

        $serializer = new Serializer([
            new OrderDenormalizer(),
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
}
