<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PromoteUserCommand extends Command
{
    protected static $defaultName = 'app:promote-admin';
    protected static $defaultDescription = 'Promote a user to admin';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the user to promote');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error('User not found with email: ' . $email);
            return Command::FAILURE;
        }

        // Add ROLE_ADMIN
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles(array_unique($roles));
        }

        // Create Admin entity if not exists
        $admin = $user->getAdmin();
        if (!$admin) {
            $admin = new Admin();
            $admin->setUser($user);
            $admin->setNiveau('super_admin');
            $admin->setDateNomination(new \DateTime());
            $admin->setEstActif(true);
            $admin->setPermissions(['manage_users', 'manage_products', 'manage_orders', 'view_analytics', 'manage_kyc']);
            
            $this->entityManager->persist($admin);
        }

        $this->entityManager->flush();

        $io->success($email . ' has been promoted to admin!');

        return Command::SUCCESS;
    }
}

