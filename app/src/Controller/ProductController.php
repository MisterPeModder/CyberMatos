<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Order;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/list', name: 'app_product_list', methods: ['GET'])]
    public function list(ProductRepository $productRepository): Response
    {
        return $this->render('product/list.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTimeImmutable());
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/id/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_list', [], Response::HTTP_SEE_OTHER);
    }

    # En cours de construction
    #[Route('/{id}/add-to-cart2', name: 'app_add_to_cart2', methods: ['GET', 'POST'])]
    public function addToCart(Product $product, Order $order, ProductRepository $productRepository, Request $request, OrderRepository $orderRepository, EntityManagerInterface $manager, ManagerRegistry $doctrine /* UserInterface $user */): Response
    {
        $productRepository = $doctrine->getRepository(Product::class);

        /* récupérer le user */
        /*  $user = $this->getUser(); */
        $user = $order->getApplicant(1);

        // Chercher une commande existante avec l'ID spécifié
        $order = $orderRepository->findOneBy(['id' => $order, 'applicant' => $user]);

        if (!$order) {
            $order = new Order();
            $order->setCreatedAt(new \DateTimeImmutable());
            // set un l'id du candidat -> user connecté
            $order->setApplicant($user);
        }

        $product = $productRepository->find($product);
        $order->addProduct($product);
        $manager->persist($order);
        $manager->flush();
        $orderRepository->save($order, true);


        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
