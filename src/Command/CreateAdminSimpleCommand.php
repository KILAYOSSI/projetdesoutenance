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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminSimpleCommand extends Command
{
    protected static $defaultName = 'app:create-admin-simple';
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Créer un admin facilement')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe')
            ->addArgument('nom', InputArgument::OPTIONAL, 'Nom', 'Administrateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $nom = $input->getArgument('nom');

        $user = new User();
        $user->setEmail($email);
        $user->setNomComplet($nom);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);

        $admin = new Admin();
        $admin->setUser($user);
        $admin->setNiveau('super_admin');
        $admin->setDateNomination(new \DateTime());
        $admin->setEstActif(true);
        $admin->setPermissions(['manage_users', 'manage_products', 'manage_orders', 'view_analytics', 'manage_kyc']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Compte admin créé!');
        $io->info('Email: ' . $email);
        $io->info('Allez sur /login pour vous connecter');

        return Command::SUCCESS;
    }
}

