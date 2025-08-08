<?php

namespace App\Controller;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\OrderItem;
use App\Serializer\OrderItemDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class OrderItemUpdateController extends AbstractController
{
    public function __invoke(
        Request $request,
        EntityManagerInterface $entityManager,
        OrderItem $orderItem,
        IriConverterInterface $iriConverter
    )
    {
        $denormalizer = new OrderItemDenormalizer($iriConverter);
        $denormalizer->denormalize(
            json_decode($request->getContent(), true),
            OrderItem::class,
            null,
            ['object_to_populate' => $orderItem]
        );

       $entityManager->flush();

       return new JsonResponse(json_encode($orderItem->getId()));
    }
}
