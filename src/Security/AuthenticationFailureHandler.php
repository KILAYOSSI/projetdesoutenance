<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    private $router;
    private $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $errorMessage = $this->translator->trans('security.login.error', [], 'messages');
        
        // Personnaliser le message d'erreur selon le type d'erreur
        if (strpos($exception->getMessage(), 'Bad credentials') !== false) {
            $errorMessage = 'Email ou mot de passe incorrect. Veuillez vérifier vos identifiants.';
        } elseif (strpos($exception->getMessage(), 'Account is disabled') !== false) {
            $errorMessage = 'Votre compte est désactivé. Veuillez contacter l\'administrateur.';
        } elseif (strpos($exception->getMessage(), 'User is disabled') !== false) {
            $errorMessage = 'Votre compte n\'est pas encore activé. Veuillez vérifier votre email.';
        } else {
            $errorMessage = $exception->getMessage();
        }

        // Ajouter le message d'erreur dans la session
        $request->getSession()->getFlashBag()->add('error', $errorMessage);
        
        // Stocker le dernier email utilisé pour le réutiliser dans le formulaire
        $request->getSession()->set('_last_username', $request->request->get('email'));

        // Rediriger vers la page de login
        return new RedirectResponse($this->router->generate('app_login'));
    }
}

