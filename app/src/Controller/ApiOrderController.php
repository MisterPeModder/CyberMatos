<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;


class ApiOrderController extends AbstractController
{
    # Récupérer toutes les commandes de l'utilisateur actuel /api/orders/ – AUTHED
    # A MODIFIER AVEC LE USER CONNECTE
    #[Route('/api/orders', name: 'app_get_order_json', methods: ['GET'])]
    public function orderList(SerializerInterface $serializer, OrderRepository $orderRepository): JsonResponse
    {
        $userOrders = $orderRepository->findBy(['applicant' => 1]);
        $json = $serializer->serialize($userOrders, 'json', ['groups' => 'order_list']);
        return new JsonResponse($json, 200, [], true);
    }

    # Récupérer une commande (/api/orders/{orderId}) – AUTHED
    #[Route('/api/orders/{id}', name: 'app_post_order_json', methods: ['GET'])]
    public function orderStore($id, SerializerInterface $serializer, OrderRepository $orderRepository): JsonResponse
    {
        $orderList = $orderRepository->find($id);
        $json = $serializer->serialize($orderList, 'json', ['groups' => 'order_list']);
        return new JsonResponse($json, 200, [], true);
    }
}
