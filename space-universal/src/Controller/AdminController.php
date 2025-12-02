<?php

// src/Controller/AdminController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Review;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $totalUsers = $userRepository->count([]);
        $totalStock = $productRepository->createQueryBuilder('p')
            ->select('SUM(p.stock)')
            ->getQuery()
            ->getSingleScalarResult();
        $totalSales = $orderRepository->count([]);
        $totalRevenue = $orderRepository->createQueryBuilder('o')
            ->select('SUM(o.totalPrice)')
            ->getQuery()
            ->getSingleScalarResult();

        $revenueData = $orderRepository->createQueryBuilder('o')
            ->select("DATE_TRUNC('month', o.createdAt) as month, SUM(o.totalPrice) as revenue")
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();

        $productsByCategory = $categoryRepository->createQueryBuilder('c')
            ->select('c.name as category, COUNT(p.id) as count')
            ->leftJoin('c.products', 'p')
            ->groupBy('c.id, c.name')
            ->getQuery()
            ->getResult();

        $data = [
            'total_revenue' => $totalRevenue,
            'total_users' => $totalUsers,
            'products_in_stock' => $totalStock,
            'total_sales' => $totalSales,
            'revenue_data' => $revenueData,
            'products_by_category' => $productsByCategory,
        ];

        return $this->render('admin/dashboard.html.twig', $data);
    }

    #[Route('/admin/products', name: 'admin_products')]
    public function products(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/products.html.twig', ['products' => $products]);
    }

    #[Route('/admin/products/add', name: 'admin_product_add')]
    public function addProduct(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $product = new Product();
            $category = $categoryRepository->find($request->request->get('category'));
            $product->setCategory($category);
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice($request->request->get('price'));
            $product->setStock($request->request->get('stock'));
            $product->setOriginPlanet($request->request->get('origin_planet'));
            $product->setCreatedAt(new \DateTime());

            $imageFile = $request->files->get('image');
            if ($imageFile && $imageFile->isValid()) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $product->setImageUrl('img/products/' . $newFilename);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_products');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('admin/add_product.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/products/edit/{id}', name: 'admin_product_edit')]
    public function editProduct(Request $request, Product $product, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $category = $categoryRepository->find($request->request->get('category'));
            $product->setCategory($category);
            $product->setName($request->request->get('name'));
            $product->setDescription($request->request->get('description'));
            $product->setPrice($request->request->get('price'));
            $product->setStock($request->request->get('stock'));
            $product->setOriginPlanet($request->request->get('origin_planet'));

            $imageFile = $request->files->get('image');
            if ($imageFile && $imageFile->isValid()) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $product->setImageUrl('img/products/' . $newFilename);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_products');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('admin/edit_product.html.twig', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_products');
    }

    #[Route('/admin/orders', name: 'admin_orders')]
    public function orders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();

        return $this->render('admin/orders.html.twig', ['orders' => $orders]);
    }

    #[Route('/admin/orders/edit/{id}', name: 'admin_order_edit')]
    public function editOrderStatus(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $newStatus = $request->request->get('status');
            $order->setStatus($newStatus);
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('admin/edit_order.html.twig', ['order' => $order]);
    }

    #[Route('/admin/orders/delete/{id}', name: 'admin_order_delete', methods: ['POST'])]
    public function deleteOrder(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_orders');
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    #[Route('/admin/users/edit/{id}', name: 'admin_user_edit')]
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $email = $request->request->get('email');
            $isVerified = $request->request->get('is_verified') === '1';

            if ($username !== null) {
                $user->setUsername($username);
            }

            if ($email !== null) {
                $user->setEmail($email);
            }

            $user->setIsVerified($isVerified);

            if ($request->request->get('remove_avatar')) {
                $user->setAvatarUrl(null);
            } else {
                $avatarFile = $request->files->get('avatar');
                if ($avatarFile) {
                    $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                    try {
                        $avatarFile->move(
                            $this->getParameter('avatars_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $user->setAvatarUrl('img/avatars/' . $newFilename);
                }
            }

            $roles = $request->request->all('roles');
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/edit_user.html.twig', ['user' => $user]);
    }

    #[Route('/admin/users/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/reviews', name: 'admin_reviews')]
    public function reviews(ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findAll();

        return $this->render('admin/reviews.html.twig', ['reviews' => $reviews]);
    }

    #[Route('/admin/reviews/delete/{id}', name: 'admin_review_delete', methods: ['POST'])]
    public function deleteReview(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_reviews');
    }

    #[Route('/admin/categories', name: 'admin_categories')]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('admin/categories.html.twig', ['categories' => $categories]);
    }

    #[Route('/admin/categories/add', name: 'admin_category_add')]
    public function addCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $category = new Category();
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/add_category.html.twig');
    }

    #[Route('/admin/categories/edit/{id}', name: 'admin_category_edit')]
    public function editCategory(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/edit_category.html.twig', ['category' => $category]);
    }

    #[Route('/admin/categories/delete/{id}', name: 'admin_category_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_categories');
    }
}
