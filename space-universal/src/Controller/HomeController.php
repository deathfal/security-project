<?php

// src/Controller/HomeController.php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\FeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\Collection;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ProductRepository $productRepository,
        FeedbackRepository $feedbackRepository
    ): Response {
        // Get all products and sort them by average rating
        $products = $productRepository->findAll();
        usort($products, function ($a, $b) {
            $aRating = $this->getAverageRating($a->getReviews());
            $bRating = $this->getAverageRating($b->getReviews());
            return $bRating <=> $aRating;
        });

        return $this->render('index.html.twig', [
            'products' => $products,
            'feedbacks' => $feedbackRepository->findBy([], ['createdAt' => 'DESC'], 6)
        ]);
    }

    private function getAverageRating(Collection $reviews): float
    {
        if ($reviews->isEmpty()) {
            return 0.0;
        }

        $total = 0;
        foreach ($reviews as $review) {
            $total += $review->getRating();
        }

        return $total / $reviews->count();
    }
}
