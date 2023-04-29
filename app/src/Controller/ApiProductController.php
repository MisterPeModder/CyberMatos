<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function productId($id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['error' => "The product $id was not found"], 404);
        }

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
    public function addProductId(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonRetrieved = $request->getContent();

        try {
            $newProduct = $serializer->deserialize($jsonRetrieved, Product::class, 'json');
            $newProduct->setCreatedAt(new \DateTimeImmutable());

            $violations = $validator->validate($newProduct);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $property = $violation->getPropertyPath();
                    $message = $violation->getMessage();
                    $errors[$property] = $message;
                }

                return $this->json(['error' => $errors], 400);
            }
            $em->persist($newProduct);
            $em->flush();

            return $this->json($newProduct, 201, [], ['groups' => 'product_light']);
        } catch (NotEncodableValueException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    // Modifier un produit /api/products/{productId} – AUTHED
    #[Route('/api/products/{id}', name: 'app_edit_product_json', methods: ['PUT'])]
    public function editProductId($id, ProductRepository $productRepository, SerializerInterface $serializer, EntityManagerInterface $em, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['error' => "The product $id was not found"], 404);
        }
        $jsonRetrieved = $request->getContent();

        try {
            $newProduct = $serializer->deserialize($jsonRetrieved, Product::class, 'json');
            $product->setName($newProduct->getName());
            $product->setDescription($newProduct->getDescription());
            $product->setPhoto($newProduct->getPhoto());
            $product->setPrice($newProduct->getPrice());

            $violations = $validator->validate($newProduct);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $property = $violation->getPropertyPath();
                    $message = $violation->getMessage();
                    $errors[$property] = $message;
                }

                return $this->json(['error' => $errors], 400);
            }

            $em->persist($product);
            $em->flush();

            return $this->json($product, 200, [], ['groups' => 'product_light']);
        } catch (NotEncodableValueException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
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

        return new JsonResponse(['message' => "The product n°$id has been deleted", 200]);
    }
}
