<?php

declare(strict_types=1);


namespace App\Application\Query\Subscription\GetCurrentLifecycleTraffic;


use App\Domain\User\User;

final class GetCurrentLifecycleTrafficQuery
{
    private $user;

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