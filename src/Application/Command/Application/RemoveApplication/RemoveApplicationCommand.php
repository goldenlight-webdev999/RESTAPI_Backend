<?php

declare(strict_types=1);


namespace App\Application\Command\Application\RemoveApplication;


use App\Domain\OAuth2\OAuth2Client;

final class RemoveApplicationCommand
{
    private $application;

    /**
     * RemoveApplicationCommand constructor.
     * @param OAuth2Client $application
     */
    public function __construct(OAuth2Client $application)
    {
        $this->application = $application;
    }

    /**
     * @return OAuth2Client
     */
    public function getApplication(): OAuth2Client
    {
        return $this->application;
    }
}