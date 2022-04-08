<?php

declare(strict_types=1);


namespace App\Application\Command\ContactRequest\CreateContactRequest;


use DDD\Embeddable\EmailAddress;

final class CreateContactRequestCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EmailAddress
     */
    private $email;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $message;

    /**
     * @var bool
     */
    private $acceptCommercialCommunications;

    /**
     * CreateContactRequestCommand constructor.
     * @param string $name
     * @param EmailAddress $email
     * @param string $phoneNumber
     * @param string $message
     * @param bool $acceptCommercialCommunications
     */
    public function __construct(
        string $name,
        EmailAddress $email,
        string $phoneNumber,
        string $message,
        bool $acceptCommercialCommunications)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->acceptCommercialCommunications = $acceptCommercialCommunications;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return EmailAddress
     */
    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isAcceptCommercialCommunications(): bool
    {
        return $this->acceptCommercialCommunications;
    }
}