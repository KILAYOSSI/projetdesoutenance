<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateAdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Créer un admin automatiquement
     * URL: /create-admin
     */
    #[Route('/create-admin', name: 'create_admin')]
    public function createAdmin(): Response
    {
        $email = 'admin@kilysagri.com';
        $password = 'Admin123';
        $nom = 'Administrateur';

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            // Ajouter le rôle admin
            $roles = $existingUser->getRoles();
            if (!in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_ADMIN';
                $existingUser->setRoles(array_unique($roles));
            }

            // Créer Admin entity si pas existant
            if (!$existingUser->getAdmin()) {
                $admin = new Admin();
                $admin->setUser($existingUser);
                $admin->setNiveau('super_admin');
                $admin->setDateNomination(new \DateTime());
                $admin->setEstActif(true);
                $admin->setPermissions(['manage_users', 'manage_products', 'manage_orders', 'view_analytics', 'manage_kyc']);
                $this->entityManager->persist($admin);
            }

            $this->entityManager->flush();

            return new Response("
                <h1 style='text-align:center;margin-top:50px;'>
                <img src='https://via.placeholder.com/100x100/198754/ffffff?text=KilysAgri' style='border-radius:50%;'>
                <br><br>
                Utilisateur déjà existant !</h1>
                <p style='text-align:center;'>Email: $email</p>
                <p style='text-align:center;'><a href='/login' style='background:#198754;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;'>Se connecter</a></p>
            ");
        }

        // Créer nouvel utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setNomComplet($nom);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);

        // Créer Admin entity
        $admin = new Admin();
        $admin->setUser($user);
        $admin->setNiveau('super_admin');
        $admin->setDateNomination(new \DateTime());
        $admin->setEstActif(true);
        $admin->setPermissions(['manage_users', 'manage_products', 'manage_orders', 'view_analytics', 'manage_kyc']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        return new Response("
            <h1 style='text-align:center;margin-top:50px;'>
            <img src='https://via.placeholder.com/100x100/198754/ffffff?text=KilysAgri' style='border-radius:50%;'>
            <br><br>
            Compte admin créé avec succès !</h1>
            <p style='text-align:center;'>Email: $email</p>
            <p style='text-align:center;'>Mot de passe: $password</p>
            <p style='text-align:center;'><a href='/login' style='background:#198754;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;'>Se connecter</a></p>
        ");
    }
}

