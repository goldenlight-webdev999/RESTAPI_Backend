<?php

declare(strict_types=1);


namespace App\Infrastructure\EventBus;


use App\Domain\Event\Interfaces\EventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventBus
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchEvent(EventInterface $event): void
    {
        $this->eventDispatcher->dispatch(
            $event::getEventName(),
            $event
        );
    }
}