<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Entity\CartProduct;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CartRepository;
use App\Repository\CartProductRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\CardSchemeValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;



class ApiCartController extends AbstractController
{
    # Ajouter un produit au panier. /api/carts/{productId} – AUTHED
    #[Route('/api/carts/{productId}', name: 'app_add_product_cart_json', methods: ['POST'])]
    public function addProductCart($productId, EntityManagerInterface $em, CartRepository $cartRepository): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($productId);
        if (!$product) {
            return new JsonResponse(['erreur' => "Product $productId not found"], 404);
        }
        $cartId = $cartRepository->find(2);
        $cartProduct = $em->getRepository(CartProduct::class)->findOneBy(['cart' => $cartId, 'product' => $product]);
        if ($cartProduct) {
            $cartProduct->setQuantity($cartProduct->getQuantity() + 1);
            $cartProduct->setTotalPrice($cartProduct->getTotalPrice() + $product->getPrice());
        } else {
            $cartProduct = new CartProduct();
            $cartProduct->setCart($cartId);
            $cartProduct->setProduct($product);
            $cartProduct->setQuantity(1);
            $cartProduct->setTotalPrice($product->getPrice());
            $em->persist($cartProduct);
        }
        $em->flush();
        return new JsonResponse(['message' => "The product $productId has been added to your cart."], 201, []);
    }

    # Retirer produit du panier. /api/carts/{productId} – AUTHED
    #[Route('/api/carts/{productId}', name: 'app_delete_product_cart_json', methods: ['DELETE'])]
    public function deleteProductCartId($productId, EntityManagerInterface $em, CartProductRepository $cartProductRepository): JsonResponse
    {
        $cartProducts = $cartProductRepository->findBy(['product' => $productId]);
        foreach ($cartProducts as $cartProduct) {
            $em->remove($cartProduct);
        }
        $em->flush();
        return new JsonResponse(['message' => 'Product removed from cart.']);
    }

    # Voir l'état du panier /api/carts/{productId} – AUTHED
    #[Route('/api/carts', name: 'app_state_cart_json', methods: ['GET'])]
    public function CartId(CartRepository $cartRepository): JsonResponse
    {
        $id = 2;
        $cart = $cartRepository->find($id);
        if (!$cart) {
            return new JsonResponse(['error' => "This Cart $id was not found"], 404);
        }

        $total = 0;

        $data = [
            'id' => $cart->getId(),
            # -> ce totalPrice doit être le total de tous les produits
            'totalPrice' => $total,
            'products' => []
        ];

        foreach ($cart->getCartProducts() as $cartProduct) {
            $product = $cartProduct->getProduct();
            $total += $product->getPrice() * $cartProduct->getQuantity();
            $data['products'][] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'quantity' => $cartProduct->getQuantity(),
                'price' => $product->getPrice(),
                'totalPrice' => $product->getPrice() * $cartProduct->getQuantity(),
            ];
        }

        $data['totalPrice'] = $total;

        $json = json_encode($data);
        return new JsonResponse($json, 200, [], true);
    }

    # Validation du panier (convertir le panier en commande) /api/carts/validate – AUTHED
    # ?



}
