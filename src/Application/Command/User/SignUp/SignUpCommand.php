<?php

declare(strict_types=1);


namespace App\Application\Command\User\SignUp;

use DDD\Embeddable\EmailAddress;
use Ramsey\Uuid\UuidInterface;

final class SignUpCommand
{
    private $email;
    private $fullName;
    private $password;
    private $uuid;
    private $acceptsCommercialCommunications;

    /**
     * SignUpCommand constructor.
     * @param EmailAddress $email
     * @param string $fullName
     * @param string $password
     * @param UuidInterface $uuid
     */
    public function __construct(
        EmailAddress $email,
        string $fullName,
        string $password,
        UuidInterface $uuid,
        bool $acceptsCommercialCommunications
)
    {
        $this->email = $email;
        $this->fullName = $fullName;
        $this->password = $password;
        $this->uuid = $uuid;
        $this->acceptsCommercialCommunications = $acceptsCommercialCommunications;
    }

    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function isAcceptsCommercialCommunications(): bool
    {
        return $this->acceptsCommercialCommunications;
    }
}