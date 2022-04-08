<?php

declare(strict_types=1);


namespace App\UI\Http\Security\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Templating\EngineInterface;

final class LoginController
{
    /**
     * @Route ("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, EngineInterface $rendererService): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUsername();

        $result =  $rendererService->render(
            '@security/login/login.html.twig',
            [
                'last_username' => $lastUserName,
                'error' => $error,
            ]
        );

        return new Response($result);
    }
}