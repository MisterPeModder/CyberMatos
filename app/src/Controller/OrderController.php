<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    // liste de toutes les commandes
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    // delete une commande
    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

    # editer une commande
    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->save($order, true);

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    # voir mon panier de session
    #[Route('/panier', name: 'app_order_show', methods: ['GET'])]
    public function show(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);

        $panierDatas = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $panierDatas[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        foreach ($panierDatas as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->render('order/show.html.twig', [
            'items' => $panierDatas,
            'total' => $total,
        ]);
    }

    #[Route('/{id}/add-to-cart', name: 'app_add_to_cart', methods: ['GET', 'POST'])]
    public function addPanier($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            ++$panier[$id];
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_order_show');
        /*  dd($session->get('panier')); */
    }

    #[Route('/{id}/remove-to-cart', name: 'app_remove_to_cart', methods: ['GET', 'POST'])]
    public function removePanier($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_order_show');
    }
}

/*     #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
public function new(Request $request, OrderRepository $orderRepository): Response
{
    $order = new Order();
    $form = $this->createForm(OrderType::class, $order);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $orderRepository->save($order, true);

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('order/new.html.twig', [
        'order' => $order,
        'form' => $form,
    ]);
} */
