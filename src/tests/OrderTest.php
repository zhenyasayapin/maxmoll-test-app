<?php

namespace App\Tests;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use App\Factory\OrderFactory;
use App\Factory\ProductFactory;
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

    public function testAddItemToOrder(): void
    {
        $order = OrderFactory::createOne();
        $product = ProductFactory::createOne();

        $client = static::createClient();

        $response = $client->request('POST', '/api/orders/' . $order->getId() . '/items', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'body' => json_encode([
                'product' => '/api/products/' . $product->getId(),
                'count' => 2,
            ]),
        ]);

        $this->assertResponseIsSuccessful();

        $repository = $client->getContainer()->get('doctrine')->getRepository(Order::class);
        $updatedOrder = $repository->find($order->getId());
        $this->assertNotEmpty($updatedOrder);
        $this->assertCount(1, $updatedOrder->getItems());
        $this->assertEquals($product->getId(), $updatedOrder->getItems()[0]->getProduct()->getId());
        $this->assertEquals(2, $updatedOrder->getItems()[0]->getCount());
    }

    public function testUpdateOrderStatus(): void
    {
        $order = OrderFactory::createOne(['status' => OrderStatusEnum::ACTIVE->value]);

        $client = static::createClient();

        $response = $client->request('PATCH', '/api/orders/' . $order->getId(), [
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
            'body' => json_encode([
                'status' => OrderStatusEnum::CANCELLED->value,
            ]),
        ]);

        $this->assertResponseIsSuccessful();

        $repository = $client->getContainer()->get('doctrine')->getRepository(Order::class);
        $updatedOrder = $repository->find($order->getId());
        $this->assertNotEmpty($updatedOrder);
        $this->assertEquals(OrderStatusEnum::CANCELLED->value, $updatedOrder->getStatus());
    }
}
