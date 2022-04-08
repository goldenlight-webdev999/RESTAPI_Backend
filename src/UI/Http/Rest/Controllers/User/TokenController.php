<?php

declare(strict_types=1);


namespace App\UI\Http\Rest\Controllers\User;

use App\Application\Command\User\RemoveAuthorizationToken\RemoveAuthorizationTokenCommand;
use App\Domain\User\User;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class TokenController
{
    private const CURRENT_USER_ALIAS = 'me';

    /**
     * @Route ("/users/{userUuidRaw}/tokens/{accessTokenRaw}", methods={"DELETE"})
     * @param string $userUuidRaw
     * @return JsonResponse
     */
    public function delete(string $userUuidRaw, string $accessTokenRaw, Security $security, CommandBus $commandBus): JsonResponse
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $security->getUser();

        if ($userUuidRaw === self::CURRENT_USER_ALIAS) {
            $userUuidRaw = $currentUser->getId()->toString();
        }

        if (!Uuid::isValid($userUuidRaw)) {
            throw new \InvalidArgumentException();
        }

        $commandBus->handle(
            new RemoveAuthorizationTokenCommand(
                Uuid::fromString($userUuidRaw),
                $accessTokenRaw
            )
        );

        return new JsonResponse(
            [
                'errors' => false,
            ]
        );
    }
}