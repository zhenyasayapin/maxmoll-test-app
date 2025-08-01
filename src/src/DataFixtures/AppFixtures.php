<?php

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\Factory\WarehouseFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        WarehouseFactory::createMany(10);

        ProductFactory::createMany(10);

        $manager->flush();
    }
}
