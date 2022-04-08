<?php

declare(strict_types=1);


namespace App\Infrastructure\Captcha;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

final class CaptchaAuthenticator implements SimpleFormAuthenticatorInterface
{
    private $encoder;
    private $requestStack;
    private $captchaProvider;

    /**
     * CaptchaAuthenticator constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param RequestStack $requestStack
     * @param CaptchaProvider $captchaProvider
     */
    public function __construct(UserPasswordEncoderInterface $encoder, RequestStack $requestStack, CaptchaProvider $captchaProvider)
    {
        $this->encoder = $encoder;
        $this->requestStack = $requestStack;
        $this->captchaProvider = $captchaProvider;
    }


    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if(!$userCaptchaResponse = $currentRequest->get('g-recaptcha-response')) {
            throw new CustomUserMessageAuthenticationException('Invalid captcha', array(), Response::HTTP_PRECONDITION_FAILED);
        }

        // I think you should check the google captcha first, before accessing to database and manipulate user credentials
        if(!$this->captchaProvider->isCaptchaSolutionValid($userCaptchaResponse ?? '') ) {
            throw new CustomUserMessageAuthenticationException('Invalid captcha', array(), Response::HTTP_PRECONDITION_FAILED);
        }

        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }

        $passwordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());

        if ($passwordValid) {
            return new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $providerKey,
                $user->getRoles()
            );
        }

        throw new CustomUserMessageAuthenticationException('Invalid username or password');
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

}