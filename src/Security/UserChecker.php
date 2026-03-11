<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof \App\Entity\User) {
            return;
        }

        // Vérifier si le compte est vérifié
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte n\'est pas encore activé. Veuillez vérifier votre adresse email pour activer votre compte.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof \App\Entity\User) {
            return;
        }
    }
}

