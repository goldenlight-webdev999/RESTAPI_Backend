<?php

declare(strict_types=1);


namespace App\Application\Query\Application\GetUserApplicationsLifecycleTraffic;


use App\Domain\User\User;
use App\Domain\OAuth2\OAuth2Client;

final class GetUserApplicationsLifecycleTrafficQuery
{
    private $user;
    private $OAuth2Client;

    public function __construct(
        User $user,
        OAuth2Client $OAuth2Client
    )
    {
        $this->user = $user;
        $this->OAuth2Client = $OAuth2Client;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return OAuth2Client
     */
    public function getApplication(): OAuth2Client
    {
        return $this->OAuth2Client;
    }
}
