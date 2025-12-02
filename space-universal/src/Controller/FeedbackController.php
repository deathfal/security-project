<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\User;
use App\Form\FeedbackType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/feedback')]
class FeedbackController extends AbstractController
{
    #[Route('/new', name: 'app_feedback_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $feedback->setUser($user);
            $entityManager->persist($feedback);
            $entityManager->flush();

            $this->addFlash('success', 'Thank you for sharing your feedback!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('feedback/new.html.twig', [
            'form' => $form,
        ]);
    }
}
