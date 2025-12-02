<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ProductRepository;

class CartController extends AbstractController
{
    #[Route('/cart/add', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request, SessionInterface $session)
    {
        $productId = $request->request->get('product_id');
        $quantity = (int) $request->request->get('quantity', 1);

        $cart = $session->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_shopping_basket');
    }

    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Request $request, SessionInterface $session)
    {
        $productId = $request->request->get('product_id');
        $cart = $session->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_shopping_basket');
    }

    #[Route('/cart/increase/{id}', name: 'app_cart_increase')]
    public function increase(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]++;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_shopping_basket');
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_shopping_basket');
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart(int $id, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $quantity = 1;

        if (isset($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_shopping_basket');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');

        $this->addFlash('success', 'Votre panier a été vidé');
        return $this->redirectToRoute('app_shopping_basket');
    }
}
