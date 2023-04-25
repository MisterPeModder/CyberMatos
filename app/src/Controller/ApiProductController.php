<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiProductController extends AbstractController
{
    // Récupérer la liste des produits /api/products
    #[Route('/api/products', name: 'app_products_json', methods: ['GET'])]
    public function productList(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $jsonData = [];
        foreach ($products as $product) {
            $jsonData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice(),
            ];
        }

        return $this->json($jsonData);
    }

    // Récupérer un produit /api/products/{productId}
    #[Route('/api/products/{id}', name: 'app_product_json', methods: ['GET'])]
    public function productId(Product $product): JsonResponse
    {
        $jsonData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'photo' => $product->getPhoto(),
            'price' => $product->getPrice(),
        ];

        return $this->json($jsonData);
    }

    // Ajouter un produit api/produits – AUTHED
    #[Route('/api/products', name: 'app_add_product_json', methods: ['POST'])]
    public function addProductId(SerializerInterface $serializer, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $jsonRetrieved = $request->getContent();
        $newProduct = $serializer->deserialize($jsonRetrieved, Product::class, 'json');
        $newProduct->setCreatedAt(new \DateTimeImmutable());
        $em->persist($newProduct);
        $em->flush();

        return $this->json($newProduct, 201, [], ['groups' => 'product_light']);
    }

    // Modifier un produit /api/products/{productId} – AUTHED
    #[Route('/api/products/{id}', name: 'app_edit_product_json', methods: ['PUT'])]
    public function editProductId($id, ProductRepository $productRepository, SerializerInterface $serializer, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['error' => "The product $id was not found"], 404);
        }
        $jsonRetrieved = $request->getContent();
        $newProduct = $serializer->deserialize($jsonRetrieved, Product::class, 'json');
        $product->setName($newProduct->getName());
        $product->setDescription($newProduct->getDescription());
        $product->setPhoto($newProduct->getPhoto());
        $product->setPrice($newProduct->getPrice());
        $em->persist($product);
        $em->flush();

        return $this->json($product, 200, [], ['groups' => 'product_light']);
    }

    // Supprimer un produit /api/products/{productId} – AUTHED
    #[Route('/api/products/{id}', name: 'app_delete_product_json', methods: ['DELETE'])]
    public function deleteProductId($id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['error' => "The product $id was not found"], 404);
        }
        $productRepository->remove($product, true);

        return new JsonResponse(['message' => "The product n°$id has been deleted"]);
    }
}
