<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    protected static $defaultDescription = 'Create an admin user';

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
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Email admin', 'admin@kilysagri.com');
        $password = $io->askHidden('Mot de passe admin');
        $nom = $io->ask('Nom complet', 'Administrateur');

        // Check if user exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            // Add ROLE_ADMIN
            $roles = $existingUser->getRoles();
            if (!in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_ADMIN';
                $existingUser->setRoles(array_unique($roles));
            }

            // Create Admin entity
            $admin = $existingUser->getAdmin();
            if (!$admin) {
                $admin = new Admin();
                $admin->setUser($existingUser);
                $admin->setNiveau('super_admin');
                $admin->setDateNomination(new \DateTime());
                $admin->setEstActif(true);
                $admin->setPermissions(['manage_users', 'manage_products', 'manage_orders', 'view_analytics', 'manage_kyc']);
                $this->entityManager->persist($admin);
            }

            $this->entityManager->flush();
            $io->success('Utilisateur ' . $email . ' est maintenant admin!');
            return Command::SUCCESS;
        }

        // Create new user
        $user = new User();
        $user->setEmail($email);
        $user->setNomComplet($nom);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);

        // Create Admin entity
        $admin = new Admin();
        $admin->setUser($user);
        $admin->setNiveau('super_admin');
        $admin->setDateNomination(new \DateTime());
        $admin->setEstActif(true);
        $admin->setPermissions(['manage_users', 'manage_products', 'manage_orders', 'view_analytics', 'manage_kyc']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Compte admin créé avec succès!');
        $io->info('Email: ' . $email);
        $io->info('Vous pouvez maintenant vous connecter sur /login');

        return Command::SUCCESS;
    }
}

