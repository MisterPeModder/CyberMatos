<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Product;
use App\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;


class FrontController extends AbstractController

{

    #[Route('/front', name: 'app_front')]
    public function index(SerializerInterface $serializer): Response
    {

        $kernel = new Kernel('prod', false);
        $request = Request::create('/api/products', 'GET');
        $response = $kernel->handle($request, HttpKernelInterface::MAIN_REQUEST, false);
        $liste_products = $serializer->deserialize($response->getContent(), 'App\Entity\Product[]', 'json');
        /*   dd($liste_products); */
        return $this->render('product/list.html.twig', [
            'products' => $liste_products
        ]);
    }
}
