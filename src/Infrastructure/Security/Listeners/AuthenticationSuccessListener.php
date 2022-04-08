<?php

declare(strict_types=1);


namespace App\Infrastructure\Security\Listeners;

use App\Application\Command\User\SynchronizeUserRoles\SynchronizeUserRolesCommand;
use App\Domain\User\User;
use League\Tactician\CommandBus;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class AuthenticationSuccessListener
{
    private $commandBus;

    /**
     * AuthenticationSuccessListener constructor.
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }


    public function onAuthenticationSuccess(InteractiveLoginEvent $event): void
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $event->getAuthenticationToken()->getUser();
        $this->commandBus->handle(
            new SynchronizeUserRolesCommand($currentUser->getId())
        );
    }
}