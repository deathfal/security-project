<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Address;
use App\Entity\User;

class ShoppingBasketController extends AbstractController
{
    #[Route('/shopping-basket', name: 'app_shopping_basket')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
            }
        }

        return $this->render('shopping/basket.html.twig', [
            'items' => $cartItems,
            'total' => $total,
        ]);
    }



    #[Route('/checkout', name: 'checkout')]
    public function checkout(SessionInterface $session, ProductRepository $productRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $address = $user->getAddress() ?: new Address();
        $cart = $session->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $cartItems[] = [
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'quantity' => $quantity,
                    'image' => $product->getImageUrl() ?: 'img/products/default_product.png'
                ];
                $subtotal += $product->getPrice() * $quantity;
            }
        }

        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + 20 + $tax; // Subtotal + Shipping + Tax

        return $this->render('shopping/checkout.html.twig', [
            'user' => $user,
            'address' => $address,
            'cart' => $cartItems,
            'subtotal' => number_format($subtotal, 2),
            'tax' => number_format($tax, 2),
            'total' => number_format($total, 2)
        ]);
    }

    #[Route('/confirm-order', name: 'confirm_order', methods: ['POST'])]
    public function confirmOrder(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        // Calculer le total
        $cart = $session->get('cart', []);
        $subtotal = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $subtotal += $product->getPrice() * $quantity;
            }
        }

        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + 20 + $tax; // Subtotal + Shipping + Tax

        // Sauvegarder le total dans la session
        $session->set('total', $total);

        return $this->redirectToRoute('payment');
    }

    #[Route('/payment', name: 'payment')]
    public function payment(SessionInterface $session): Response
    {
        $total = $session->get('total');

        if ($total === null) {
            return $this->redirectToRoute('app_shopping_basket');
        }

        return $this->render('shopping/pay.html.twig', [
            'total' => number_format($total, 2)
        ]);
    }

    #[Route('/process-payment', name: 'process_payment', methods: ['POST'])]
    public function processPayment(Request $request, SessionInterface $session): Response
    {
        // Process the payment here
        // Clear the cart after successful payment
        $session->remove('cart');
        $session->remove('total');

        return $this->redirectToRoute('order_confirmation');
    }

    #[Route('/order-confirmation', name: 'order_confirmation')]
    public function orderConfirmation(): Response
    {
        return $this->render('shopping/order_confirmation.html.twig');
    }
}
