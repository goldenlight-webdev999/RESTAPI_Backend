<?php

declare(strict_types=1);


namespace App\Application\Command\Application\CreateApplication;


use App\Application\Command\CommandHandlerInterface;
use App\Domain\OAuth2\OAuth2Client;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;

final class CreateApplicationHandler implements CommandHandlerInterface
{
    private $clientManager;

    /**
     * CreateApplicationHandler constructor.
     * @param ClientManagerInterface $clientManager
     */
    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    public function handle(CreateApplicationCommand $command): void
    {
        /**
         * @var OAuth2Client $client
         */
        $client = $this->clientManager->createClient();

        $client->setUser($command->getUser()->getId());
        $client->setName($command->getName());
        $client->setAllowedGrantTypes($command->getGrantTypes());
        $client->setRedirectUris($command->getRedirectUris());

        /**
         * @var ClientInterface $client
         */
        $this->clientManager->updateClient($client);
    }
}