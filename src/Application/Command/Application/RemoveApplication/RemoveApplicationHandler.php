<?php

declare(strict_types=1);


namespace App\Application\Command\Application\RemoveApplication;


use App\Application\Command\CommandHandlerInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;

final class RemoveApplicationHandler implements CommandHandlerInterface
{
    private $clientManager;

    /**
     * RemoveApplicationHandler constructor.
     * @param ClientManagerInterface $clientManager
     */
    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    public function handle(RemoveApplicationCommand $command): void
    {
        $this->clientManager->deleteClient($command->getApplication());
    }
}