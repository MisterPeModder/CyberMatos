<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiCartController extends AbstractController
{
    // Ajouter un produit au panier. /api/carts/{productId} – AUTHED
    #[Route('/api/carts/{productId}', name: 'app_add_product_cart_json', methods: ['POST'])]
    public function addProductCart($productId, EntityManagerInterface $em, CartRepository $cartRepository, #[CurrentUser] ?User $user): JsonResponse
    {
        if ($productId = 'validate') {
            return $this->validateCart($em, $cartRepository, $user);
        }

        $product = $em->getRepository(Product::class)->find($productId);
        if (!$product) {
            return new JsonResponse(['erreur' => "Product $productId not found"], 404);
        }

        $cart = $cartRepository->findOneBy([]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setTotalPrice(0);
            $cart->setUser($user);
            $em->persist($cart);
            $em->flush();
        }

        $cartProduct = $em->getRepository(CartProduct::class)->findOneBy(['cart' => $cart, 'product' => $product]);
        if ($cartProduct) {
            $cartProduct->setQuantity($cartProduct->getQuantity() + 1);
            $cartProduct->setTotalPrice($cartProduct->getTotalPrice() + $product->getPrice());
        } else {
            $cartProduct = new CartProduct();
            $cartProduct->setCart($cart);
            $cartProduct->setProduct($product);
            $cartProduct->setQuantity(1);
            $cartProduct->setTotalPrice($product->getPrice());
            $em->persist($cartProduct);
        }
        $em->flush();

        return new JsonResponse(['message' => "The product $productId has been added to your cart."], 201, []);
    }

    // Retirer produit du panier. /api/carts/{productId} – AUTHED
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

    // Voir l'état du panier /api/carts/{productId} – AUTHED
    #[Route('/api/carts', name: 'app_state_cart_json', methods: ['GET'])]
    public function CartId(CartRepository $cartRepository, #[CurrentUser] ?User $user): JsonResponse
    {
        $total = 0;

        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            return new JsonResponse(['error' => 'Cart was not found'], 404);
        }

        $data = [
            'id' => $cart->getId(),
            'totalPrice' => $total,
            'products' => [],
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

    // Validation du panier (convertir le panier en commande) /api/carts/validate – AUTHED
    private function validateCart(EntityManagerInterface $em, CartRepository $cartRepository, #[CurrentUser] ?User $user): JsonResponse
    {
        $total = 0;
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            return new JsonResponse(['error' => 'Cart was not found'], 404);
        }

        $order = new Order();
        $order->setApplicant($user);
        $order->setCreatedAt(new \DateTimeImmutable());
        $em->persist($order);
        $em->flush();

        foreach ($cart->getCartProducts() as $cartProduct) {
            $product = $cartProduct->getProduct();
            $product->addOrder($order);
            $order->addProduct($product);
        }

        $data = [
            'id' => $order->getId(),
            'totalPrice' => $total,
            'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'products' => [],
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

        $order->setTotalPrice($total);
        $em->persist($order);
        $em->flush();

        $jsonData = json_encode($data);

        return new JsonResponse($jsonData, 200, [], true);
    }
}
