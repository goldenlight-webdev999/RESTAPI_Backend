<?php

declare(strict_types=1);


namespace App\Application\Query\Subscription\GetUserLiveSubscriptions;


use App\Domain\User\User;

final class GetUserLiveSubscriptionsQuery
{
    private $user;

    /**
     * GetUserLiveSubscriptions constructor.
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