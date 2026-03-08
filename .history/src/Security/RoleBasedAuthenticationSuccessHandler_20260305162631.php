<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class RoleBasedAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();

        // Si l'utilisateur a le rôle ADMIN, rediriger vers le dashboard admin
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse(
                '/admin/dashboard'
            );
        }

        // Si l'utilisateur peut vendre (producteur validé), rediriger vers son dashboard
        if ($user->canSell()) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse(
                '/vendeur/dashboard'
            );
        }

        // Par défaut, rediriger vers la page des produits
        return new \Symfony\Component\HttpFoundation\RedirectResponse(
            '/produits'
        );
    }
}

