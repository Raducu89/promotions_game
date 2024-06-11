<?php

namespace App\Command;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:seed-users')]
class SeedUsersCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Seeds the users table with sample data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = [
            ['username' => 'user1', 'password' => 'password1', 'language' => 'en'],
            ['username' => 'user2', 'password' => 'password2', 'language' => 'en'],
            ['username' => 'user3', 'password' => 'password3', 'language' => 'de'],
            ['username' => 'user4', 'password' => 'password4', 'language' => 'de'],
            ['username' => 'user5', 'password' => 'password5', 'language' => 'en'],
        ];

        foreach ($users as $userData) {
            $user = new Users();
            $user->setUsername($userData['username']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);
            $user->setLanguage($userData['language']);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $output->writeln('Users seeded successfully.');

        return Command::SUCCESS;
    }
}
