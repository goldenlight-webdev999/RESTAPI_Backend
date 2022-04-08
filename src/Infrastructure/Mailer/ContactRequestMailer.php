<?php

declare(strict_types=1);


namespace App\Infrastructure\Mailer;


use DDD\Embeddable\EmailAddress;
use Symfony\Component\Templating\EngineInterface;

final class ContactRequestMailer
{
    private $defaultSystemEmail;
    private $mailer;
    private $rendererService;

    /**
     * ContactRequestMailer constructor.
     * @param string $defaultSystemEmail
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $rendererService
     */
    public function __construct(string $defaultSystemEmail, \Swift_Mailer $mailer, EngineInterface $rendererService)
    {
        $this->mailer = $mailer;
        $this->rendererService = $rendererService;
        $this->defaultSystemEmail = $defaultSystemEmail;
    }

    public function sendNewContactRequest(
        string $name,
        string $message,
        EmailAddress $emailAddress,
        string $phoneNumber
    ): void
    {
        $body = $this->rendererService->render(
            '@mailer/contact/contact_request_received.html.twig',
            [
                'name' => $name,
                'message' => $message,
                'email' => (string)$emailAddress,
                'phone_number' => $phoneNumber,
            ]
        );

        $message = new \Swift_Message('New contact request', $body, 'text/html');

        $message->setFrom((string)$emailAddress);
        $message->setTo($this->defaultSystemEmail);

        $this->mailer->send($message);
    }


}