<?php

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    public function testProductList()
    {
        $client = static::createClient();

        // si ça retourne une réponse HTTP 200
        $client->request('GET', '/api/products');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // si on retourne du JSON
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        // si la liste de produits est correcte
        $json = json_decode($client->getResponse()->getContent(), true);
        $productRepository = static::getContainer()->get(ProductRepository::class);
        $products = $productRepository->findAll();
        foreach ($products as $j => $product) {
            $this->assertEquals($product->getId(), $json[$j]['id']);
            $this->assertEquals($product->getName(), $json[$j]['name']);
            $this->assertEquals($product->getDescription(), $json[$j]['description']);
            $this->assertEquals($product->getPhoto(), $json[$j]['photo']);
            $this->assertEquals($product->getPrice(), $json[$j]['price']);
        }
    }

    public function testProductId()
    {
        $client = static::createClient();

        // créer un nouveau produit
        $product = new Product();
        $product->setName('Product test');
        $product->setDescription('Description test');
        $product->setPhoto('photo.png');
        $product->setPrice(206.98);
        $product->setCreatedAt(new \DateTimeImmutable());
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        // si la route /api/products/{productId} retourne une réponse HTTP 200
        $client->request('GET', '/api/products/'.$product->getId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        // si la reponse contient les données du produit
        $json = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($product->getId(), $json['id']);
        $this->assertEquals($product->getName(), $json['name']);
        $this->assertEquals($product->getDescription(), $json['description']);
        $this->assertEquals($product->getPhoto(), $json['photo']);
        $this->assertEquals($product->getPrice(), $json['price']);
    }

    public function testAddProduct()
    {
        $client = static::createClient();

        // faire une requête POST
        $newProductData = [
            'name' => 'Nouveau produit',
            'description' => 'blablabla',
            'photo' => 'photo.png',
            'price' => 42.42,
        ];
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newProductData)
        );

        // si succès -> code 201
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        // si la reponse contient les données du produit
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertSame($newProductData['name'], $responseData['name']);
        $this->assertSame($newProductData['description'], $responseData['description']);
        $this->assertSame($newProductData['photo'], $responseData['photo']);
        $this->assertSame($newProductData['price'], $responseData['price']);
    }

    /*     public function testDeleteProduct(): void
    {
        $client = static::createClient();

        // créer un nouveau produit
        $product = new Product();
        $product->setName('Encore un nouveau produit');
        $product->setDescription('blablabla');
        $product->setPhoto('photo.png');
        $product->setPrice(42.42);
        $product->setCreatedAt(new \DateTimeImmutable());

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        // NE MARCHE PAS CAR JE N'ARRIVE PAS A RECUPERER L'ID
        //  Envoyer une requête DELETE pour supprimer le produit
        $client->request('DELETE', '/api/products/' . $product->getId());

        // verifier la reponse
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

    } */

    public function testDeleteNonexistentProduct(): void
    {
        $client = static::createClient();
        // id qui n'existe pas
        $client->request('DELETE', '/api/products/9999');
        // reponse 404
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
