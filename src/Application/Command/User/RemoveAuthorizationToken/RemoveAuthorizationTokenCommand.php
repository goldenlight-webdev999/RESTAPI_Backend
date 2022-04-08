<?php

declare(strict_types=1);


namespace App\Application\Command\User\RemoveAuthorizationToken;


use Ramsey\Uuid\UuidInterface;

final class RemoveAuthorizationTokenCommand
{
    private $userId;
    private $token;

    public function __construct(
        UuidInterface $userId,
        string $token)
    {
        $this->userId = $userId;
        $this->token = $token;
    }

    /**
     * @return UuidInterface
     */
    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}