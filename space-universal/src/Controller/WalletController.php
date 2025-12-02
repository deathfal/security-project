<?php

namespace App\Controller;

use App\Entity\PaymentMethod;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/wallet')]
class WalletController extends AbstractController
{
    private function addDecimals(string $num1, string $num2, int $scale = 8): string
    {
        // Convert to cents/smallest unit to avoid floating point issues
        $multiplier = pow(10, $scale);
        $num1Int = intval(strval(floatval($num1) * $multiplier));
        $num2Int = intval(strval(floatval($num2) * $multiplier));

        // Add as integers
        $sum = $num1Int + $num2Int;

        // Convert back to decimal string
        return number_format($sum / $multiplier, $scale, '.', '');
    }

    public static function getUserWallets($user, WalletRepository $walletRepository): array
    {
        if (!$user) {
            return [];
        }
        return $walletRepository->findBy(['owner' => $user]);
    }

    #[Route('/', name: 'app_wallet_index')]
    public function index(WalletRepository $walletRepository): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to view wallets');
            return $this->redirectToRoute('app_login');
        }

        try {
            $wallets = $user->getWallets();
            $paymentMethods = $user->getPaymentMethods();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error retrieving wallet information');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('wallet/index.html.twig', [
            'wallets' => $wallets,
            'payment_methods' => $paymentMethods,
        ]);
    }

    #[Route('/buy', name: 'app_wallet_buy', methods: ['POST'])]
    public function buy(Request $request, EntityManagerInterface $entityManager, WalletRepository $walletRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
            }
            return $this->redirectToRoute('app_login');
        }

        $amount = $request->request->get('amount');
        $currency = $request->request->get('currency');
        $paymentMethodId = $request->request->get('payment_method');

        if (!$amount || !$currency || !$paymentMethodId) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['error' => 'All fields are required'], Response::HTTP_BAD_REQUEST);
            }
            $this->addFlash('error', 'All fields are required');
            return $this->redirectToRoute('app_wallet_index');
        }

        try {
            // Validate amount is a valid number
            if (!is_numeric($amount) || floatval($amount) <= 0) {
                throw new \InvalidArgumentException('Invalid amount');
            }

            // Get payment method
            $paymentMethod = $entityManager->getRepository(PaymentMethod::class)->find($paymentMethodId);
            if (!$paymentMethod || $paymentMethod->getOwner() !== $user) {
                throw new \InvalidArgumentException('Invalid payment method');
            }

            // Get or create wallet for the currency
            $wallet = $walletRepository->findOrCreateWallet($currency, $user);

            // Add the amount to the current balance
            $newBalance = $this->addDecimals($wallet->getBalance(), $amount);
            $wallet->setBalance($newBalance);

            $entityManager->flush();

            $successMessage = sprintf('Successfully added %s %s to your wallet', $amount, $currency);

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => $successMessage,
                    'newBalance' => $newBalance,
                    'currency' => $currency
                ]);
            }

            $this->addFlash('success', $successMessage);
            return $this->redirectToRoute('app_wallet_index');
        } catch (\Exception $e) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_wallet_index');
        }
    }

    #[Route('/set-display-currency', name: 'app_wallet_set_display_currency', methods: ['POST'])]
    public function setDisplayCurrency(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $currency = $data['currency'] ?? null;

        if (!$currency) {
            return $this->json(['error' => 'Currency is required'], Response::HTTP_BAD_REQUEST);
        }

        // Store the selected currency in the session
        $request->getSession()->set('display_currency', $currency);

        return $this->json(['success' => true]);
    }
}
