<?php

declare(strict_types=1);


namespace App\Application\ProcessManager\Subscription;


use App\Application\Command\Subscription\MarkSubscriptionAsExpired\MarkSubscriptionAsExpiredCommand;
use App\Application\Command\Subscription\MarkSubscriptionAsPaid\MarkSubscriptionAsPaidCommand;
use App\Application\ProcessManager\ProcessManagerInterface;
use App\Domain\Subscription\Events\UserSubscriptionExpiredEvent;
use App\Domain\Subscription\Events\UserSubscriptionPaidOutEvent;
use League\Tactician\CommandBus;

final class SubscriptionProcessManager implements ProcessManagerInterface
{
    private $commandBus;

    public function __construct(
        CommandBus $commandBus
    )
    {
        $this->commandBus = $commandBus;
    }


    public static function getSubscribedEvents()
    {
        return [
            UserSubscriptionExpiredEvent::getEventName() => 'onUserSubscriptionExpiredEvent',
            UserSubscriptionPaidOutEvent::getEventName() => 'onUserSubscriptionPaidOutEvent',
        ];
    }

    public function onUserSubscriptionExpiredEvent(UserSubscriptionExpiredEvent $event): void
    {
        $this->commandBus->handle(
            new MarkSubscriptionAsExpiredCommand($event->getUserId())
        );
    }

    public function onUserSubscriptionPaidOutEvent(UserSubscriptionPaidOutEvent $event): void
    {
        $this->commandBus->handle(
            new MarkSubscriptionAsPaidCommand(
                $event->getUserId(),
                $event->getDatePaid()
            )
        );
    }
}