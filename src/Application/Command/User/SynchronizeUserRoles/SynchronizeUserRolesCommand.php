<?php

declare(strict_types=1);


namespace App\Application\Command\User\SynchronizeUserRoles;


use Ramsey\Uuid\UuidInterface;

final class SynchronizeUserRolesCommand
{
    private $userId;

    /**
     * SynchronizeUserRolesCommand constructor.
     * @param UuidInterface $userId
     */
    public function __construct(UuidInterface $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return UuidInterface
     */
    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }
}