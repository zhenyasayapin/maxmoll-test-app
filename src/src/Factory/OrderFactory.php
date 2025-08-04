<?php

namespace App\Factory;

use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Order>
 */
final class OrderFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Order::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'completedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'customer' => self::faker()->name(),
            'status' => OrderStatusEnum::cases()[self::faker()->numberBetween(0, count(OrderStatusEnum::cases()) - 1)]->value,
            'warehouse' => WarehouseFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Order $order): void {})
        ;
    }
}
