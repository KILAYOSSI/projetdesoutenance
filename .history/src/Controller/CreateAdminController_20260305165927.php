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
     * Créer un admin - aller sur cette URL pour créer le compte admin
     * URL: /create-admin?email=admin@kilysagri.com&password=Password123
     */
    #[Route('/create-admin', name: 'create_admin')]
    public function createAdmin(\Symfony\Component\HttpFoundation\Request $request): Response
    {
        // Protéger cette route avec un code secret
        $secretCode = $request->query->get('code', '');
        
        if ($secretCode !== 'kilysagri2024') {
            return new Response('Code secret incorrect. Ajoutez ?code=kilysagri2024 à l\'URL');
        }

        $email = $request->query->get('email', 'admin@kilysagri.com');
        $password = $request->query->get('password', 'Password123');
        $nom = $request->query->get('nom', 'Administrateur');

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
                <h1>Utilisateur promu admin!</h1>
                <p>Email: $email</p>
                <p>Mot de passe: (inchangé)</p>
                <p><a href='/login'>Aller à la page de connexion</a></p>
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
            <h1>Compte admin créé!</h1>
            <p>Email: $email</p>
            <p>Mot de passe: $password</p>
            <p><a href='/login'>Aller à la page de connexion</a></p>
        ");
    }
}

