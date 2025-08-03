<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [new GetCollection()],
    normalizationContext: ['groups' => ['product:read']],
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stock:read', 'product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['stock:read', 'product:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['stock:read', 'product:read'])]
    private ?float $price = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Stock::class)]
    #[Groups(['stock:read', 'product:read'])]
    private Collection $stocks;

    public function __construct()
    {
        $this->stocks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function setStocks(Collection $stocks): void
    {
        $this->stocks = $stocks;
    }
}
