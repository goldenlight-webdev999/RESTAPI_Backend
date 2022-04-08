<?php

declare(strict_types=1);


namespace App\Application\Command\User\SignUp;


use App\Application\Command\CommandHandlerInterface;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Templating\EngineInterface;

final class SignUpHandler implements CommandHandlerInterface
{
    private $userRepository;
    private $userFactory;
    private $defaultSystemEmail;
    private $mailer;
    private $rendererService;
    private $appSecret;
    private $appUrl;

    /**
     * SignUpHandler constructor.
     * @param UserRepositoryInterface $userRepository
     * @param UserFactory $userFactory
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        UserFactory $userFactory,
        \Swift_Mailer $mailer,
        EngineInterface $rendererService,
        string $defaultSystemEmail,
        string $appUrl,
        string $appSecret
    )
    {
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
        $this->mailer = $mailer;
        $this->rendererService = $rendererService;
        $this->defaultSystemEmail = $defaultSystemEmail;
        $this->appUrl = $appUrl;
        $this->appSecret = $appSecret;
    }

    /**
     * @param SignUpCommand $command
     * @throws \Exception
     */
    public function handle(SignUpCommand $command): void
    {
        $user = $this->userFactory->createUser(
            $command->getEmail(),
            $command->getFullName(),
            $command->getUuid(),
            $command->getPassword(),
            $command->isAcceptsCommercialCommunications()
        );

        $this->userRepository->save($user);

        // we have a user lets send the email
        $body = $this->rendererService->render(
            '@mailer/user/welcome.html.twig',
            [
                'name' => $user->getUserName(),
                'verify_email_url' => $user->getEmailVerificationUrl($this->appUrl, $this->appSecret),
                'email' => (string)$user->getEmail(),
            ]
        );

        $message = (new \Swift_Message('Welcome to Metacleaner.com'))
            ->setTo((string)$user->getEmail())
            ->setFrom($this->defaultSystemEmail)
            ->setBody($body, 'text/html');

        $mailerResult = $this->mailer->send($message);


    }
}
