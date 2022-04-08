<?php

declare(strict_types=1);


namespace App\Application\Query\Application\GetUserApplications;


use App\Domain\User\User;

final class GetUserApplicationsQuery
{
    private $user;

    /**
     * GetUserApplicationsQuery constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}