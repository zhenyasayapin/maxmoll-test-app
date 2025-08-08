<?php

namespace App\DataFixtures;

use App\Factory\OrderFactory;
use App\Factory\OrderItemFactory;
use App\Factory\ProductFactory;
use App\Factory\StockFactory;
use App\Factory\WarehouseFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        WarehouseFactory::createMany(10);
        ProductFactory::createMany(10);

        StockFactory::createMany(10, function () {
            return [
                'product' => ProductFactory::random(),
                'warehouse' => WarehouseFactory::random(),
            ];
        });

        OrderFactory::createMany(100, function () {
            return [
                'warehouse' => WarehouseFactory::random(),
            ];
        });

        OrderItemFactory::createMany(30, function () {
            return [
                'order' => OrderFactory::random(),
                'product' => ProductFactory::random(),
            ];
        });

        $manager->flush();
    }
}
