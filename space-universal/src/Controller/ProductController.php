<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $minPrice = $request->query->get('min_price', '0');
        $maxPrice = $request->query->get('max_price', '1000');
        $categories = $request->query->all('categories');

        $queryBuilder = $productRepository->createQueryBuilder('p')
            ->where('p.price >= :minPrice')
            ->andWhere('p.price <= :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice);

        if (!empty($categories)) {
            $queryBuilder->andWhere('p.category IN (:categories)')
                ->setParameter('categories', $categories);
        }

        $products = $queryBuilder->getQuery()->getResult();

        return $this->render('products/index.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_detail')]
    public function detail(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('products/detail.html.twig', [
            'product' => $product,
        ]);
    }
}
