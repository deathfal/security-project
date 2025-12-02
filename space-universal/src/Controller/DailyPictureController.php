<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DailyPictureController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/daily-picture', name: 'daily_picture')]
    public function index(): Response
    {
        $response = $this->client->request(
            'GET',
            'https://api.nasa.gov/planetary/apod?api_key=DEMO_KEY'
        );

        $image = $response->toArray();

        return $this->render('daily_picture.html.twig', [
            'image' => $image,
        ]);
    }
}
