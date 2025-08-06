<?php

namespace App\Controller;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Serializer\OrderItemDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

final class OrderItemCreateController extends AbstractController
{
    public function __invoke(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
        int $orderId,
        IriConverterInterface $iriConverter
    )
    {
       $order = $orderRepository->find($orderId);

       $orderItem = (new Serializer([
           new OrderItemDenormalizer(
               $iriConverter
           )]))
           ->denormalize(
                json_decode($request->getContent(), true),
           OrderItem::class
       );

       $orderItem->setOrder($order);

       $entityManager->persist($orderItem);
       $entityManager->flush();

       return new JsonResponse(json_encode($order->getId()));
    }
}
