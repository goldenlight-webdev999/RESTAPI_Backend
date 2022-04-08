<?php

declare(strict_types=1);


namespace App\Application\Command\User\RemoveAuthorizationToken;


use App\Application\Command\CommandHandlerInterface;
use App\Domain\User\User;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;

final class RemoveAuthorizationTokenHandler implements CommandHandlerInterface
{

    private $accessTokenManager;

    /**
     * RemoveAuthorizationTokenHandler constructor.
     */
    public function __construct(AccessTokenManagerInterface $accessTokenManager)
    {
        $this->accessTokenManager = $accessTokenManager;
    }

    public function handle(RemoveAuthorizationTokenCommand $command): void
    {
        $accessToken = $this->accessTokenManager->findTokenByToken($command->getToken());

        if (!$accessToken) {
            throw new \InvalidArgumentException('Access token not found');
        }

        /**
         * @var User $user
         */
        $user = $accessToken->getUser();
        if (!$user->getId()->equals($command->getUserId())) {
            throw new \RuntimeException('User id doesn\'t match with the current access token');
        }

        $this->accessTokenManager->deleteToken($accessToken);
    }
}