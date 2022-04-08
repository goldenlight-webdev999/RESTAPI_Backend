<?php

declare(strict_types=1);


namespace App\UI\Http\PublicRest\Controllers;

use App\Domain\User\Repository\UserRepositoryInterface;
use DDD\Embeddable\EmailAddress;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserController
{

    private $userRepository;
    private $defaultSystemEmail;
    private $mailer;
    private $rendererService;
    private $appUrl;
    private $appSecret;
    private $passwordEncoder;

    public function __construct(
        string $defaultSystemEmail,
        string $appUrl,
        string $appSecret,
        \Swift_Mailer $mailer,
        EngineInterface $rendererService,
        UserRepositoryInterface $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->mailer = $mailer;
        $this->rendererService = $rendererService;
        $this->defaultSystemEmail = $defaultSystemEmail;
        $this->appUrl = $appUrl;
        $this->appSecret = $appSecret;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @Route ("/sendEmailVerification", name="sendEmailVerification", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmailVerification(Request $request): JsonResponse
    {

        $content = json_decode($request->getContent(), true);

        $emailAddress = new EmailAddress($content['email']);

        $user = $this->userRepository->getByEmail($emailAddress);

        if ($user) {

            // we have a user lets send the email
            $body = $this->rendererService->render(
                '@mailer/user/welcome.html.twig',
                [
                    'name' => $user->getUserName(),
                    'verify_email_url' => $user->getEmailVerificationUrl($this->appUrl, $this->appSecret),
                    'email' => (string)$emailAddress,
                ]
            );

            $message = (new \Swift_Message('To continue using metacleaner.com you must verify your email'))
                ->setTo((string)$emailAddress)
                ->setFrom($this->defaultSystemEmail)
                ->setBody($body, 'text/html');

            $mailerResult = $this->mailer->send($message);

        }

        return new JsonResponse(
            'If there was an user with suplied email address, we have sent the verification email',
            200
        );
    }

    /**
     * @Route ("/validate/{token}/{expiry}/{uuid}", name="validateEmailVerification", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function validateEmailVerification(string $token, int $expiry, string $uuid, Request $request): JsonResponse
    {


        $verificationToken = hash_hmac('sha256', json_encode(['expiry' => $expiry, 'id' => $uuid]), $this->appSecret);
        try {
            if (!hash_equals($verificationToken, $token)) {
                throw new \InvalidArgumentException('The verification token is not valid');
            }

            if ($expiry < time()) {
                throw new \InvalidArgumentException('The verification token has expired');
            }

            $user = $this->userRepository->get(Uuid::fromString($uuid));
            if (!$user) {
                throw new \InvalidArgumentException('The verification token user is not valid');
             }

            $user->setDateVerified(new \DateTimeImmutable());
            $this->userRepository->save($user);

            return new JsonResponse(
                'Your email address was verified successfuly',
                200
            );

        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse([
                    'errors' => true,
                    'payload' => [
                        'token' => $exception->getMessage(),
                    ],
                ],
                400
            );
        }
    }

    /**
     * @Route ("/forgot_password", name="forgotPassword", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function sendForgotPassword(Request $request): JsonResponse
    {

        $content = json_decode($request->getContent(), true);

        $emailAddress = new EmailAddress($content['email']);

        $user = $this->userRepository->getByEmail($emailAddress);

        if ($user) {

            // we have a user lets send the email
            $body = $this->rendererService->render(
                '@mailer/user/forgot_password.html.twig',
                [
                    'name' => $user->getUserName(),
                    'reset_password_url' => $user->getResetPasswordUrl($this->appUrl, $this->appSecret),
                    'email' => (string)$emailAddress,
                ]
            );

            $message = (new \Swift_Message('Reset your password on Metacleaner.com'))
                ->setTo((string)$emailAddress)
                ->setFrom($this->defaultSystemEmail)
                ->setBody($body, 'text/html');

            $mailerResult = $this->mailer->send($message);

        }

        return new JsonResponse(
            'If there was an user with suplied email address, we have emaild you the password reset instructions!',
            200
        );
    }

    /**
     * @Route ("/forgot_password/{token}/{expiry}/{uuid}", name="validateForgotPassword", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function validateForgotPassword(string $token, int $expiry, string $uuid, Request $request): JsonResponse
    {

        $verificationToken = hash_hmac('sha256', json_encode(['expiry' => $expiry, 'id' => $uuid]), $this->appSecret);

        try {
            if (!hash_equals($verificationToken, $token)) {
                throw new \InvalidArgumentException('The verification token is not valid');
            }

            if ($expiry < time()) {
                throw new \InvalidArgumentException('The verification token has expired');
            }

            $user = $this->userRepository->get(Uuid::fromString($uuid));
            if (!$user) {
                throw new \InvalidArgumentException('The verification token user is not valid');
             }

            $content = json_decode($request->getContent(), true);

            if (empty($content['password'])) {
                throw new \InvalidArgumentException('You didn\'t enter a password');
            }

            $encodedPassword = $this->passwordEncoder->encodePassword($user, $content['password']);
            $user->setPassword($encodedPassword);
            $this->userRepository->save($user);
            return new JsonResponse(
                'Your password was changed successfuly',
                200
            );
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse([
                    'errors' => true,
                    'payload' => [
                        'token' => $exception->getMessage(),
                    ],
                ],
                400
            );
        }
    }
}
