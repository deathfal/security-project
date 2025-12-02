<?php

namespace App\Command;

use App\Entity\Feedback;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-feedback',
    description: 'Test feedback creation',
)]
class TestFeedbackCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get the first user
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);

        if (!$user) {
            $io->error('No users found in database');
            return Command::FAILURE;
        }

        try {
            // Create a test feedback
            $feedback = new Feedback();
            $feedback->setUser($user);
            $feedback->setComment('Test feedback from command');

            $this->entityManager->persist($feedback);
            $this->entityManager->flush();

            $io->success('Feedback created successfully with ID: ' . $feedback->getId());

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error creating feedback: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
