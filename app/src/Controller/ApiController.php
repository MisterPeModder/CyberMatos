<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/products', name: 'app_products_json', methods: ['GET'])]
    public function productList(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $jsonData = [];
        foreach ($products as $product) {
            $jsonData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getImage(),
                'price' => $product->getPrice()
            ];
        }
        return $this->json($jsonData);
    }

    #[Route('/products/{id}', name: 'app_product_json', methods: ['GET'])]
    public function productId(Product $product): Response
    {
        $jsonData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'photo' => $product->getImage(),
            'price' => $product->getPrice()
        ];
        return $this->json($jsonData);
    }
}
